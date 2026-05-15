import { load as loadCheerio } from 'cheerio';
import { requireAuth } from './_lib/auth.js';
import { loadSchema, loadPreviewHtml, savePreviewHtml } from './_lib/config.js';

// Resolve a save key into a concrete field {selector, attribute, html, textOnly, multi}
// Supports:
//   - static field key like "hero.title" → match section.fields[].key
//   - list field key like "team.0.name"  → use section.listConfig + section.itemFields
function resolveKey(schema, key) {
    // Try list pattern: sectionId.index.fieldKey
    const listMatch = key.match(/^([a-z-]+)\.(\d+)\.([a-z._-]+)$/i);
    if (listMatch) {
        const [, sectionId, idxStr, fieldKey] = listMatch;
        const section = schema.sections.find(s => s.id === sectionId);
        if (!section || !section.listConfig || !Array.isArray(section.itemFields)) return null;
        const idx = parseInt(idxStr, 10);
        const itemField = section.itemFields.find(f => f.key === fieldKey);
        if (!itemField) return null;
        const absSelector = `${section.listConfig.container} ${section.listConfig.itemSelector}:nth-of-type(${idx + 1}) ${itemField.scope}`.replace(/\s+/g, ' ').trim();
        return {
            selector:  absSelector,
            html:      !!itemField.html,
            textOnly:  !!itemField.textOnly,
            multi:     !!itemField.multi,
            attribute: itemField.attribute || null,
        };
    }
    // Static fallback
    for (const section of schema.sections) {
        const f = (section.fields || []).find(x => x.key === key);
        if (f) return {
            selector:  f.selector,
            html:      !!f.html,
            textOnly:  !!f.textOnly,
            multi:     !!f.multi,
            attribute: f.attribute || null,
        };
    }
    return null;
}

function applyToOne(f, $one, newValue) {
    if (f.attribute) {
        if (newValue === '' || newValue == null) $one.removeAttr(f.attribute);
        else                                     $one.attr(f.attribute, String(newValue));
    } else if (f.html) {
        $one.html(String(newValue));
    } else if (f.textOnly) {
        const directTextNodes = $one.contents().filter((_, n) => n.type === 'text').toArray();
        if (directTextNodes.length === 0) {
            $one.prepend(' ' + String(newValue) + ' ');
        } else {
            directTextNodes[0].data = ' ' + String(newValue) + ' ';
            for (let i = 1; i < directTextNodes.length; i++) directTextNodes[i].data = '';
        }
    } else {
        $one.text(String(newValue));
    }
}

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

        const applied = [], skipped = [];

        for (const [key, newValue] of Object.entries(body.changes)) {
            const f = resolveKey(schema, key);
            if (!f) { skipped.push({ key, reason: 'unknown key (no schema match)' }); continue; }
            const $el = $(f.selector);
            if ($el.length === 0) { skipped.push({ key, reason: `selector matched 0: ${f.selector}` }); continue; }
            if ($el.length > 1 && !f.multi) {
                skipped.push({ key, reason: `selector matched ${$el.length} (use multi:true to apply to all)` });
                continue;
            }

            if (f.multi) $el.each((_, el) => applyToOne(f, $(el), newValue));
            else         applyToOne(f, $el, newValue);
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
            ok: true, applied, skipped, committed: true,
            commitSha: result.commit?.sha?.slice(0, 7) || null,
            commitUrl: result.commit?.html_url || null,
        });
    } catch (err) {
        console.error('[save]', err);
        res.status(500).json({ error: err.message });
    }
}
