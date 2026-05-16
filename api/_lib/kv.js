// Vercel KV (Upstash Redis under the hood) wrapper for view/like counters.
// Requires Vercel KV connected to project: env vars KV_REST_API_URL + KV_REST_API_TOKEN
// auto-injected by Vercel after dashboard connection.
//
// Key schema:
//   views:{slug}         → integer counter (INCR)
//   likes:{slug}         → integer counter (INCR)
//   like-ip:{slug}:{ip}  → "1" (TTL 24h) to prevent duplicate likes from same IP

import { kv } from '@vercel/kv';

const VIEWS_KEY = (slug) => `views:${slug}`;
const LIKES_KEY = (slug) => `likes:${slug}`;
const LIKE_IP_KEY = (slug, ip) => `like-ip:${slug}:${ip}`;
const IP_DEDUPE_TTL_S = 60 * 60 * 24; // 24 hours

function safeSlug(s) {
    return String(s || '').toLowerCase().replace(/[^a-z0-9_-]/g, '').slice(0, 80);
}

export function isKvAvailable() {
    return !!(process.env.KV_REST_API_URL && process.env.KV_REST_API_TOKEN);
}

export async function getCounters(slugs) {
    const cleanSlugs = slugs.map(safeSlug).filter(Boolean);
    if (cleanSlugs.length === 0) return {};
    const keys = cleanSlugs.flatMap(s => [VIEWS_KEY(s), LIKES_KEY(s)]);
    const values = await kv.mget(...keys);
    const result = {};
    for (let i = 0; i < cleanSlugs.length; i++) {
        result[cleanSlugs[i]] = {
            views: Number(values[i * 2])     || 0,
            likes: Number(values[i * 2 + 1]) || 0,
        };
    }
    return result;
}

export async function incrementViews(slugs) {
    const cleanSlugs = slugs.map(safeSlug).filter(Boolean);
    if (cleanSlugs.length === 0) return {};
    const result = {};
    // INCR each (Vercel KV supports pipeline but simpler to do sequential)
    for (const slug of cleanSlugs) {
        const newVal = await kv.incr(VIEWS_KEY(slug));
        result[slug] = { views: Number(newVal) };
    }
    return result;
}

export async function incrementLike(slug, ip) {
    const s = safeSlug(slug);
    if (!s) throw new Error('Slug tidak valid');

    if (ip) {
        const ipKey = LIKE_IP_KEY(s, ip);
        const seen = await kv.get(ipKey);
        if (seen) {
            const current = Number(await kv.get(LIKES_KEY(s))) || 0;
            return { likes: current, alreadyLiked: true };
        }
        // Mark IP as seen with TTL
        await kv.set(ipKey, '1', { ex: IP_DEDUPE_TTL_S });
    }

    const newVal = await kv.incr(LIKES_KEY(s));
    return { likes: Number(newVal), alreadyLiked: false };
}

export function clientIp(req) {
    // Vercel injects x-forwarded-for; first IP is client
    const xff = req.headers['x-forwarded-for'] || req.headers['x-real-ip'] || '';
    return String(xff).split(',')[0].trim() || 'unknown';
}
