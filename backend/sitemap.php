<?php
/**
 * Mithooos - Dynamic Sitemap Generator
 * Generates XML sitemap including static pages and dynamic products.
 */
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';

header('Content-Type: application/xml; charset=utf-8');

$db = new PDO("pgsql:host={$dbHost};port=5432;dbname={$dbName}", $dbUser, $dbPass);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ensure absolute URLs for the sitemap
$baseUrl = 'https://mithooos.com';

// Get static URLs
$staticUrls = [
    ['loc' => $baseUrl . '/', 'changefreq' => 'daily', 'priority' => '1.0'],
    ['loc' => $baseUrl . '/pages/shop.php', 'changefreq' => 'daily', 'priority' => '0.9'],
    ['loc' => $baseUrl . '/pages/about.php', 'changefreq' => 'monthly', 'priority' => '0.5'],
    ['loc' => $baseUrl . '/pages/contact.php', 'changefreq' => 'monthly', 'priority' => '0.4'],
    ['loc' => $baseUrl . '/pages/blog.php', 'changefreq' => 'weekly', 'priority' => '0.6'],
];

// Get dynamic products
$stmt = $db->prepare("SELECT product_id, slug, updated_at FROM products WHERE status = 'published' AND is_active = TRUE ORDER BY product_id DESC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($staticUrls as $url) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($url['loc']) . "</loc>\n";
    echo "    <changefreq>" . $url['changefreq'] . "</changefreq>\n";
    echo "    <priority>" . $url['priority'] . "</priority>\n";
    echo "  </url>\n";
}

foreach ($products as $product) {
    $loc = $baseUrl . '/pages/product.php?id=' . $product['product_id'];
    $lastmod = date('c', strtotime($product['updated_at']));
    
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
    echo "    <lastmod>" . $lastmod . "</lastmod>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>0.8</priority>\n";
    echo "  </url>\n";
}

echo '</urlset>';
