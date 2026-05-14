<?php

/**
 * Vercel PHP entry point.
 * Routes all requests through Laravel's public/index.php.
 */

$_ENV['APP_BASE_PATH'] = dirname(__DIR__);

define('LARAVEL_START', microtime(true));

require dirname(__DIR__) . '/public/index.php';
