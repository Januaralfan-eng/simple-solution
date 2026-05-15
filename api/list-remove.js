import { load as loadCheerio } from 'cheerio';
import { requireAuth } from './_lib/auth.js';
import { loadSchema, loadPreviewHtml, savePreviewHtml } from './_lib/config.js';

export default async function handler(req, res) {
    if (req.method !== 'POST') { res.status(405).json({ error: 'Method not allowed' }); return; }
    const session = requireAuth(req, res);
    if (!session) return;

    try {
        const { section: sectionId, index } = req.body || {};
        if (!sectionId || index === undefined) {
            res.status(400).json({ error: 'Body { section, index } wajib' }); return;
        }
        const idx = parseInt(index, 10);
        if (Number.isNaN(idx) || idx < 0) { res.status(400).json({ error: 'index tidak valid' }); return; }

        const [schema, preview] = await Promise.all([loadSchema(), loadPreviewHtml()]);
        const section = schema.sections.find(s => s.id === sectionId);
        if (!section || !section.listConfig) {
            res.status(400).json({ error: `Section "${sectionId}" tidak punya listConfig` }); return;
        }

        const $ = loadCheerio(preview.html, { decodeEntities: false });
        const items = $(`${section.listConfig.container} > ${section.listConfig.itemSelector}`);
        if (idx >= items.length) {
            res.status(400).json({ error: `index ${idx} di luar jangkauan (count=${items.length})` }); return;
        }
        if (items.length <= (section.listConfig.min ?? 1)) {
            res.status(400).json({ error: `Tidak bisa hapus: minimum ${section.listConfig.min} item harus tetap ada` }); return;
        }

        items.eq(idx).remove();

        const newHtml = $.html();
        const commitMessage = `Remove ${section.listConfig.label || 'item'} #${idx + 1} dari ${sectionId} — by ${session.username}`;
        const result = await savePreviewHtml(newHtml, commitMessage, preview.sha);

        res.status(200).json({
            ok: true, sectionId, removedIndex: idx,
            newCount: items.length - 1,
            commitSha: result.commit?.sha?.slice(0, 7) || null,
        });
    } catch (err) {
        console.error('[list-remove]', err);
        res.status(500).json({ error: err.message });
    }
}
