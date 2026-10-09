<?php
if ($method === 'GET') {
    if ($id) {
        $c = is_numeric($id) ? 
            $db->fetchOne('SELECT * FROM collections WHERE collection_id = ? AND is_active = TRUE', [(int)$id]) : 
            $db->fetchOne('SELECT * FROM collections WHERE slug = ? AND is_active = TRUE', [$id]);
        if (!$c) { Response::error('Collection not found.', 404); return; }
        
        // Fetch products in this collection
        ['limit' => $limit, 'page' => $page, 'offset' => $offset] = Pagination::params($query);
        $total = (int)$db->fetchColumn('SELECT COUNT(*) FROM product_collections pc JOIN products p ON pc.product_id = p.product_id WHERE pc.collection_id = ? AND p.status = \'published\' AND p.is_active = TRUE', [$c['collection_id']]);
        $rows = $db->fetchAll("
            SELECT p.product_id, p.product_name, p.slug,
                   ROUND(p.price - (p.price * p.discount_percentage / 100), 2) AS final_price, p.rating,
                   (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = TRUE LIMIT 1) AS image
            FROM product_collections pc
            JOIN products p ON pc.product_id = p.product_id
            WHERE pc.collection_id = ? AND p.status = 'published' AND p.is_active = TRUE
            ORDER BY p.created_at DESC LIMIT ? OFFSET ?", 
            [$c['collection_id'], $limit, $offset]
        );
        $c['products'] = $rows;
        $c['pagination'] = Pagination::meta($total, $limit, $page);
        Response::success($c);
    } else {
        $rows = $db->fetchAll('SELECT * FROM collections WHERE is_active = TRUE ORDER BY collection_name');
        Response::success($rows);
    }
    return;
}

if ($method === 'POST') {
    $auth->requireAdmin();
    $v = Validator::make($body)->required('collection_name');
    if ($v->fails()) { Response::error('Validation failed.', 422, $v->errors()); return; }
    
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $body['collection_name']));
    $i = 1; $base = $slug;
    while ($db->fetchOne('SELECT 1 FROM collections WHERE slug = ?', [$slug])) {
        $slug = "$base-" . $i++;
    }
    
    $cid = $db->insert(
        'INSERT INTO collections (collection_name, slug, description, cover_image_url) VALUES (?,?,?,?)',
        [$body['collection_name'], $slug, $body['description'] ?? null, $body['cover_image_url'] ?? null]
    );
    Response::success(['collection_id' => $cid], 'Collection created.', 201);
    return;
}

if ($method === 'PUT' && $id) {
    $auth->requireAdmin();
    $fields = ['updated_at = CURRENT_TIMESTAMP'];
    $bind = [];
    $allowed = ['collection_name', 'description', 'cover_image_url', 'is_active'];
    foreach ($allowed as $col) {
        if (array_key_exists($col, $body)) { $fields[] = "$col = ?"; $bind[] = $body[$col] === '' ? null : $body[$col]; }
    }
    $bind[] = (int)$id;
    $db->execute('UPDATE collections SET ' . implode(', ', $fields) . ' WHERE collection_id = ?', $bind);
    Response::success(null, 'Collection updated.');
    return;
}

if ($method === 'DELETE' && $id) {
    $auth->requireAdmin();
    $db->execute('UPDATE collections SET is_active = FALSE WHERE collection_id = ?', [(int)$id]);
    Response::success(null, 'Collection deleted.');
    return;
}

Response::error('Method not allowed.', 405);
