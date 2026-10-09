<?php
if ($method === 'GET') {
    if ($id) {
        $u = $db->fetchOne('SELECT user_id,email,first_name,last_name,phone,user_type,is_active,is_verified,last_login,created_at FROM users WHERE user_id=?',[(int)$id]);
        if (!$u) { Response::error('Customer not found.',404); return; }
        $u['order_count'] = (int)$db->fetchColumn('SELECT COUNT(*) FROM orders WHERE user_id=?',[(int)$id]);
        $u['total_spent'] = (float)$db->fetchColumn("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE user_id=? AND order_status!='cancelled'",[(int)$id]);
        $u['recent_orders'] = $db->fetchAll('SELECT order_id,order_number,total_amount,order_status,created_at FROM orders WHERE user_id=? ORDER BY created_at DESC LIMIT 5',[(int)$id]);
        Response::success($u);
    } else {
        ['limit'=>$limit,'page'=>$page,'offset'=>$offset] = Pagination::params($query);
        $search = $query['search'] ?? '';
        $w = $search ? "WHERE user_type='customer' AND (email LIKE ? OR first_name LIKE ? OR last_name LIKE ?)" : "WHERE user_type='customer'";
        $bind = $search ? ["%$search%","%$search%","%$search%"] : [];
        $total = (int)$db->fetchColumn("SELECT COUNT(*) FROM users $w", $bind);
        $rows  = $db->fetchAll(
            "SELECT user_id,email,first_name,last_name,phone,is_active,created_at,last_login,
             (SELECT COUNT(*) FROM orders WHERE user_id=u.user_id) AS order_count,
             (SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE user_id=u.user_id AND order_status!='cancelled') AS total_spent
             FROM users u $w ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [...$bind,$limit,$offset]
        );
        // Customer-base-wide stats for the page's stat cards — always
        // unfiltered by the current search, so they reflect the whole
        // customer base rather than just the current page/search results.
        $stats = [
            'total_customers'   => (int)$db->fetchColumn("SELECT COUNT(*) FROM users WHERE user_type='customer'"),
            'new_last_30_days'  => (int)$db->fetchColumn("SELECT COUNT(*) FROM users WHERE user_type='customer' AND created_at >= datetime('now', '-30 days')"),
            'avg_order_value'   => (float)$db->fetchColumn("SELECT COALESCE(AVG(total_amount),0) FROM orders WHERE order_status != 'cancelled'"),
            'repeat_buyer_pct'  => (function() use ($db) {
                $withOrders = (int)$db->fetchColumn("SELECT COUNT(DISTINCT user_id) FROM orders WHERE order_status != 'cancelled'");
                if ($withOrders === 0) return 0.0;
                $repeat = (int)$db->fetchColumn("SELECT COUNT(*) FROM (SELECT user_id FROM orders WHERE order_status != 'cancelled' GROUP BY user_id HAVING COUNT(*) > 1) t");
                return round(($repeat / $withOrders) * 100, 1);
            })(),
        ];
        echo json_encode(['success'=>true,'data'=>$rows,'pagination'=>Pagination::meta($total,$limit,$page),'stats'=>$stats]);
    }
    return;
}
if ($method === 'PUT' && $id) {
    $allowed = ['is_active','first_name','last_name','phone'];
    $fields=[]; $bind=[];
    foreach($allowed as $col) { if(array_key_exists($col,$body)){$fields[]="$col=?";$bind[]=$body[$col];} }
    if(empty($fields)){Response::error('Nothing to update.');return;}
    $bind[]=(int)$id;
    $db->execute('UPDATE users SET '.implode(',',$fields).' WHERE user_id=?',$bind);
    Response::success(null,'Customer updated.');
    return;
}
Response::error('Method not allowed.',405);
