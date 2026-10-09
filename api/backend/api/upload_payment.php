<?php
/**
 * Handle payment screenshot uploads.
 * Requires an authenticated user.
 */
$user = $auth->require();

if ($method !== 'POST') {
    Response::error('Method not allowed.', 405);
    return;
}

if (!isset($_FILES['screenshot']) || $_FILES['screenshot']['error'] !== UPLOAD_ERR_OK) {
    Response::error('No file uploaded or upload error occurred.', 400);
    return;
}

$file = $_FILES['screenshot'];

// Validate file type
$allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
$mime_type = mime_content_type($file['tmp_name']);
if (!in_array($mime_type, $allowed_types)) {
    Response::error('Invalid file type. Only JPG, PNG, and WebP are allowed.', 400);
    return;
}

// Validate file size (max 5MB)
if ($file['size'] > 5 * 1024 * 1024) {
    Response::error('File is too large. Maximum size is 5MB.', 400);
    return;
}

$data = file_get_contents($file['tmp_name']);
$base64 = 'data:' . $mime_type . ';base64,' . base64_encode($data);

// Return the base64 URL to be passed to order creation
$url = $base64;
Response::success(['url' => $url], 'File uploaded successfully.');
