import { isKvAvailable, incrementViews } from './_lib/kv.js';

// PUBLIC endpoint — called once per page load with all visible portfolio slugs
export default async function handler(req, res) {
    if (req.method !== 'POST') { res.status(405).json({ error: 'Method not allowed' }); return; }
    if (!isKvAvailable()) {
        res.status(200).json({ kvAvailable: false, counters: {} }); return;
    }
    try {
        const slugs = Array.isArray(req.body?.slugs) ? req.body.slugs : [];
        if (slugs.length === 0) { res.status(200).json({ kvAvailable: true, counters: {} }); return; }
        const result = await incrementViews(slugs);
        res.status(200).json({ kvAvailable: true, counters: result });
    } catch (err) {
        console.error('[view-increment]', err);
        res.status(500).json({ error: err.message });
    }
}
