<?php
/**
 * EventOS Built-in Server Router
 * Drop-in replacement for artisan serve that bypasses the artisan command.
 * Usage: php -c php_custom.ini -t public server.php
 *
 * Key benefits over artisan serve:
 * - Preserves -c php.ini flag (artisan serve does NOT pass it to child processes)
 * - Handles CORS preflight for the built-in server
 * - Properly rewrites URLs for the SPA
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Tenant-ID, Authorization');
    header('Access-Control-Max-Age: 86400');
    http_response_code(204);
    return;
}

// Serve static files directly
$file = __DIR__ . '/public' . $path;
if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
    return false;
}

// Rewrite /api/* and other routes to index.php
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/public/index.php';

require __DIR__ . '/public/index.php';
