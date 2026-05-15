// Minimal static HTTP server + local admin editor with auth.
// Public routes:    /, /mobile, preview files, /api/auth-status, /api/login, /api/logout
// Protected routes: /admin, /api/content, /api/schema, /api/save, /api/change-credentials
//
// First-run: admin-config.json is auto-created with default creds admin / admin
// (file is gitignored). Change them via the "Pengaturan Akun" form inside the admin panel.

import http   from 'node:http';
import fs     from 'node:fs';
import fsp    from 'node:fs/promises';
import path   from 'node:path';
import crypto from 'node:crypto';
import { spawn } from 'node:child_process';
import { promisify } from 'node:util';
import { fileURLToPath } from 'node:url';
import { load as loadCheerio } from 'cheerio';

const scrypt    = promisify(crypto.scrypt);
const __dirname = path.dirname(fileURLToPath(import.meta.url));

const PORT          = process.env.PORT || 3000;
const ROOT          = __dirname;
const PREVIEW_FILE  = path.join(ROOT, 'preview-home.html');
const SCHEMA_FILE   = path.join(ROOT, 'admin-schema.json');
const CONFIG_FILE   = path.join(ROOT, 'admin-config.json');

const SESSION_TTL_MS  = 1000 * 60 * 60 * 24 * 7; // 7 days
const COOKIE_NAME     = 'admin_session';

const MIME = {
    '.html':  'text/html; charset=utf-8',
    '.js':    'application/javascript; charset=utf-8',
    '.css':   'text/css; charset=utf-8',
    '.json':  'application/json; charset=utf-8',
    '.svg':   'image/svg+xml',
    '.png':   'image/png', '.jpg': 'image/jpeg', '.jpeg': 'image/jpeg',
    '.gif':   'image/gif', '.webp': 'image/webp', '.ico': 'image/x-icon',
    '.txt':   'text/plain; charset=utf-8',
    '.xml':   'application/xml; charset=utf-8',
    '.woff':  'font/woff', '.woff2': 'font/woff2',
};

const REWRITES = {
    '/':       '/preview-home.html',
    '/mobile': '/preview-mobile.html',
    '/admin':  '/admin.html',
};

// ────────────────────────────────────────────────────────────────────────
// Auth: password hashing (scrypt) + in-memory session store
// ────────────────────────────────────────────────────────────────────────

async function hashPassword(password, saltHex) {
    const salt = saltHex || crypto.randomBytes(16).toString('hex');
    const hash = (await scrypt(String(password), salt, 64)).toString('hex');
    return { hash, salt };
}

async function verifyPassword(password, salt, expectedHash) {
    const { hash } = await hashPassword(password, salt);
    const a = Buffer.from(hash, 'hex');
    const b = Buffer.from(expectedHash, 'hex');
    return a.length === b.length && crypto.timingSafeEqual(a, b);
}

async function loadConfig() {
    try {
        const raw = await fsp.readFile(CONFIG_FILE, 'utf8');
        return JSON.parse(raw);
    } catch (err) {
        if (err.code === 'ENOENT') {
            // First run: create default config (admin / admin)
            const { hash, salt } = await hashPassword('admin');
            const config = { username: 'admin', passwordHash: hash, salt };
            await fsp.writeFile(CONFIG_FILE, JSON.stringify(config, null, 2), 'utf8');
            console.log('');
            console.log('  ⚠  admin-config.json belum ada — dibuat dengan kredensial default:');
            console.log('     username: admin');
            console.log('     password: admin');
            console.log('  → SEGERA ganti via /admin → "Pengaturan Akun".');
            console.log('');
            return config;
        }
        throw err;
    }
}

async function saveConfig(config) {
    await fsp.writeFile(CONFIG_FILE, JSON.stringify(config, null, 2), 'utf8');
}

// sessionId → { username, createdAt }
const sessions = new Map();

function createSession(username) {
    const id = crypto.randomBytes(32).toString('hex');
    sessions.set(id, { username, createdAt: Date.now() });
    return id;
}

function destroySession(id) { sessions.delete(id); }

function parseCookies(header) {
    const out = {};
    if (!header) return out;
    for (const part of header.split(/;\s*/)) {
        const i = part.indexOf('=');
        if (i < 0) continue;
        out[part.slice(0, i).trim()] = decodeURIComponent(part.slice(i + 1));
    }
    return out;
}

function getSession(req) {
    const cookies = parseCookies(req.headers.cookie || '');
    const id = cookies[COOKIE_NAME];
    if (!id) return null;
    const s = sessions.get(id);
    if (!s) return null;
    if (Date.now() - s.createdAt > SESSION_TTL_MS) { sessions.delete(id); return null; }
    return { id, ...s };
}

