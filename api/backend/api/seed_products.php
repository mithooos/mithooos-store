<?php
/**
 * ONE-TIME product seeder — remove after use.
 * Call: GET /backend/api/seed-products?key=mith_seed_oct26
 */
if (($_GET['key'] ?? '') !== 'mith_seed_oct26') {
    http_response_code(403); echo json_encode(['error' => 'Forbidden']); exit;
}

$results = [];

// ── 1. Get existing categories ────────────────────────────────
$cats = $db->fetchAll("SELECT category_id, slug FROM categories WHERE is_active = 1");
$catMap = [];
foreach ($cats as $c) { $catMap[$c['slug']] = $c['category_id']; }

// ── 2. Ensure all required categories exist ───────────────────
$needed = [
    ['category_name' => 'Sindhi Topi',      'slug' => 'sindhi-topi',    'description' => 'Traditional Sindhi caps featuring beautiful cultural patterns, intricate embroidery, and timeless heritage.',             'display_order' => 1],
    ['category_name' => 'Ajrak',             'slug' => 'ajrak',          'description' => 'Explore traditional Sindhi Ajrak featuring iconic geometric patterns, rich colors, and cultural artistry.',              'display_order' => 2],
    ['category_name' => 'Sindhi Kajoor',     'slug' => 'sindhi-kajoor',  'description' => 'Premium Sindhi dates, hand-picked and naturally sweet. Sold by weight.',                                                  'display_order' => 3],
    ['category_name' => 'Achaar',            'slug' => 'achaar',         'description' => 'Authentic Sindhi homemade pickles (achaar) made with traditional recipes. Sold by weight.',                              'display_order' => 4],
    ['category_name' => 'Home Textiles',     'slug' => 'home-textiles',  'description' => 'Premium bedsheets, curtains, comforters and home textile sets with Sindhi-inspired designs.',                             'display_order' => 5],
];

foreach ($needed as $cat) {
    if (!isset($catMap[$cat['slug']])) {
        $cid = $db->insert(
            "INSERT INTO categories (category_name, slug, description, display_order, is_active) VALUES (?,?,?,?,1)",
            [$cat['category_name'], $cat['slug'], $cat['description'], $cat['display_order']]
        );
        $catMap[$cat['slug']] = $cid;
        $results[] = "Created category: {$cat['category_name']} (id=$cid)";
    } else {
        $results[] = "Category exists: {$cat['category_name']} (id={$catMap[$cat['slug']]})";
    }
}

// ── 3. Helper: create product + variants ─────────────────────
function seedProduct(Database $db, array $p, array $variants = []): string {
    $existing = $db->fetchOne("SELECT product_id FROM products WHERE slug = ?", [$p['slug']]);
    if ($existing) {
        // Update price on existing product
        $db->execute("UPDATE products SET price = ?, stock_quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE product_id = ?",
            [$p['price'], $p['stock_quantity'], $existing['product_id']]);
        $pid = $existing['product_id'];
        $action = "Updated";
    } else {
        $pid = $db->insert(
            "INSERT INTO products (product_name, slug, category_id, description, short_description,
             price, cost_price, discount_percentage, stock_quantity, sku, is_featured, is_new, is_active,
             status, meta_title, meta_description)
             VALUES (?,?,?,?,?,?,?,0,?,?,?,?,1,'published',?,?)",
            [
                $p['name'], $p['slug'], $p['category_id'],
                $p['description'], $p['short_description'],
                $p['price'], $p['cost_price'] ?? 0,
                $p['stock_quantity'], $p['sku'],
                (int)($p['is_featured'] ?? 0), (int)($p['is_new'] ?? 1),
                $p['meta_title'] ?? $p['name'] . ' | Mithooos',
                $p['meta_description'] ?? $p['short_description'],
            ]
        );
        $action = "Created";
    }

    // Upsert inventory
    $invExist = $db->fetchOne("SELECT id FROM inventory_items WHERE product_id = ? AND variant_id IS NULL", [$pid]);
    if ($invExist) {
        $db->execute("UPDATE inventory_items SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE product_id = ? AND variant_id IS NULL",
            [$p['stock_quantity'], $pid]);
    } else {
        $db->execute("INSERT OR IGNORE INTO inventory_items (product_id, variant_id, quantity, reserved_quantity) VALUES (?,NULL,?,0)",
            [$pid, $p['stock_quantity']]);
    }

    // Variants
    if (!empty($variants) && $action === 'Created') {
        foreach ($variants as $v) {
            $vid = $db->insert(
                "INSERT INTO product_variants (product_id, sku, variant_name, size, price, stock_quantity, is_active) VALUES (?,?,?,?,?,?,1)",
                [$pid, $v['sku'], $v['name'], $v['size'], $v['price'], $v['stock']]
            );
            $db->execute("INSERT OR IGNORE INTO inventory_items (product_id, variant_id, quantity, reserved_quantity) VALUES (?,?,?,0)",
                [$pid, $vid, $v['stock']]);
        }
    }

    return "$action product: {$p['name']} (id=$pid)";
}

