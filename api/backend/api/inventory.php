<?php
$inv = new Inventory($db);

// Only admins can access the inventory API
$user = $auth->requireAdmin();

if ($method === 'GET') {
    // Basic inventory fetch - fetch all items with low stock logic
    $query = "
        SELECT i.inventory_id, i.product_id, i.variant_id, 
               p.product_name, v.variant_name, p.sku as p_sku, v.sku as v_sku,
               i.available_stock, i.reserved_stock, i.on_hand_stock, i.low_stock_threshold, i.status
        FROM inventory_items i
        JOIN products p ON i.product_id = p.product_id
        LEFT JOIN product_variants v ON i.variant_id = v.variant_id
        ORDER BY i.product_id, i.variant_id
    ";
    
    $items = $db->fetchAll($query);
    Response::success($items);
    return;
}

if ($method === 'POST') {
    if (isset($pathParts[3]) && $pathParts[3] === 'adjust') {
        $v = Validator::make($body)->required('product_id')->required('quantity_change')->required('reason');
        if ($v->fails()) {
            Response::error('Validation failed.', 422, $v->errors());
            return;
        }

        try {
            $inv->adjustStock(
                (int)$body['product_id'], 
                !empty($body['variant_id']) ? (int)$body['variant_id'] : null, 
                (int)$body['quantity_change'], 
                'adjusted', 
                $body['reason'], 
                (int)$user['user_id']
            );
            Response::success(null, 'Inventory adjusted successfully.', 200);
        } catch (Exception $e) {
            Response::error($e->getMessage(), 400);
        }
        return;
    }
}

Response::error('Method not allowed.', 405);