function setSessionCookie(res, sessionId) {
    res.setHeader('Set-Cookie',
        `${COOKIE_NAME}=${sessionId}; HttpOnly; Path=/; Max-Age=${Math.floor(SESSION_TTL_MS / 1000)}; SameSite=Lax`);
}

function clearSessionCookie(res) {
    res.setHeader('Set-Cookie', `${COOKIE_NAME}=; HttpOnly; Path=/; Max-Age=0; SameSite=Lax`);
}

// ────────────────────────────────────────────────────────────────────────
// HTTP helpers
// ────────────────────────────────────────────────────────────────────────

function sendJson(res, code, data, extraHeaders = {}) {
    res.writeHead(code, { 'Content-Type': 'application/json; charset=utf-8', 'Cache-Control': 'no-store', ...extraHeaders });
    res.end(JSON.stringify(data));
}

async function readBody(req, limit = 2 * 1024 * 1024) {
    return new Promise((resolve, reject) => {
        let total = 0;
        const chunks = [];
        req.on('data', c => {
            total += c.length;
            if (total > limit) { req.destroy(); reject(new Error('Payload too large')); return; }
            chunks.push(c);
        });
        req.on('end',   () => resolve(Buffer.concat(chunks).toString('utf8')));
        req.on('error', reject);
    });
}

async function readJsonBody(req) {
    const body = await readBody(req);
    if (!body) return {};
    try { return JSON.parse(body); }
    catch { throw new Error('Body bukan JSON valid'); }
}

// ────────────────────────────────────────────────────────────────────────
// Schema / preview file helpers
// ────────────────────────────────────────────────────────────────────────

async function loadSchema()  { return JSON.parse(await fsp.readFile(SCHEMA_FILE, 'utf8')); }
async function loadPreview() { return fsp.readFile(PREVIEW_FILE, 'utf8'); }
async function savePreview(html) { await fsp.writeFile(PREVIEW_FILE, html, 'utf8'); }

// ────────────────────────────────────────────────────────────────────────
// Auth endpoints
// ────────────────────────────────────────────────────────────────────────

async function handleAuthStatus(req, res) {
    const s = getSession(req);
    sendJson(res, 200, { authenticated: !!s, username: s ? s.username : null });
}

async function handleLogin(req, res) {
    try {
        const { username, password } = await readJsonBody(req);
        if (!username || !password) { sendJson(res, 400, { error: 'Username dan password wajib diisi' }); return; }

        const config = await loadConfig();
        if (String(username).trim() !== config.username) {
            sendJson(res, 401, { error: 'Username atau password salah' }); return;
        }
        const ok = await verifyPassword(password, config.salt, config.passwordHash);
        if (!ok) { sendJson(res, 401, { error: 'Username atau password salah' }); return; }

        const id = createSession(config.username);
        setSessionCookie(res, id);
        sendJson(res, 200, { ok: true, username: config.username });
    } catch (err) {
        sendJson(res, 500, { error: err.message });
    }
}

function handleLogout(req, res) {
    const s = getSession(req);
    if (s) destroySession(s.id);
    clearSessionCookie(res);
    sendJson(res, 200, { ok: true });
}

async function handleChangeCredentials(req, res) {
    const s = getSession(req);
    if (!s) { sendJson(res, 401, { error: 'Belum login' }); return; }

    try {
        const { currentPassword, newUsername, newPassword } = await readJsonBody(req);
        if (!currentPassword) { sendJson(res, 400, { error: 'Password lama wajib diisi' }); return; }
        if (!newUsername && !newPassword) { sendJson(res, 400, { error: 'Tidak ada yang diubah' }); return; }

        const config = await loadConfig();
        const ok = await verifyPassword(currentPassword, config.salt, config.passwordHash);
        if (!ok) { sendJson(res, 401, { error: 'Password lama salah' }); return; }

        if (newUsername) config.username = String(newUsername).trim();
        if (newPassword) {
            if (String(newPassword).length < 4) { sendJson(res, 400, { error: 'Password minimal 4 karakter' }); return; }
            const { hash, salt } = await hashPassword(newPassword);
            config.passwordHash = hash;
            config.salt         = salt;
            // Invalidate other sessions when password changes
            for (const [sid, sess] of sessions) {
                if (sid !== s.id && sess.username === config.username) sessions.delete(sid);
            }
        }
        await saveConfig(config);

        // Update current session's username if changed
        const sess = sessions.get(s.id);
        if (sess) sess.username = config.username;

        sendJson(res, 200, { ok: true, username: config.username });
    } catch (err) {
        sendJson(res, 500, { error: err.message });
    }
}

// ────────────────────────────────────────────────────────────────────────
// Content endpoints (protected)
// ────────────────────────────────────────────────────────────────────────

