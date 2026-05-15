// Stateless auth: scrypt password hashing + JWT cookies
// No DB / KV needed. Credentials live in admin-config.json on the repo.

import crypto from 'node:crypto';
import { promisify } from 'node:util';
import jwt from 'jsonwebtoken';

const scrypt = promisify(crypto.scrypt);

export const COOKIE_NAME    = 'admin_session';
export const SESSION_TTL_S  = 60 * 60 * 24 * 7; // 7 days

function getJwtSecret() {
    const s = process.env.JWT_SECRET;
    if (!s || s.length < 16) throw new Error('JWT_SECRET env var belum di-set (min 16 char)');
    return s;
}

export async function hashPassword(password, saltHex) {
    const salt = saltHex || crypto.randomBytes(16).toString('hex');
    const hash = (await scrypt(String(password), salt, 64)).toString('hex');
    return { hash, salt };
}

export async function verifyPassword(password, salt, expectedHash) {
    const { hash } = await hashPassword(password, salt);
    const a = Buffer.from(hash, 'hex');
    const b = Buffer.from(expectedHash, 'hex');
    return a.length === b.length && crypto.timingSafeEqual(a, b);
}

export function signSession(username) {
    return jwt.sign({ u: username }, getJwtSecret(), { expiresIn: SESSION_TTL_S });
}

export function verifySession(token) {
    try { return jwt.verify(token, getJwtSecret()); }
    catch { return null; }
}

export function parseCookies(header) {
    const out = {};
    if (!header) return out;
    for (const part of header.split(/;\s*/)) {
        const i = part.indexOf('=');
        if (i < 0) continue;
        out[part.slice(0, i).trim()] = decodeURIComponent(part.slice(i + 1));
    }
    return out;
}

export function getSessionFromRequest(req) {
    const cookies = parseCookies(req.headers.cookie || '');
    const token = cookies[COOKIE_NAME];
    if (!token) return null;
    const payload = verifySession(token);
    return payload ? { username: payload.u, exp: payload.exp } : null;
}

export function setSessionCookie(res, token) {
    const secure = process.env.VERCEL ? '; Secure' : '';
    res.setHeader('Set-Cookie',
        `${COOKIE_NAME}=${token}; HttpOnly; Path=/; Max-Age=${SESSION_TTL_S}; SameSite=Lax${secure}`);
}

export function clearSessionCookie(res) {
    const secure = process.env.VERCEL ? '; Secure' : '';
    res.setHeader('Set-Cookie', `${COOKIE_NAME}=; HttpOnly; Path=/; Max-Age=0; SameSite=Lax${secure}`);
}

export function requireAuth(req, res) {
    const s = getSessionFromRequest(req);
    if (!s) {
        res.status(401).json({ error: 'Belum login' });
        return null;
    }
    return s;
}
