import { requireAuth } from './_lib/auth.js';
import { writeFile } from './_lib/github.js';

const MAX_BYTES        = 2 * 1024 * 1024; // 2 MB
const ALLOWED_EXT      = new Set(['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg']);
const ALLOWED_MIME_RE  = /^image\/(png|jpe?g|gif|webp|svg\+xml)$/;

// Sanitize filename: keep only [A-Za-z0-9._-], lowercase, drop traversal
function safeName(input) {
    return String(input || '')
        .toLowerCase()
        .replace(/[^a-z0-9._-]/g, '-')
        .replace(/^-+|-+$/g, '')
        .replace(/-{2,}/g, '-')
        .slice(0, 80) || `upload-${Date.now()}`;
}

function extFromName(name) {
    const m = name.match(/\.([a-z0-9]+)$/i);
    return m ? m[1].toLowerCase() : '';
}

export default async function handler(req, res) {
    if (req.method !== 'POST') { res.status(405).json({ error: 'Method not allowed' }); return; }
    const session = requireAuth(req, res);
    if (!session) return;

    try {
        const body = req.body || {};
        const { filename, data, slot } = body;

        if (!filename || !data) { res.status(400).json({ error: 'Body harus { filename, data, slot? }' }); return; }

        // data is expected as data URI: "data:image/png;base64,iVBOR..."
        const match = String(data).match(/^data:([^;]+);base64,(.+)$/);
        if (!match) { res.status(400).json({ error: 'data harus dalam format data URI base64' }); return; }

        const mime    = match[1];
        const b64     = match[2];
        if (!ALLOWED_MIME_RE.test(mime)) {
            res.status(400).json({ error: `MIME tidak diizinkan (${mime}). Hanya PNG/JPG/GIF/WebP/SVG` }); return;
        }

        // Decode size check (approx: base64 size × 0.75 = byte size)
        const approxBytes = Math.floor(b64.length * 0.75);
        if (approxBytes > MAX_BYTES) {
            res.status(413).json({ error: `Ukuran file ${(approxBytes/1024/1024).toFixed(1)} MB melebihi batas ${(MAX_BYTES/1024/1024)} MB` }); return;
        }

        // Determine target path
        const cleanName = safeName(filename);
        const ext = extFromName(cleanName);
        if (!ALLOWED_EXT.has(ext)) {
            res.status(400).json({ error: `Ekstensi .${ext} tidak diizinkan. Hanya: ${[...ALLOWED_EXT].join(', ')}` }); return;
        }

        // If slot is provided (e.g., "logo"), use fixed name: images/logo.<ext>
        // Otherwise use timestamped name to avoid collisions: images/<slot-or-original>-<ts>.<ext>
        let targetPath;
        if (slot && /^[a-z0-9_-]{1,40}$/i.test(slot)) {
            targetPath = `images/${slot}.${ext}`;
        } else {
            const stem = cleanName.replace(/\.[a-z0-9]+$/i, '');
            targetPath = `images/${stem}-${Date.now()}.${ext}`;
        }

        // Commit binary image via raw base64 (avoid utf-8 corruption)
        const result = await writeFile(targetPath, null, `Upload image: ${targetPath} — by ${session.username}`, {
            rawBase64: b64,
        });

        // Public URL = same as path (Vercel serves /images/... statically)
        res.status(200).json({
            ok: true,
            path: targetPath,
            url:  '/' + targetPath,
            size: approxBytes,
            mime,
            commitSha: result.commit?.sha?.slice(0, 7) || null,
        });
    } catch (err) {
        console.error('[upload-image]', err);
        res.status(500).json({ error: err.message });
    }
}
