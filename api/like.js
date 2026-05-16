import { isKvAvailable, incrementLike, clientIp } from './_lib/kv.js';

// PUBLIC endpoint — like button on portfolio cards
export default async function handler(req, res) {
    if (req.method !== 'POST') { res.status(405).json({ error: 'Method not allowed' }); return; }
    if (!isKvAvailable()) {
        res.status(503).json({ error: 'KV belum di-enable di Vercel dashboard' }); return;
    }
    try {
        const { slug } = req.body || {};
        if (!slug) { res.status(400).json({ error: 'slug wajib' }); return; }
        const ip = clientIp(req);
        const result = await incrementLike(slug, ip);
        res.status(200).json({ ok: true, slug, ...result });
    } catch (err) {
        console.error('[like]', err);
        res.status(500).json({ error: err.message });
    }
}
