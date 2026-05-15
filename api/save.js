import { load as loadCheerio } from 'cheerio';
import { requireAuth, getSessionFromRequest } from './_lib/auth.js';
import { loadSchema, loadPreviewHtml, savePreviewHtml } from './_lib/config.js';

export default async function handler(req, res) {
    if (req.method !== 'POST') { res.status(405).json({ error: 'Method not allowed' }); return; }
    const session = requireAuth(req, res);
    if (!session) return;

    try {
        const body = req.body || {};
        if (!body || typeof body.changes !== 'object') {
            res.status(400).json({ error: 'Body harus { changes: { key: value } }' }); return;
        }

        const [schema, preview] = await Promise.all([loadSchema(), loadPreviewHtml()]);
        const $ = loadCheerio(preview.html, { decodeEntities: false });

        const fieldMap = new Map();
        for (const section of schema.sections) for (const f of section.fields) fieldMap.set(f.key, f);

        const applied = [], skipped = [];

        for (const [key, newValue] of Object.entries(body.changes)) {
            const f = fieldMap.get(key);
            if (!f) { skipped.push({ key, reason: 'unknown key' }); continue; }
            const $el = $(f.selector);
            if ($el.length === 0) { skipped.push({ key, reason: 'selector matched 0 elements' }); continue; }
            if ($el.length > 1)   { skipped.push({ key, reason: `selector matched ${$el.length} elements` }); continue; }

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

        if (applied.length === 0) {
            res.status(200).json({ ok: true, applied, skipped, committed: false, note: 'tidak ada field yang berhasil diapply' });
            return;
        }

        const newHtml = $.html();
        const summary = applied.length === 1 ? applied[0] : `${applied.length} field`;
        const commitMessage = `Update content via admin: ${summary} — by ${session.username}`;

        const result = await savePreviewHtml(newHtml, commitMessage, preview.sha);
        res.status(200).json({
            ok: true,
            applied,
            skipped,
            committed: true,
            commitSha: result.commit?.sha?.slice(0, 7) || null,
            commitUrl: result.commit?.html_url || null,
        });
    } catch (err) {
        console.error('[save]', err);
        res.status(500).json({ error: err.message });
    }
}