async function handleGetContent(req, res) {
    if (!getSession(req)) { sendJson(res, 401, { error: 'Belum login' }); return; }
    try {
        const [schema, html] = await Promise.all([loadSchema(), loadPreview()]);
        const $ = loadCheerio(html, { decodeEntities: false });
        const result = { sections: [] };

        for (const section of schema.sections) {
            const out = { id: section.id, title: section.title, fields: [] };
            for (const f of section.fields) {
                const $el = $(f.selector);
                let value = '';
                if ($el.length === 0)        value = '';
                else if (f.html)             value = $el.html() ?? '';
                else if (f.textOnly)         value = $el.contents().filter((_, n) => n.type === 'text').text();
                else                         value = $el.text();
                out.fields.push({
                    key: f.key, label: f.label, type: f.type, html: !!f.html, textOnly: !!f.textOnly,
                    found: $el.length > 0, selector: f.selector, value: value.trim(),
                });
            }
            result.sections.push(out);
        }
        sendJson(res, 200, result);
    } catch (err) {
        console.error('[/api/content]', err);
        sendJson(res, 500, { error: err.message });
    }
}

async function handlePostSave(req, res) {
    if (!getSession(req)) { sendJson(res, 401, { error: 'Belum login' }); return; }
    try {
        const data = await readJsonBody(req);
        if (!data || typeof data.changes !== 'object') {
            sendJson(res, 400, { error: 'Body harus { changes: { key: value } }' }); return;
        }

        const [schema, html] = await Promise.all([loadSchema(), loadPreview()]);
        const $ = loadCheerio(html, { decodeEntities: false });

        const fieldMap = new Map();
        for (const section of schema.sections) for (const f of section.fields) fieldMap.set(f.key, f);

        const applied = [], skipped = [];

        for (const [key, newValue] of Object.entries(data.changes)) {
            const f = fieldMap.get(key);
            if (!f) { skipped.push({ key, reason: 'unknown key' }); continue; }
            const $el = $(f.selector);
            if ($el.length === 0) { skipped.push({ key, reason: 'selector matched 0 elements' }); continue; }
            if ($el.length > 1)   { skipped.push({ key, reason: `selector matched ${$el.length} elements (must be unique)` }); continue; }

            if (f.html) {
                $el.html(String(newValue));
            } else if (f.textOnly) {
                const directTextNodes = $el.contents().filter((_, n) => n.type === 'text').toArray();
                if (directTextNodes.length === 0) {
                    $el.prepend(' ' + String(newValue) + ' ');
                } else {
                    directTextNodes[0].data = ' ' + String(newValue) + ' ';
                    for (let i = 1; i < directTextNodes.length; i++) directTextNodes[i].data = '';
                }
            } else {
                $el.text(String(newValue));
            }
            applied.push(key);
        }

        await savePreview($.html());
        sendJson(res, 200, { ok: true, applied, skipped });
    } catch (err) {
        console.error('[/api/save]', err);
        sendJson(res, 500, { error: err.message });
    }
}

async function handleGetSchema(req, res) {
    if (!getSession(req)) { sendJson(res, 401, { error: 'Belum login' }); return; }
    try { sendJson(res, 200, await loadSchema()); }
    catch (err) { sendJson(res, 500, { error: err.message }); }
}

// ────────────────────────────────────────────────────────────────────────
// Publish endpoint: git add + commit + push, then vercel deploy --prod
// ────────────────────────────────────────────────────────────────────────

function runCmd(cmd, args, { useShell = false, timeoutMs = 180_000 } = {}) {
    return new Promise((resolve) => {
        const proc = spawn(cmd, args, { cwd: ROOT, shell: useShell, windowsHide: true });
        let stdout = '', stderr = '';
        const timer = setTimeout(() => { proc.kill(); }, timeoutMs);
        proc.stdout?.on('data', d => { stdout += d.toString(); });
        proc.stderr?.on('data', d => { stderr += d.toString(); });
        proc.on('close', code => {
            clearTimeout(timer);
            resolve({ ok: code === 0, code, stdout: stdout.slice(-4000), stderr: stderr.slice(-4000) });
        });
        proc.on('error', err => {
            clearTimeout(timer);
            resolve({ ok: false, code: -1, stdout, stderr: err.message });
        });
    });
}

