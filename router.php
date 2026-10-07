<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$ext = pathinfo($path, PATHINFO_EXTENSION);
$file = __DIR__ . $path;

// Serve existing files directly
if (file_exists($file) && !is_dir($file)) {
    return false; 
}

// Serve 404 page for missing files or routes
http_response_code(404);
include __DIR__ . '/404.php';
return true;
