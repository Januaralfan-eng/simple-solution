import { getSessionFromRequest } from './_lib/auth.js';

export default function handler(req, res) {
    if (req.method !== 'GET') { res.status(405).json({ error: 'Method not allowed' }); return; }
    const s = getSessionFromRequest(req);
    res.status(200).json({ authenticated: !!s, username: s ? s.username : null });
}