async function handlePublish(req, res) {
    if (!getSession(req)) { sendJson(res, 401, { error: 'Belum login' }); return; }

    let body = {};
    try { body = await readJsonBody(req); } catch {}
    const message = (body.message && String(body.message).trim()) ||
        `Update content via admin — ${new Date().toISOString()}`;

    const steps = [];

    // 1) git add preview-home.html
    let r = await runCmd('git', ['add', 'preview-home.html']);
    steps.push({ name: 'git add', ok: r.ok, code: r.code, stdout: r.stdout, stderr: r.stderr });
    if (!r.ok) { sendJson(res, 500, { ok: false, steps, error: 'git add gagal' }); return; }

    // 2) check if there are staged changes
    r = await runCmd('git', ['diff', '--cached', '--quiet']);
    const hasChanges = r.code !== 0;
    steps.push({ name: 'git status', ok: true, note: hasChanges ? 'ada perubahan untuk commit' : 'tidak ada perubahan (deploy ulang saja)' });

    if (hasChanges) {
        // 3) git commit
        r = await runCmd('git', ['commit', '-m', message]);
        steps.push({ name: 'git commit', ok: r.ok, code: r.code, stdout: r.stdout, stderr: r.stderr });
        if (!r.ok) { sendJson(res, 500, { ok: false, steps, error: 'git commit gagal' }); return; }

        // 4) git push
        r = await runCmd('git', ['push']);
        steps.push({ name: 'git push', ok: r.ok, code: r.code, stdout: r.stdout, stderr: r.stderr });
        if (!r.ok) { sendJson(res, 500, { ok: false, steps, error: 'git push gagal — cek koneksi / kredensial' }); return; }
    }

    // 5) vercel deploy --prod (shell needed on Windows for .cmd)
    r = await runCmd('vercel', ['deploy', '--prod', '--yes'], { useShell: true });
    steps.push({ name: 'vercel deploy', ok: r.ok, code: r.code, stdout: r.stdout, stderr: r.stderr });
    if (!r.ok) { sendJson(res, 500, { ok: false, steps, error: 'vercel deploy gagal' }); return; }

    // Parse URLs from vercel output
    const combined = r.stdout + '\n' + r.stderr;
    const aliasMatch     = combined.match(/Aliased:\s+(https?:\/\/[^\s]+\.vercel\.app)/);
    const productionMatch= combined.match(/Production:\s+(https?:\/\/[^\s]+\.vercel\.app)/);

    sendJson(res, 200, {
        ok: true,
        steps,
        productionUrl: productionMatch?.[1] || null,
        publicUrl:     aliasMatch?.[1]      || null,
        committed:     hasChanges,
        message:       hasChanges ? message : null,
    });
}

// ────────────────────────────────────────────────────────────────────────
// Request router
// ────────────────────────────────────────────────────────────────────────

const server = http.createServer(async (req, res) => {
    const urlObj   = new URL(req.url, `http://${req.headers.host || 'localhost'}`);
    const pathname = urlObj.pathname;
    const method   = req.method;

    try {
        // Auth API
        if (method === 'GET'  && pathname === '/api/auth-status')         return handleAuthStatus(req, res);
        if (method === 'POST' && pathname === '/api/login')               return handleLogin(req, res);
        if (method === 'POST' && pathname === '/api/logout')              return handleLogout(req, res);
        if (method === 'POST' && pathname === '/api/change-credentials')  return handleChangeCredentials(req, res);

        // Content API (protected)
        if (method === 'GET'  && pathname === '/api/content') return handleGetContent(req, res);
        if (method === 'GET'  && pathname === '/api/schema')  return handleGetSchema(req, res);
        if (method === 'POST' && pathname === '/api/save')    return handlePostSave(req, res);

        // Static + rewrites
        if (method !== 'GET' && method !== 'HEAD') { res.writeHead(405); res.end('Method Not Allowed'); return; }

        let url = decodeURI(pathname);
        if (REWRITES[url]) url = REWRITES[url];

        const filePath = path.normalize(path.join(ROOT, url));
        if (!filePath.startsWith(ROOT)) { res.writeHead(403); res.end('Forbidden'); return; }

        fs.stat(filePath, (err, stat) => {
            if (err || !stat.isFile()) {
                res.writeHead(404, { 'Content-Type': 'text/plain; charset=utf-8' });
                res.end('404 — Not Found'); return;
            }
            const ext = path.extname(filePath).toLowerCase();
            res.writeHead(200, {
                'Content-Type':  MIME[ext] || 'application/octet-stream',
                'Cache-Control': 'no-store',
            });
            fs.createReadStream(filePath).pipe(res);
        });
    } catch (err) {
        console.error('[router]', err);
        sendJson(res, 500, { error: err.message });
    }
});

// Auto-create admin-config.json on startup so the first login works
await loadConfig();

server.listen(PORT, () => {
    console.log(`[server] listening on port ${PORT}`);
    console.log(`         /        → preview-home.html (publik)`);
    console.log(`         /mobile  → preview-mobile.html (publik)`);
    console.log(`         /admin   → admin.html (perlu login)`);
});