// ── 4. Seed products ─────────────────────────────────────────

// Sindhi Topi — update existing to 2500
$results[] = seedProduct($db, [
    'name'              => 'Sindhi Topi',
    'slug'              => 'sindhi-topi',
    'category_id'       => $catMap['sindhi-topi'],
    'description'       => 'The iconic Sindhi Topi — a traditional cap that represents the rich cultural heritage of Sindh. Each piece is hand-crafted with intricate embroidery and mirror work, making it a timeless symbol of Sindhi identity. Perfect for cultural events, Eid, and everyday wear.',
    'short_description' => 'Traditional Sindhi cap with intricate embroidery and mirror work. A symbol of Sindhi heritage.',
    'price'             => 2500,
    'cost_price'        => 1500,
    'stock_quantity'    => 50,
    'sku'               => 'MTH-TOP-001',
    'is_featured'       => 1,
    'is_new'            => 1,
    'meta_title'        => 'Sindhi Topi — Traditional Sindhi Cap | Mithooos',
    'meta_description'  => 'Buy authentic handcrafted Sindhi Topi online. Traditional cap with mirror work and embroidery.',
]);

// Ajrak
$results[] = seedProduct($db, [
    'name'              => 'Sindhi Ajrak',
    'slug'              => 'sindhi-ajrak',
    'category_id'       => $catMap['ajrak'],
    'description'       => 'Authentic Sindhi Ajrak — a masterpiece of block printing passed down through generations. Features bold geometric patterns in deep indigo, crimson, and earthy tones using natural dyes. Each Ajrak tells a story of Sindhi craftsmanship and cultural pride. Versatile as a shawl, turban, or wall art.',
    'short_description' => 'Authentic hand block-printed Sindhi Ajrak with geometric patterns and natural dyes.',
    'price'             => 2500,
    'cost_price'        => 1400,
    'stock_quantity'    => 30,
    'sku'               => 'MTH-AJK-001',
    'is_featured'       => 1,
    'is_new'            => 1,
    'meta_title'        => 'Sindhi Ajrak — Authentic Block-Printed Shawl | Mithooos',
    'meta_description'  => 'Buy original Sindhi Ajrak online. Hand block-printed with natural dyes — a true piece of Sindhi heritage.',
]);

// Sindhi Kajoor (Dates) — weight variants
$results[] = seedProduct($db, [
    'name'              => 'Sindhi Kajoor (Dates)',
    'slug'              => 'sindhi-kajoor',
    'category_id'       => $catMap['sindhi-kajoor'],
    'description'       => 'Premium Sindhi Kajoor (Dates) — naturally sweet, hand-picked from the fertile lands of Sindh. Rich in nutrients, naturally preserved with no additives. A perfect gift and a healthy snack enjoyed year-round. Price is per kilogram.',
    'short_description' => 'Premium naturally sweet Sindhi Kajoor (Dates). Hand-picked, no additives. Price per kg.',
    'price'             => 1500,
    'cost_price'        => 900,
    'stock_quantity'    => 100,
    'sku'               => 'MTH-DAT-001',
    'is_featured'       => 1,
    'is_new'            => 1,
    'meta_title'        => 'Sindhi Kajoor (Dates) — Fresh Premium Dates | Mithooos',
    'meta_description'  => 'Buy fresh Sindhi Kajoor online. Premium quality dates from Sindh at PKR 1500/kg.',
], [
    ['sku' => 'MTH-DAT-500G', 'name' => '500g Pack',  'size' => '500g',  'price' => 750,  'stock' => 40],
    ['sku' => 'MTH-DAT-1KG',  'name' => '1 Kg Pack',  'size' => '1 Kg',  'price' => 1500, 'stock' => 40],
    ['sku' => 'MTH-DAT-2KG',  'name' => '2 Kg Pack',  'size' => '2 Kg',  'price' => 2800, 'stock' => 20],
]);

