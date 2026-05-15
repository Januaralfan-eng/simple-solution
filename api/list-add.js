import { load as loadCheerio } from 'cheerio';
import { requireAuth } from './_lib/auth.js';
import { loadSchema, loadPreviewHtml, savePreviewHtml } from './_lib/config.js';

export default async function handler(req, res) {
    if (req.method !== 'POST') { res.status(405).json({ error: 'Method not allowed' }); return; }
    const session = requireAuth(req, res);
    if (!session) return;

    try {
        const { section: sectionId } = req.body || {};
        if (!sectionId) { res.status(400).json({ error: 'Body { section } wajib' }); return; }

        const [schema, preview] = await Promise.all([loadSchema(), loadPreviewHtml()]);
        const section = schema.sections.find(s => s.id === sectionId);
        if (!section || !section.listConfig) {
            res.status(400).json({ error: `Section "${sectionId}" tidak punya listConfig` }); return;
        }

        const $ = loadCheerio(preview.html, { decodeEntities: false });
        const items = $(`${section.listConfig.container} > ${section.listConfig.itemSelector}`);
        if (items.length === 0) {
            res.status(400).json({ error: 'Tidak ada item untuk di-clone (container kosong)' }); return;
        }
        if (items.length >= (section.listConfig.max ?? 50)) {
            res.status(400).json({ error: `Sudah mencapai batas maksimum ${section.listConfig.max} item` }); return;
        }

        // Clone the LAST item (most recently added pattern is usually most current)
        const lastIdx = items.length - 1;
        const clone = $.html(items.eq(lastIdx));
        $(section.listConfig.container).append(clone);

        const newHtml = $.html();
        const commitMessage = `Add ${section.listConfig.label || 'item'} ke ${sectionId} (cloned dari #${items.length}) — by ${session.username}`;
        const result = await savePreviewHtml(newHtml, commitMessage, preview.sha);

        res.status(200).json({
            ok: true, sectionId,
            newCount: items.length + 1,
            newIndex: items.length,
            commitSha: result.commit?.sha?.slice(0, 7) || null,
        });
    } catch (err) {
        console.error('[list-add]', err);
        res.status(500).json({ error: err.message });
    }
}
