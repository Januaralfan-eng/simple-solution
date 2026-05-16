import { isKvAvailable, getCounters } from './_lib/kv.js';

// PUBLIC endpoint (no auth) — used by preview-home.html JS to display counts
export default async function handler(req, res) {
    if (req.method !== 'GET' && req.method !== 'POST') {
        res.status(405).json({ error: 'Method not allowed' }); return;
    }
    if (!isKvAvailable()) {
        res.status(200).json({ kvAvailable: false, counters: {} });
        return;
    }
    try {
        // Accept slugs via query (?slugs=a,b,c) or POST body { slugs: [...] }
        let slugs = [];
        if (req.method === 'GET') {
            const q = req.query?.slugs ?? '';
            slugs = String(q).split(',').filter(Boolean);
        } else {
            slugs = Array.isArray(req.body?.slugs) ? req.body.slugs : [];
        }
        const counters = await getCounters(slugs);
        res.setHeader('Cache-Control', 'public, max-age=10, stale-while-revalidate=30');
        res.status(200).json({ kvAvailable: true, counters });
    } catch (err) {
        console.error('[counters]', err);
        res.status(500).json({ error: err.message });
    }
}
