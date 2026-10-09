<?php
$product = new Product($db);

if ($method === 'GET') {
    if ($id) {
        $row = $db->fetchOne(
            'SELECT p.*, ROUND(p.price - (p.price * p.discount_percentage / 100), 2) AS final_price, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id WHERE p.product_id = ?',
            [(int)$id]
        );
        if (!$row) { Response::error('Product not found.',404); return; }
        $row['images'] = $db->fetchAll(
            'SELECT image_id, product_id, image_url, image_mime, alt_text, display_order, is_primary, created_at FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, display_order',
            [(int)$id]
        );
        Response::success($row);
        return;
    }
    ['limit'=>$limit,'page'=>$page,'offset'=>$offset] = Pagination::params($query);
    $where = ['1=1']; $bind = [];
    if (!empty($query['search']))      { $where[] = '(p.product_name LIKE ? OR p.sku LIKE ? OR c.category_name LIKE ?)'; $term = "%{$query['search']}%"; array_push($bind, $term, $term, $term); }
    if (!empty($query['category_id'])) { $where[] = 'p.category_id = ?'; $bind[] = (int)$query['category_id']; }
    if (isset($query['is_active']))    { $where[] = 'p.is_active = ?'; $bind[] = filter_var($query['is_active'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0; }
    $w     = implode(' AND ', $where);
    $sort = match ($query['sort'] ?? 'newest') {
        'oldest' => 'p.created_at ASC',
        'price_asc' => 'ROUND(p.price - (p.price * p.discount_percentage / 100), 2) ASC',
        'price_desc' => 'ROUND(p.price - (p.price * p.discount_percentage / 100), 2) DESC',
        'name_asc' => 'p.product_name ASC',
        'name_desc' => 'p.product_name DESC',
        'stock_asc' => 'p.stock_quantity ASC',
        'stock_desc' => 'p.stock_quantity DESC',
        default => 'p.created_at DESC',
    };
    $total = (int)$db->fetchColumn("SELECT COUNT(*) FROM products p LEFT JOIN categories c ON p.category_id = c.category_id WHERE $w", $bind);
    $rows  = $db->fetchAll(
        "SELECT p.*, ROUND(p.price - (p.price * p.discount_percentage / 100), 2) AS final_price, c.category_name,
            COALESCE(review_stats.average_rating, 0) AS average_rating,
            COALESCE(review_stats.review_count, 0) AS review_count,
            (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) AS image
         FROM products p
         LEFT JOIN categories c ON p.category_id=c.category_id
         LEFT JOIN (
             SELECT product_id, AVG(rating) AS average_rating, COUNT(*) AS review_count
             FROM product_reviews WHERE status = 'approved' GROUP BY product_id
         ) review_stats ON review_stats.product_id = p.product_id
         WHERE $w ORDER BY $sort LIMIT ? OFFSET ?",
        [...$bind, $limit, $offset]
    );
    // Catalog-wide counts (unfiltered) for the stat cards at the top of the
    // admin products page — separate from $total above, which reflects the
    // current search/filter.
    $stats = [
        'total_products'  => (int)$db->fetchColumn('SELECT COUNT(*) FROM products'),
        'active_products' => (int)$db->fetchColumn('SELECT COUNT(*) FROM products WHERE is_active = 1'),
        'low_stock'       => (int)$db->fetchColumn('SELECT COUNT(*) FROM products WHERE stock_quantity <= 5'),
        'on_sale'         => (int)$db->fetchColumn('SELECT COUNT(*) FROM products WHERE is_sale = 1'),
    ];
    echo json_encode(['success'=>true,'data'=>$rows,'pagination'=>Pagination::meta($total,$limit,$page),'stats'=>$stats]);
    return;
}
if ($method === 'POST') {
    $v=Validator::make($body)->required('product_name')->required('category_id')->required('price')->positive('price')->required('description');
    if (isset($body['cost_price']) && is_numeric($body['cost_price']) && (float)$body['cost_price'] < 0) {
        $v->addError('cost_price', 'Cost price cannot be negative.');
    }
    if (isset($body['online_discount_percentage']) && is_numeric($body['online_discount_percentage'])) {
        $odp = (float)$body['online_discount_percentage'];
        if ($odp < 0 || $odp > 100) $v->addError('online_discount_percentage', 'Discount must be between 0 and 100.');
    }
    if ($v->fails()) { Response::error('Validation failed.',422,$v->errors()); return; }

    $db->beginTransaction();
    try {
        $pid = $product->create($body);
        if (!empty($body['images'])) {
            foreach ($body['images'] as $i => $img) {
                $proofData = null;
                $proofMime = null;
                $imgUrl = $img['url'];
                if (str_starts_with($imgUrl, 'data:')) {
                    if (!preg_match('#^data:(image/(?:jpeg|png|webp));base64,[A-Za-z0-9+/]*={0,2}$#D', $imgUrl, $matches)) {
                        throw new RuntimeException('Invalid product image data.');
                    }
                    $proofMime = $matches[1];
                    $proofData = $imgUrl;
                    $imgUrl = null;
                }
                $imgId = $db->insert('INSERT INTO product_images(product_id,image_url,image_data,image_mime,alt_text,is_primary,display_order) VALUES(?,?,?,?,?,?,?)', [$pid,$imgUrl,$proofData,$proofMime,$img['alt']??null,$i===0?1:0,$i]);
                if ($proofData) $db->execute("UPDATE product_images SET image_url = ? WHERE image_id = ?", ["/backend/api/image.php?type=product&id={$imgId}", $imgId]);
            }
        }
        $db->commit();
    } catch (Throwable $e) {
        $db->rollBack();
        throw $e;
    }
    Response::success(['product_id'=>$pid],'Product created.',201);
    return;
}
if ($method === 'PUT' && $id) {
    $db->beginTransaction();
    try {
        if (!$product->update((int)$id, $body)) {
            throw new RuntimeException('Product was not updated.');
        }
        if (!empty($body['images'])) {
            $db->execute('UPDATE product_images SET is_primary = 0, display_order = display_order + ? WHERE product_id = ?', [count($body['images']), (int)$id]);
            foreach ($body['images'] as $i => $img) {
                $proofData = null;
                $proofMime = null;
                $imgUrl = $img['url'];
                if (str_starts_with($imgUrl, 'data:')) {
                    if (!preg_match('#^data:(image/(?:jpeg|png|webp));base64,[A-Za-z0-9+/]*={0,2}$#D', $imgUrl, $matches)) {
                        throw new RuntimeException('Invalid product image data.');
                    }
                    $proofMime = $matches[1];
                    $proofData = $imgUrl;
                    $imgUrl = null;
                }
                $imgId = $db->insert('INSERT INTO product_images(product_id,image_url,image_data,image_mime,alt_text,is_primary,display_order) VALUES(?,?,?,?,?,?,?)', [(int)$id, $imgUrl, $proofData, $proofMime, $img['alt'] ?? null, $i === 0 ? 1 : 0, $i]);
                if ($proofData) $db->execute("UPDATE product_images SET image_url = ? WHERE image_id = ?", ["/backend/api/image.php?type=product&id={$imgId}", $imgId]);
            }
        }
        $db->commit();
    } catch (Throwable $e) {
        $db->rollBack();
        throw $e;
    }
    Response::success(null,'Product updated.');
    return;
}
if ($method === 'DELETE' && $id) { $product->delete((int)$id); Response::success(null,'Product deleted.'); return; }
Response::error('Method not allowed.',405);
