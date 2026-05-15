import { requireAuth, hashPassword, verifyPassword, signSession, setSessionCookie } from './_lib/auth.js';
import { loadAdminConfig, saveAdminConfig } from './_lib/config.js';

export default async function handler(req, res) {
    if (req.method !== 'POST') { res.status(405).json({ error: 'Method not allowed' }); return; }
    const session = requireAuth(req, res);
    if (!session) return;

    try {
        const { currentPassword, newUsername, newPassword } = req.body || {};
        if (!currentPassword) { res.status(400).json({ error: 'Password lama wajib diisi' }); return; }
        if (!newUsername && !newPassword) { res.status(400).json({ error: 'Tidak ada yang diubah' }); return; }

        const config = await loadAdminConfig();
        const ok = await verifyPassword(currentPassword, config.salt, config.passwordHash);
        if (!ok) { res.status(401).json({ error: 'Password lama salah' }); return; }

        let usernameChanged = false, passwordChanged = false;

        if (newUsername && String(newUsername).trim() && String(newUsername).trim() !== config.username) {
            config.username = String(newUsername).trim();
            usernameChanged = true;
        }
        if (newPassword) {
            if (String(newPassword).length < 4) { res.status(400).json({ error: 'Password minimal 4 karakter' }); return; }
            const { hash, salt } = await hashPassword(newPassword);
            config.passwordHash = hash;
            config.salt         = salt;
            passwordChanged = true;
        }

        if (!usernameChanged && !passwordChanged) {
            res.status(200).json({ ok: true, username: config.username, note: 'tidak ada perubahan' });
            return;
        }

        const what = [usernameChanged && 'username', passwordChanged && 'password'].filter(Boolean).join(' + ');
        await saveAdminConfig(config, `Update admin credentials (${what})`);

        // Issue a fresh JWT with new username so client session stays valid
        const token = signSession(config.username);
        setSessionCookie(res, token);

        res.status(200).json({
            ok: true,
            username: config.username,
            usernameChanged,
            passwordChanged,
        });
    } catch (err) {
        console.error('[change-credentials]', err);
        res.status(500).json({ error: err.message });
    }
}
