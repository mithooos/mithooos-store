<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Response.php';

$db = Database::getInstance();
$type = $_GET['type'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if (!$id || !in_array($type, ['product', 'blog', 'payment'])) {
    http_response_code(404);
    exit('Not found');
}

$row = null;
if ($type === 'product') {
    $row = $db->fetchOne("SELECT image_data as data, image_mime as mime FROM product_images WHERE image_id = ?", [$id]);
} elseif ($type === 'blog') {
    $row = $db->fetchOne("SELECT featured_image_data as data, featured_image_mime as mime FROM blog_posts WHERE post_id = ?", [$id]);
} elseif ($type === 'payment') {
    $row = $db->fetchOne("SELECT payment_proof_data as data, payment_proof_mime as mime FROM orders WHERE order_id = ?", [$id]);
}

if (!$row || !$row['data']) {
    http_response_code(404);
    exit('Image not found');
}

// Ensure cache is set
header('Cache-Control: public, max-age=31536000, immutable');
header('Content-Type: ' . ($row['mime'] ?: 'image/jpeg'));
echo $row['data'];
exit;
