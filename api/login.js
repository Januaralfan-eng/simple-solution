import { verifyPassword, signSession, setSessionCookie } from './_lib/auth.js';
import { loadAdminConfig } from './_lib/config.js';

export default async function handler(req, res) {
    if (req.method !== 'POST') { res.status(405).json({ error: 'Method not allowed' }); return; }
    try {
        const { username, password } = req.body || {};
        if (!username || !password) {
            res.status(400).json({ error: 'Username dan password wajib diisi' });
            return;
        }
        const config = await loadAdminConfig();
        if (String(username).trim() !== config.username) {
            res.status(401).json({ error: 'Username atau password salah' }); return;
        }
        const ok = await verifyPassword(password, config.salt, config.passwordHash);
        if (!ok) {
            res.status(401).json({ error: 'Username atau password salah' }); return;
        }
        const token = signSession(config.username);
        setSessionCookie(res, token);
        res.status(200).json({ ok: true, username: config.username });
    } catch (err) {
        console.error('[login]', err);
        res.status(500).json({ error: err.message });
    }
}
