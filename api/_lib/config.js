// admin-config.json lifecycle — fetched from GitHub repo, written back via GitHub API.

import { readFile, writeFile } from './github.js';

const CONFIG_PATH = 'admin-config.json';

export async function loadAdminConfig() {
    const { content } = await readFile(CONFIG_PATH);
    if (!content) {
        throw new Error('admin-config.json belum ada di repo — seed dulu dengan default admin/admin');
    }
    return JSON.parse(content);
}

export async function saveAdminConfig(config, commitMessage) {
    const json = JSON.stringify(config, null, 2) + '\n';
    return writeFile(CONFIG_PATH, json, commitMessage || 'Update admin credentials');
}

// admin-schema.json — read once, schema is part of the build
export async function loadSchema() {
    const { content } = await readFile('admin-schema.json');
    if (!content) throw new Error('admin-schema.json tidak ditemukan di repo');
    return JSON.parse(content);
}

export async function loadPreviewHtml() {
    const { content, sha } = await readFile('preview-home.html');
    if (!content) throw new Error('preview-home.html tidak ditemukan di repo');
    return { html: content, sha };
}

export async function savePreviewHtml(html, message, sha) {
    return writeFile('preview-home.html', html, message, { sha });
}
