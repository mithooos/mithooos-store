<?php
require_once __DIR__ . '/../backend/config/constants.php';
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/includes/Database.php';
require_once __DIR__ . '/../backend/includes/Product.php';

$db = Database::getInstance();
$productModel = new Product($db);

$productId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_GET['slug']) ? $_GET['slug'] : 0);
$by = isset($_GET['slug']) ? 'slug' : 'id';
$product = $productId ? $productModel->find($productId, $by) : false;

$title = "Product Not Found | Mithooos";
$description = "This product could not be found or is no longer available.";
$image = "";
$url = "https://mithooos.com/pages/product.php?" . ($by === 'slug' ? 'slug=' : 'id=') . htmlspecialchars($productId);

if ($product) {
    $title = $product['meta_title'] ?: $product['product_name'] . ' | Mithooos';
    $rawDesc = $product['meta_description'] ?: ($product['short_description'] ?: $product['description']);
    $description = mb_substr(trim(strip_tags($rawDesc)), 0, 160);
    
    if (!empty($product['images'][0]['image_url'])) {
        $img = $product['images'][0]['image_url'];
        
        if (preg_match('#/uploads/.*$#', $img, $matches)) {
            $rawImage = 'https://mithooos.com' . $matches[0];
        } elseif (str_starts_with($img, '/')) {
            $rawImage = 'https://mithooos.com' . $img;
        } elseif (!preg_match('#^https?://#', $img)) {
            $rawImage = 'https://mithooos.com/' . $img;
        } else {
            $rawImage = $img;
        }
    } else {
        $rawImage = 'https://mithooos.com/images/logo.png';
    }
} else {
    $rawImage = 'https://mithooos.com/images/logo.png';
}

$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
$description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
$image = htmlspecialchars($rawImage, ENT_QUOTES, 'UTF-8');
$url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

ob_start();
include __DIR__ . '/product-detail.php';
$html = ob_get_clean();

// Replace standard meta tags in the <head>
$metaHtml = <<<HTML
  <title>{$title}</title>
  <meta name="description" id="metaDesc" content="{$description}">
  <link rel="canonical" href="{$url}">
  <meta property="og:title" id="ogTitle" content="{$title}">
  <meta property="og:description" id="ogDesc" content="{$description}">
  <meta property="og:image" id="ogImage" content="{$image}">
  <meta property="og:url" id="ogUrl" content="{$url}">
  <meta property="og:type" content="product">
  <meta property="og:site_name" content="Mithooos">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" id="twTitle" content="{$title}">
  <meta name="twitter:description" id="twDesc" content="{$description}">
  <meta name="twitter:image" id="twImage" content="{$image}">
HTML;

if ($product) {
    $jsonLd = [
        "@context" => "https://schema.org",
        "@type" => "Product",
        "name" => $product['product_name'],
        "url" => "https://mithooos.com/pages/product.php?" . ($by === 'slug' ? 'slug=' : 'id=') . $productId
    ];
    
    $rawJsonDesc = $product['description'] ?: $product['short_description'];
    if (!empty($rawJsonDesc)) {
        $jsonLd['description'] = strip_tags($rawJsonDesc);
    }
    
    if (!empty($rawImage)) { // $rawImage is before htmlspecialchars
        $jsonLd['image'] = $rawImage;
    }
    
    if (!empty($product['sku'])) {
        $jsonLd['sku'] = (string)$product['sku'];
    }
    
    if (!empty($product['brand'])) {
        $jsonLd['brand'] = [
            "@type" => "Brand",
            "name" => $product['brand']
        ];
    } else {
        $jsonLd['brand'] = [
            "@type" => "Brand",
            "name" => "Mithooos"
        ];
    }
    
    $price = $product['final_price'] ?? $product['price'] ?? null;
    if ($price !== null) {
        $jsonLd['offers'] = [
            "@type" => "Offer",
            "price" => $price,
            "priceCurrency" => "PKR",
            "availability" => ((int)($product['stock_quantity'] ?? 0) > 0) ? "https://schema.org/InStock" : "https://schema.org/OutOfStock",
            "url" => $jsonLd['url']
        ];
    }
    
    // JSON_HEX_TAG prevents closing script tag injection e.g., </script>
    $jsonLdScript = "\n" . '<script type="application/ld+json">' . "\n" . json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . "\n" . '</script>';
    
    // AEO FAQ Schema
    $faqLd = [
        "@context" => "https://schema.org",
        "@type" => "FAQPage",
        "mainEntity" => [
            [
                "@type" => "Question",
                "name" => "Is this product authentic Sindhi heritage?",
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => "Yes, all Mithooos products are sourced to reflect authentic Sindhi heritage and quality."
                ]
            ],
            [
                "@type" => "Question",
                "name" => "What payment methods are accepted?",
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => "Mithooos accepts Cash on Delivery (COD) and Easypaisa."
                ]
            ]
        ]
    ];
    $jsonLdScript .= "\n" . '<script type="application/ld+json">' . "\n" . json_encode($faqLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . "\n" . '</script>';

    $metaHtml .= $jsonLdScript;
}

// Strip out existing generic meta tags related to SEO to avoid duplicates
$html = preg_replace('/<title>.*?<\/title>/is', '', $html);
$html = preg_replace('/<meta name="description".*?>/is', '', $html);
$html = preg_replace('/<link rel="canonical".*?>/is', '', $html);
$html = preg_replace('/<meta property="og:(title|description|image|url|type|site_name)".*?>/is', '', $html);
$html = preg_replace('/<meta name="twitter:(card|title|description|image)".*?>/is', '', $html);

// Inject the dynamic metadata before </head>
$html = str_replace('</head>', $metaHtml . "\n</head>", $html);

echo $html;
