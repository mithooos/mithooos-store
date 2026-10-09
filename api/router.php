<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$ext = pathinfo($path, PATHINFO_EXTENSION);
$file = dirname(__DIR__) . '/public' . $path;

// Serve existing files directly
if (file_exists($file) && !is_dir($file)) {
    return false; 
}
if (is_dir($file) && file_exists($file . '/index.html')) {
    return false;
}

require __DIR__ . '/index.php';