// Achaar — weight variants
$results[] = seedProduct($db, [
    'name'              => 'Sindhi Achaar (Pickle)',
    'slug'              => 'sindhi-achaar',
    'category_id'       => $catMap['achaar'],
    'description'       => 'Traditional Sindhi Achaar (Pickle) — made with authentic family recipes using fresh seasonal vegetables, aromatic spices, and mustard oil. Bold, tangy, and deeply flavorful. The perfect accompaniment to every Sindhi meal. Made fresh in small batches. Price is per kilogram.',
    'short_description' => 'Authentic homemade Sindhi Achaar made with fresh vegetables and traditional spices. Price per kg.',
    'price'             => 1000,
    'cost_price'        => 500,
    'stock_quantity'    => 60,
    'sku'               => 'MTH-ACH-001',
    'is_featured'       => 0,
    'is_new'            => 1,
    'meta_title'        => 'Sindhi Achaar (Pickle) — Homemade Traditional Pickle | Mithooos',
    'meta_description'  => 'Buy authentic Sindhi Achaar online. Homemade pickle with traditional spices at PKR 1000/kg.',
], [
    ['sku' => 'MTH-ACH-500G', 'name' => '500g Jar',  'size' => '500g',  'price' => 500,  'stock' => 25],
    ['sku' => 'MTH-ACH-1KG',  'name' => '1 Kg Jar',  'size' => '1 Kg',  'price' => 1000, 'stock' => 25],
    ['sku' => 'MTH-ACH-2KG',  'name' => '2 Kg Jar',  'size' => '2 Kg',  'price' => 1900, 'stock' => 10],
]);

// Bedsheet + Curtains Set
$results[] = seedProduct($db, [
    'name'              => 'Bedsheet + 2 Curtains Set',
    'slug'              => 'bedsheet-curtains-set',
    'category_id'       => $catMap['home-textiles'],
    'description'       => 'Complete home textile set featuring a premium double-bed bedsheet paired with 2 matching curtains. Crafted from soft, durable fabric with vibrant Sindhi-inspired prints and patterns. Adds warmth, color, and cultural elegance to your bedroom. Machine washable.',
    'short_description' => 'Premium double-bed bedsheet with 2 matching curtains. Soft fabric with Sindhi-inspired prints.',
    'price'             => 2700,
    'cost_price'        => 1600,
    'stock_quantity'    => 25,
    'sku'               => 'MTH-BED-001',
    'is_featured'       => 0,
    'is_new'            => 1,
    'meta_title'        => 'Bedsheet + 2 Curtains Set — Sindhi Home Textiles | Mithooos',
    'meta_description'  => 'Buy premium Sindhi-inspired bedsheet and curtain set online. Double bed size with matching prints.',
]);

// Comforter
$results[] = seedProduct($db, [
    'name'              => 'Sindhi Comforter',
    'slug'              => 'sindhi-comforter',
    'category_id'       => $catMap['home-textiles'],
    'description'       => 'Luxuriously soft Sindhi Comforter with beautiful Ajrak-inspired patterns. Filled with premium microfiber for warmth without the weight. Reversible design with rich cultural motifs on one side and a solid complementary color on the other. Perfect for all seasons.',
    'short_description' => 'Premium microfiber comforter with Ajrak-inspired patterns. Warm, soft, and reversible.',
    'price'             => 2500,
    'cost_price'        => 1500,
    'stock_quantity'    => 20,
    'sku'               => 'MTH-COM-001',
    'is_featured'       => 0,
    'is_new'            => 1,
    'meta_title'        => 'Sindhi Comforter — Ajrak-Inspired Bedding | Mithooos',
    'meta_description'  => 'Buy premium Sindhi comforter online. Microfiber filled with beautiful Ajrak patterns.',
]);

// ── 5. Return results ─────────────────────────────────────────
Response::success(['seeded' => count($results), 'log' => $results], 'Seed complete.');
