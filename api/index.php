<?php
declare(strict_types=1);

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$route = $_GET['__route'] ?? null;
$target = null;
$scriptName = '/index.php';

if ($route === 'home' || ($route === null && in_array($requestPath, ['/', '/index.php'], true))) {
    $target = __DIR__ . '/home.php';
} elseif ($requestPath === '/backend/api/image.php') {
    $target = __DIR__ . '/backend/api/image.php';
    $scriptName = '/backend/api/image.php';
} elseif ($requestPath === '/backend/sitemap.php') {
    $target = __DIR__ . '/backend/sitemap.php';
    $scriptName = '/backend/sitemap.php';
} elseif (preg_match('~^/backend/(?:index\.php|api/|admin/)~', $requestPath)
    || $requestPath === '/backend'
    || $requestPath === '/backend/') {
    $target = __DIR__ . '/backend/index.php';
    $scriptName = '/backend/index.php';
} elseif ($requestPath === '/backend/upload.php') {
    $target = __DIR__ . '/backend/upload.php';
    $scriptName = '/backend/upload.php';
} elseif ($requestPath === '/admin-panel/login.php' || $route === 'admin-panel/login.php') {
    $target = __DIR__ . '/admin-panel/login.php';
    $scriptName = '/admin-panel/login.php';
} else {
    $page = null;
    if (is_string($route) && preg_match('~^pages/([a-z0-9-]+)\.php$~', $route, $matches)) {
        $page = $matches[1];
    } elseif (preg_match('~^/pages/([a-z0-9-]+)\.php$~', $requestPath, $matches)) {
        $page = $matches[1];
    } elseif (preg_match('~^/([a-z0-9-]+)/?$~', $requestPath, $matches)) {
        $page = $matches[1];
    }

    if ($page !== null && is_file(__DIR__ . '/pages/' . $page . '.php')) {
        $target = __DIR__ . '/pages/' . $page . '.php';
        $scriptName = '/pages/' . $page . '.php';
    }
}

if ($target === null || !is_file($target)) {
    http_response_code(404);
    $_SERVER['SCRIPT_NAME'] = '/404.php';
    require __DIR__ . '/errors/404.php';
    exit;
}

$_SERVER['SCRIPT_NAME'] = $scriptName;
require $target;
