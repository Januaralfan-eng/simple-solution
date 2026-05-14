<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Agency Identity
    |--------------------------------------------------------------------------
    */
    'name'    => env('AGENCY_NAME', 'Creative Agency'),
    'tagline' => env('AGENCY_TAGLINE', 'We Build Digital Experiences'),
    'email'   => env('AGENCY_EMAIL', 'hello@youragency.com'),
    'phone'   => env('AGENCY_PHONE', '+62 812 3456 7890'),
    'address' => env('AGENCY_ADDRESS', 'Jakarta, Indonesia'),

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Integration
    |--------------------------------------------------------------------------
    */
    'whatsapp' => env('WHATSAPP_NUMBER', '6281234567890'),

    /*
    |--------------------------------------------------------------------------
    | Social Media
    |--------------------------------------------------------------------------
    */
    'social' => [
        'github'    => env('SOCIAL_GITHUB',    null),
        'instagram' => env('SOCIAL_INSTAGRAM', null),
        'linkedin'  => env('SOCIAL_LINKEDIN',  null),
        'twitter'   => env('SOCIAL_TWITTER',   null),
        'youtube'   => env('SOCIAL_YOUTUBE',   null),
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO Defaults
    |--------------------------------------------------------------------------
    */
    'seo' => [
        'default_title'       => env('SEO_DEFAULT_TITLE', 'Creative Agency — We Build Digital Experiences'),
        'default_description' => env('SEO_DEFAULT_DESCRIPTION', 'Premium creative tech agency specializing in web development, UI/UX design, and digital strategy.'),
        'default_image'       => 'images/og-default.jpg',
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics
    |--------------------------------------------------------------------------
    */
    'analytics' => [
        'ga_measurement_id' => env('GA_MEASUREMENT_ID', null),
        'ga_view_id'        => env('GA_VIEW_ID', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Media / Image Settings
    |--------------------------------------------------------------------------
    */
    'media' => [
        'max_width'    => env('IMAGE_MAX_WIDTH', 2400),
        'quality'      => env('IMAGE_QUALITY', 85),
        'webp_enabled' => true,
        'avif_enabled' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Keys & TTLs (seconds)
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'homepage'  => 3600,   // 1 hour
        'portfolio' => 3600,
        'blog'      => 1800,   // 30 minutes
        'settings'  => 86400,  // 24 hours
        'sitemap'   => 43200,  // 12 hours
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact Form
    |--------------------------------------------------------------------------
    */
    'contact' => [
        'notify_email'    => env('AGENCY_EMAIL', 'hello@youragency.com'),
        'rate_limit'      => 5,   // max submissions per hour per IP
        'honeypot_field'  => '_website',
    ],

    /*
    |--------------------------------------------------------------------------
    | Client Portal
    |--------------------------------------------------------------------------
    */
    'portal' => [
        'enabled'              => true,
        'max_upload_mb'        => 25,
        'allowed_upload_types' => ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg', 'zip'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Logging — kamus.zasha.online integration
    |--------------------------------------------------------------------------
    */
    'kamus' => [
        'enabled' => env('KAMUS_LOGGING_ENABLED', false),
        'api_key' => env('KAMUS_API_KEY', null),
    ],

];
