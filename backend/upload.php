<?php
/**
 * MITHOOOS — Image Upload API
 * POST /api/upload/image
 * Accepts: multipart/form-data with field "image" (or multiple "images[]")
 * Returns: { url, filename, width, height }
 * Requires: admin JWT
 */

declare(strict_types=1);

// Load the local environment before constants calculate upload URLs.
$_envFile = dirname(__DIR__) . '/.env';
if (file_exists($_envFile)) {
    foreach (file($_envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $_envLine) {
        if (str_starts_with(trim($_envLine), '#') || !str_contains($_envLine, '=')) continue;
        [$_envKey, $_envValue] = explode('=', $_envLine, 2);
        putenv(trim($_envKey) . '=' . trim($_envValue));
    }
}
unset($_envFile, $_envLine, $_envKey, $_envValue);

// Bootstrap
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Response.php';
require_once __DIR__ . '/includes/Auth.php';

header('Content-Type: application/json; charset=utf-8');

// CORS
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed = ['http://localhost', 'http://localhost:3000', 'http://127.0.0.1'];
if (in_array($origin, $allowed, true)) {
    header("Access-Control-Allow-Origin: $origin");
}
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method not allowed.', 405); exit;
}

$db   = Database::getInstance();
$auth = new Auth($db);
$auth->requireAdmin(); // Admin only

// ── Upload directory ──────────────────────────────────────────────
$uploadDir = UPLOAD_DIR . '/products/';
$requestScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$requestHost = $_SERVER['HTTP_HOST'] ?? '';
$uploadUrl = $requestHost !== ''
    ? $requestScheme . '://' . $requestHost . '/uploads/products/'
    : UPLOAD_URL . '/products/';

if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        Response::error('Upload directory could not be created.', 500); exit;
    }
}

// ── Collect files ─────────────────────────────────────────────────
$files = [];

if (!empty($_FILES['image'])) {
    $files[] = $_FILES['image'];
} elseif (!empty($_FILES['images'])) {
    // Multiple file upload: images[]
    $count = count($_FILES['images']['name']);
    for ($i = 0; $i < $count; $i++) {
        $files[] = [
            'name'     => $_FILES['images']['name'][$i],
            'type'     => $_FILES['images']['type'][$i],
            'tmp_name' => $_FILES['images']['tmp_name'][$i],
            'error'    => $_FILES['images']['error'][$i],
            'size'     => $_FILES['images']['size'][$i],
        ];
    }
} else {
    Response::error('No image file received.'); exit;
}

if (count($files) > 10) {
    Response::error('Maximum 10 images per upload.'); exit;
}

// ── Process each file ────────────────────────────────────────────
$results = [];
$errors  = [];

foreach ($files as $file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Upload error for ' . htmlspecialchars($file['name']);
        continue;
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        $errors[] = htmlspecialchars($file['name']) . ' exceeds 5MB limit.';
        continue;
    }

    // Validate MIME by reading magic bytes (not trusting $_FILES['type'])
    $finfo    = new \finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES, true)) {
        $errors[] = htmlspecialchars($file['name']) . ' must be JPEG, PNG, or WebP.';
        continue;
    }

    // Generate unique filename: {timestamp}_{random}.{ext}
    $data = file_get_contents($file['tmp_name']);
    $base64 = 'data:' . $mimeType . ';base64,' . base64_encode($data);

    // Get image dimensions
    [$width, $height] = @getimagesize($file['tmp_name']) ?: [null, null];

    $results[] = [
        'url'      => $base64,
        'filename' => $file['name'],
        'width'    => $width,
        'height'   => $height,
        'size'     => $file['size'],
    ];
}

if (empty($results) && !empty($errors)) {
    Response::error(implode(' | ', $errors)); exit;
}

Response::success([
    'uploaded' => $results,
    'errors'   => $errors,
], count($results) . ' image(s) uploaded.');
