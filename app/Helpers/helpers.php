<?php

if (!function_exists('reading_time')) {
    /**
     * Calculate estimated reading time in minutes.
     */
    function reading_time(string $content): string
    {
        $words = str_word_count(strip_tags($content));
        $mins  = max(1, (int) ceil($words / 200));
        return "{$mins} min read";
    }
}

if (!function_exists('setting')) {
    /**
     * Get a setting value from database or config fallback.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        static $settings = null;

        if ($settings === null) {
            $settings = \Illuminate\Support\Facades\Cache::remember(
                'settings:all',
                config('agency.cache.settings', 86400),
                fn () => \App\Models\Setting::pluck('value', 'key')->toArray()
            );
        }

        return $settings[$key] ?? $default;
    }
}

if (!function_exists('agency_url')) {
    /**
     * Generate agency asset URL with optional versioning.
     */
    function agency_url(string $path): string
    {
        return asset($path);
    }
}

if (!function_exists('initials')) {
    /**
     * Generate initials from a name.
     */
    function initials(string $name, int $length = 2): string
    {
        return collect(explode(' ', $name))
            ->take($length)
            ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
            ->implode('');
    }
}

if (!function_exists('human_filesize')) {
    /**
     * Format file size to human readable.
     */
    function human_filesize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}

if (!function_exists('is_htmx_request')) {
    /**
     * Detect if current request is from HTMX.
     */
    function is_htmx_request(): bool
    {
        return request()->header('HX-Request') === 'true';
    }
}
