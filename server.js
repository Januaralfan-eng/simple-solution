// Minimal static HTTP server for Railway / any Node host.
// Serves preview-home.html at "/" and preview-mobile.html at "/mobile".
// ESM module (package.json has "type": "module").

import http from 'node:http';
import fs   from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const PORT = process.env.PORT || 3000;
const ROOT = __dirname;

const MIME = {
    '.html':  'text/html; charset=utf-8',
    '.js':    'application/javascript; charset=utf-8',
    '.mjs':   'application/javascript; charset=utf-8',
    '.css':   'text/css; charset=utf-8',
    '.json':  'application/json; charset=utf-8',
    '.svg':   'image/svg+xml',
    '.png':   'image/png',
    '.jpg':   'image/jpeg',
    '.jpeg':  'image/jpeg',
    '.gif':   'image/gif',
    '.webp':  'image/webp',
    '.ico':   'image/x-icon',
    '.txt':   'text/plain; charset=utf-8',
    '.xml':   'application/xml; charset=utf-8',
    '.woff':  'font/woff',
    '.woff2': 'font/woff2',
};

const REWRITES = {
    '/':        '/preview-home.html',
    '/mobile':  '/preview-mobile.html',
};

const server = http.createServer((req, res) => {
    let url = decodeURI(req.url.split('?')[0]);
    if (REWRITES[url]) url = REWRITES[url];

    const filePath = path.normalize(path.join(ROOT, url));

    if (!filePath.startsWith(ROOT)) {
        res.writeHead(403);
        res.end('Forbidden');
        return;
    }

    fs.stat(filePath, (err, stat) => {
        if (err || !stat.isFile()) {
            res.writeHead(404, { 'Content-Type': 'text/plain; charset=utf-8' });
            res.end('404 — Not Found');
            return;
        }
        const ext = path.extname(filePath).toLowerCase();
        res.writeHead(200, {
            'Content-Type':  MIME[ext] || 'application/octet-stream',
            'Cache-Control': 'public, max-age=60',
        });
        fs.createReadStream(filePath).pipe(res);
    });
});

server.listen(PORT, () => {
    console.log(`[server] listening on port ${PORT}  —  /  ->  preview-home.html,  /mobile  ->  preview-mobile.html`);
});
