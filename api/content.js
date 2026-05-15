import { load as loadCheerio } from 'cheerio';
import { requireAuth } from './_lib/auth.js';
import { loadSchema, loadPreviewHtml } from './_lib/config.js';

export default async function handler(req, res) {
    if (req.method !== 'GET') { res.status(405).json({ error: 'Method not allowed' }); return; }
    if (!requireAuth(req, res)) return;

    try {
        const [schema, { html }] = await Promise.all([loadSchema(), loadPreviewHtml()]);
        const $ = loadCheerio(html, { decodeEntities: false });
        const result = { sections: [] };

        for (const section of schema.sections) {
            const out = { id: section.id, title: section.title, fields: [] };
            for (const f of section.fields) {
                let $el = $(f.selector);
                // For multi-target fields, read value from the first match
                if (f.multi && $el.length > 0) $el = $el.first();
                let value = '';
                if ($el.length === 0)        value = '';
                else if (f.html)             value = $el.html() ?? '';
                else if (f.textOnly)         value = $el.contents().filter((_, n) => n.type === 'text').text();
                else                         value = $el.text();
                out.fields.push({
                    key: f.key, label: f.label, type: f.type,
                    html: !!f.html, textOnly: !!f.textOnly, multi: !!f.multi,
                    found: $(f.selector).length > 0, matches: $(f.selector).length, selector: f.selector,
                    value: value.trim(),
                });
            }
            result.sections.push(out);
        }
        res.status(200).json(result);
    } catch (err) {
        console.error('[content]', err);
        res.status(500).json({ error: err.message });
    }
}
