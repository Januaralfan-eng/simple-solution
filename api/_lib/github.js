// Thin GitHub Contents API client for reading + writing files in the repo.
// All edits create a commit; Vercel auto-redeploys on push to the watched branch.

function env(name, fallback) {
    const v = process.env[name];
    if (v !== undefined && v !== '') return v;
    if (fallback !== undefined) return fallback;
    throw new Error(`${name} env var belum di-set`);
}

function repo()   { return env('GITHUB_REPO',   'Januaralfan-eng/simple-solution'); }
function branch() { return env('GITHUB_BRANCH', 'main'); }
function token()  { return env('GITHUB_TOKEN'); }

function apiBase() { return `https://api.github.com/repos/${repo()}`; }

const COMMON_HEADERS = () => ({
    'Authorization': `Bearer ${token()}`,
    'Accept':        'application/vnd.github+json',
    'X-GitHub-Api-Version': '2022-11-28',
    'User-Agent':    'simple-solution-cloud-admin',
});

// Read a file. Returns { content: string (utf8), sha: string }.
export async function readFile(filePath) {
    const url = `${apiBase()}/contents/${encodeURIComponent(filePath).replace(/%2F/g, '/')}?ref=${encodeURIComponent(branch())}`;
    const r = await fetch(url, { headers: COMMON_HEADERS() });
    if (r.status === 404) return { content: null, sha: null };
    if (!r.ok) throw new Error(`GitHub readFile ${filePath} → ${r.status} ${await r.text()}`);
    const data = await r.json();
    const buf = Buffer.from(data.content, 'base64');
    return { content: buf.toString('utf8'), sha: data.sha };
}

// Commit a file (create or update). Auto-retries once on SHA mismatch (409).
// content: string (UTF-8 text) or Buffer (binary). Use opts.rawBase64 to skip encoding.
export async function writeFile(filePath, content, message, opts = {}) {
    const url = `${apiBase()}/contents/${encodeURIComponent(filePath).replace(/%2F/g, '/')}`;

    let sha = opts.sha;
    if (sha === undefined) {
        const existing = await readFile(filePath);
        sha = existing.sha;
    }

    let base64Content;
    if (opts.rawBase64) {
        base64Content = String(opts.rawBase64);
    } else if (Buffer.isBuffer(content)) {
        base64Content = content.toString('base64');
    } else {
        base64Content = Buffer.from(String(content), 'utf8').toString('base64');
    }

    const body = {
        message,
        content: base64Content,
        branch:  branch(),
        ...(sha ? { sha } : {}),
        committer: opts.committer,
        author:    opts.author,
    };

    const r = await fetch(url, {
        method: 'PUT',
        headers: { ...COMMON_HEADERS(), 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
    });

    if (r.status === 409 || r.status === 422) {
        // SHA conflict — fetch latest and retry once
        const latest = await readFile(filePath);
        if (latest.sha !== sha) {
            return writeFile(filePath, content, message, { ...opts, sha: latest.sha, _retried: true });
        }
    }

    if (!r.ok) throw new Error(`GitHub writeFile ${filePath} → ${r.status} ${await r.text()}`);
    return await r.json();
}

export function getBranch() { return branch(); }
export function getRepo()   { return repo();   }
