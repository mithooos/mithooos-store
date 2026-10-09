<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Response.php';

$db = Database::getInstance();
$type = $_GET['type'] ?? '';
$id = (int)($_GET['id'] ?? 0);

if (!$id || !in_array($type, ['product', 'blog', 'payment', 'category'], true)) {
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
} elseif ($type === 'category') {
    $row = $db->fetchOne('SELECT cover_image_url AS data FROM categories WHERE category_id = ?', [$id]);
    if ($row && preg_match('#^data:(image/(?:jpeg|png|webp));base64,([A-Za-z0-9+/]*={0,2})$#D', $row['data'] ?? '', $matches)) {
        $row['mime'] = $matches[1];
        $row['data'] = base64_decode($matches[2], true);
    } else {
        $row = null;
    }
}

if (!$row || !$row['data']) {
    http_response_code(404);
    exit('Image not found');
}

if ($type === 'product' && str_starts_with($row['data'], 'data:')) {
    if (preg_match('#^data:(image/(?:jpeg|png|webp));base64,([A-Za-z0-9+/]*={0,2})$#D', $row['data'], $matches)) {
        $row['mime'] = $matches[1];
        $row['data'] = base64_decode($matches[2], true);
    } else {
        http_response_code(415);
        exit('Invalid image data');
    }
}

if ($row['data'] === false) {
    http_response_code(500);
    exit('Image data could not be decoded');
}

// Ensure cache is set
header('Cache-Control: public, max-age=3600');
header('Content-Type: ' . ($row['mime'] ?: 'image/jpeg'));
echo $row['data'];
exit;
