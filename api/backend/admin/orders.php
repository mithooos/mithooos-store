<?php
$om = new Order($db);
if ($method === 'GET') {
    if ($id) {
        $o = $om->findById((int)$id);
        if (!$o) { Response::error('Order not found.',404); return; }
        Response::success($o);
    } else {
        ['limit'=>$limit,'page'=>$page,'offset'=>$offset] = Pagination::params($query);
        ['rows'=>$rows,'total'=>$total] = $om->adminList($query,$limit,$offset);
        // Per-status counts for the page's status-filter tabs — always
        // unfiltered by the current search/status so the tabs remain a
        // stable navigation aid rather than shifting based on what's
        // currently selected.
        $statusCounts = $db->fetchAll('SELECT order_status, COUNT(*) AS cnt FROM orders GROUP BY order_status');
        echo json_encode(['success'=>true,'data'=>$rows,'pagination'=>Pagination::meta($total,$limit,$page),'status_counts'=>$statusCounts]);
    }
    return;
}
if ($method === 'PUT' && $id) {
    $allowed_statuses = ['pending','confirmed','processing','shipped','delivered','cancelled','refunded'];
    $allowed_payment_statuses = ['pending','completed','failed','refunded'];
    
    if (!empty($body['order_status']) && !in_array($body['order_status'],$allowed_statuses)) {
        Response::error('Invalid status.'); return;
    }
    if (!empty($body['payment_status']) && !in_array($body['payment_status'],$allowed_payment_statuses)) {
        Response::error('Invalid payment status.'); return;
    }
    
    $oldOrder = $om->findById((int)$id);
    
    $om->updateStatus((int)$id, $body['order_status']??null, $body['payment_status']??null, $body['tracking_number']??null);
    
    if (!empty($body['order_status']) && $body['order_status'] === 'shipped' && $oldOrder && $oldOrder['order_status'] !== 'shipped') {
        try {
            $newOrder = $om->findById((int)$id);
            if ($newOrder) {
                $emailService = new Email();
                $user = ['first_name' => $newOrder['first_name'], 'email' => $newOrder['email']];
                $emailService->shippingNotification($newOrder, $user);
            }
        } catch (\Throwable $e) {
            error_log('Shipping email failed: ' . $e->getMessage());
        }
    }

    if (!empty($body['admin_notes'])) {
        $db->execute('UPDATE orders SET admin_notes=? WHERE order_id=?',[$body['admin_notes'],(int)$id]);
    }
    Response::success(null,'Order updated.');
    return;
}
Response::error('Method not allowed.',405);
