import { load as loadCheerio } from 'cheerio';
import { requireAuth } from './_lib/auth.js';
import { loadSchema, loadPreviewHtml } from './_lib/config.js';

// Build a concrete field descriptor from an itemField + list section + item index
function expandItemField(section, itemField, index) {
    const absSelector = `${section.listConfig.container} ${section.listConfig.itemSelector}:nth-of-type(${index + 1}) ${itemField.scope}`.replace(/\s+/g, ' ').trim();
    return {
        key:        `${section.id}.${index}.${itemField.key}`,
        label:      `${section.listConfig.label || 'Item'} #${index + 1} — ${itemField.label}`,
        type:       itemField.type,
        selector:   absSelector,
        html:       !!itemField.html,
        textOnly:   !!itemField.textOnly,
        multi:      !!itemField.multi,
        attribute:  itemField.attribute || null,
        uploadSlot: itemField.uploadSlot ? `${itemField.uploadSlot}-${index + 1}` : undefined,
    };
}

function readFieldValue($, f) {
    let $el = $(f.selector);
    if (f.multi && $el.length > 0) $el = $el.first();
    if ($el.length === 0) return { value: '', found: false, matches: 0 };
    let value = '';
    if (f.attribute)      value = $el.attr(f.attribute) ?? '';
    else if (f.html)      value = $el.html() ?? '';
    else if (f.textOnly)  value = $el.contents().filter((_, n) => n.type === 'text').text();
    else                  value = $el.text();
    return { value: f.attribute ? value : value.trim(), found: true, matches: $(f.selector).length };
}

export default async function handler(req, res) {
    if (req.method !== 'GET') { res.status(405).json({ error: 'Method not allowed' }); return; }
    if (!requireAuth(req, res)) return;

    try {
        const [schema, { html }] = await Promise.all([loadSchema(), loadPreviewHtml()]);
        const $ = loadCheerio(html, { decodeEntities: false });
        const result = { sections: [] };

        for (const section of schema.sections) {
            const out = {
                id: section.id,
                title: section.title,
                fields: [],
            };

            // Static (header) fields
            for (const f of section.fields || []) {
                const { value, found, matches } = readFieldValue($, f);
                out.fields.push({
                    key: f.key, label: f.label, type: f.type,
                    html: !!f.html, textOnly: !!f.textOnly, multi: !!f.multi, attribute: f.attribute || null,
                    uploadSlot: f.uploadSlot, found, selector: f.selector, matches, value,
                });
            }

            // List section — iterate items and expand fields per item
            if (section.listConfig && Array.isArray(section.itemFields)) {
                const items = $(`${section.listConfig.container} > ${section.listConfig.itemSelector}`);
                out.listConfig = {
                    container:   section.listConfig.container,
                    itemSelector:section.listConfig.itemSelector,
                    itemLabel:   section.listConfig.label || 'Item',
                    min:         section.listConfig.min ?? 1,
                    max:         section.listConfig.max ?? 50,
                    count:       items.length,
                };
                out.items = [];
                for (let i = 0; i < items.length; i++) {
                    const itemOut = { index: i, fields: [] };
                    for (const itemField of section.itemFields) {
                        const f = expandItemField(section, itemField, i);
                        const { value, found, matches } = readFieldValue($, f);
                        itemOut.fields.push({ ...f, found, matches, value });
                    }
                    out.items.push(itemOut);
                }
            }

            result.sections.push(out);
        }
        res.status(200).json(result);
    } catch (err) {
        console.error('[content]', err);
        res.status(500).json({ error: err.message });
    }
}
