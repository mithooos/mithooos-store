<?php
/**
 * Mithooos - Dynamic Sitemap Generator
 * Generates XML sitemap including static pages and dynamic products.
 */

$_envFile = dirname(__DIR__, 2) . '/.env';
if (is_file($_envFile)) {
    foreach (file($_envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $_envLine) {
        if (str_starts_with(trim($_envLine), '#') || !str_contains($_envLine, '=')) continue;
        [$_envKey, $_envValue] = explode('=', $_envLine, 2);
        if (!getenv(trim($_envKey))) putenv(trim($_envKey) . '=' . trim($_envValue));
    }
}
unset($_envFile, $_envLine, $_envKey, $_envValue);

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Database.php';

header('Content-Type: application/xml; charset=utf-8');

// Ensure absolute URLs for the sitemap
$baseUrl = rtrim(APP_URL, '/');

// Get static URLs
$staticUrls = [
    ['loc' => $baseUrl . '/', 'changefreq' => 'daily', 'priority' => '1.0'],
    ['loc' => $baseUrl . '/shop', 'changefreq' => 'daily', 'priority' => '0.9'],
    ['loc' => $baseUrl . '/about', 'changefreq' => 'monthly', 'priority' => '0.5'],
    ['loc' => $baseUrl . '/contact', 'changefreq' => 'monthly', 'priority' => '0.4'],
    ['loc' => $baseUrl . '/blog', 'changefreq' => 'weekly', 'priority' => '0.6'],
];

// Get dynamic products
$db = Database::getInstance();
$products = $db->fetchAll(
    "SELECT product_id, slug, updated_at FROM products WHERE status = 'published' AND is_active = TRUE ORDER BY product_id DESC"
);

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
    $loc = $baseUrl . '/product?id=' . $product['product_id'];
    $lastmod = date('c', strtotime($product['updated_at']));
    
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
    echo "    <lastmod>" . $lastmod . "</lastmod>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>0.8</priority>\n";
    echo "  </url>\n";
}

echo '</urlset>';
