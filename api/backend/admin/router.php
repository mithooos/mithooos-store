<?php
// Admin API router  /admin/{resource}/{id}
$admin_user = $auth->requireAdmin();

match (true) {
    /* Dashboard stats */
    $sub === 'dashboard' && $method === 'GET' => (function() use ($db) {
        $stats = [
            'revenue_month'   => (float)$db->fetchColumn("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE strftime('%Y-%m', created_at)=strftime('%Y-%m', 'now') AND order_status != 'cancelled' AND payment_status != 'refunded'"),
            'profit_month'    => (float)$db->fetchColumn("SELECT (SELECT COALESCE(SUM(total_amount - shipping_cost - tax), 0) FROM orders WHERE strftime('%Y-%m', created_at)=strftime('%Y-%m', 'now') AND order_status != 'cancelled' AND payment_status != 'refunded') - (SELECT COALESCE(SUM(oi.cost_at_purchase * oi.quantity), 0) FROM order_items oi JOIN orders o ON o.order_id = oi.order_id WHERE strftime('%Y-%m', o.created_at)=strftime('%Y-%m', 'now') AND o.order_status != 'cancelled' AND o.payment_status != 'refunded')"),
            'orders_month'    => (int)$db->fetchColumn("SELECT COUNT(*) FROM orders WHERE strftime('%Y-%m', created_at)=strftime('%Y-%m', 'now')"),
            'total_customers' => (int)$db->fetchColumn("SELECT COUNT(*) FROM users WHERE user_type='customer'"),
            'active_products' => (int)$db->fetchColumn("SELECT COUNT(*) FROM products WHERE is_active=TRUE"),
            'low_stock'       => (int)$db->fetchColumn("SELECT COUNT(*) FROM products WHERE stock_quantity <= 5"),
            'old_screenshots' => (int)$db->fetchColumn("SELECT COUNT(*) FROM orders WHERE payment_proof_url IS NOT NULL AND created_at < datetime('now', '-7 days')"),
        ];
        $recent_orders = $db->fetchAll("SELECT o.*,u.first_name,u.last_name FROM orders o JOIN users u ON o.user_id=u.user_id ORDER BY o.created_at DESC LIMIT 8");
        $top_products  = $db->fetchAll("SELECT p.product_name,SUM(oi.quantity) AS sold,SUM(oi.price_at_purchase * oi.quantity) AS revenue
            FROM order_items oi JOIN products p ON oi.product_id=p.product_id JOIN orders o ON o.order_id=oi.order_id
            WHERE o.order_status != 'cancelled' AND o.payment_status != 'refunded'
            GROUP BY oi.product_id, p.product_name ORDER BY revenue DESC LIMIT 5");
        $order_statuses = $db->fetchAll("SELECT order_status, COUNT(*) AS cnt FROM orders GROUP BY order_status");
        $payment_types = $db->fetchAll("
            SELECT payment_method, 
                   COUNT(*) AS total,
                   SUM(CASE WHEN order_status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                   SUM(CASE WHEN order_status IN ('pending', 'processing', 'shipped') THEN 1 ELSE 0 END) as pending_shipment,
                   SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
            FROM orders 
            GROUP BY payment_method
        ");
        Response::success(compact('stats','recent_orders','top_products','order_statuses','payment_types'));
    })(),

    /* Categories CRUD */
    $sub === 'categories' => (function() use ($method, $id, $body, $db, $auth) {
        require __DIR__ . '/categories.php';
    })(),

    /* Products CRUD */
    $sub === 'products' => (function() use ($method, $id, $body, $query, $db, $auth) {
        require __DIR__ . '/products.php';
    })(),

    /* Orders management */
    $sub === 'orders' => (function() use ($method, $id, $body, $query, $db) {
        require __DIR__ . '/orders.php';
    })(),

    /* Customers */
    $sub === 'customers' => (function() use ($method, $id, $body, $query, $db) {
        require __DIR__ . '/customers.php';
    })(),

    /* Coupons */
    $sub === 'coupons' => (function() use ($method, $id, $body, $db) {
        require __DIR__ . '/coupons.php';
    })(),

    /* Blog */
    $sub === 'blog' => (function() use ($method, $id, $body, $query, $db, $auth) {
        require __DIR__ . '/blog.php';
    })(),

    /* Analytics — period-based revenue chart + category breakdown */
    $sub === 'analytics' && $method === 'GET' => (function() use ($db, $query) {
        $days = max(1, min((int)($query['days'] ?? 30), 365));

        // Daily revenue for the selected window. Note: days with zero
        // orders won't appear as rows at all (this is a plain GROUP BY,
        // not a generated date sequence) — the frontend zero-fills any
        // missing days when building the chart.
        $daily = $db->fetchAll(
            "SELECT DATE(created_at) AS day, COALESCE(SUM(total_amount),0) AS revenue, COUNT(*) AS orders
             FROM orders
             WHERE created_at >= date('now', '-' || (CAST(? AS integer) - 1) || ' days') AND created_at < date('now', '+1 days') AND order_status != 'cancelled' AND payment_status != 'refunded'
             GROUP BY DATE(created_at) ORDER BY day ASC",
            [$days]
        );

        // Revenue by top-level category, for the same window
        $by_category = $db->fetchAll(
            "SELECT COALESCE(parent.category_name, c.category_name) AS category_name,
                    SUM(oi.price_at_purchase * oi.quantity) AS revenue
             FROM order_items oi
             JOIN orders o ON oi.order_id = o.order_id
             JOIN products p ON oi.product_id = p.product_id
             LEFT JOIN categories c ON p.category_id = c.category_id
             LEFT JOIN categories parent ON c.parent_category_id = parent.category_id
             WHERE o.created_at >= date('now', '-' || (CAST(? AS integer) - 1) || ' days') AND o.created_at < date('now', '+1 days') AND o.order_status != 'cancelled' AND o.payment_status != 'refunded'
             GROUP BY COALESCE(parent.category_name, c.category_name)
             ORDER BY revenue DESC",
            [$days]
        );

        // Current-period vs previous-period-of-equal-length, for trend arrows
        $current_revenue = (float)$db->fetchColumn(
            "SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE created_at >= date('now', '-' || (CAST(? AS integer) - 1) || ' days') AND created_at < date('now', '+1 days') AND order_status != 'cancelled' AND payment_status != 'refunded'", [$days]);
        $current_orders = (int)$db->fetchColumn(
            "SELECT COUNT(*) FROM orders WHERE created_at >= date('now', '-' || (CAST(? AS integer) - 1) || ' days') AND created_at < date('now', '+1 days') AND order_status != 'cancelled' AND payment_status != 'refunded'", [$days]);
        $prev_revenue = (float)$db->fetchColumn(
            "SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE created_at >= date('now', '-' || CAST(? AS integer) || ' days') AND created_at < date('now', '-' || (CAST(? AS integer) - 1) || ' days') AND order_status != 'cancelled' AND payment_status != 'refunded'",
            [$days * 2 - 1, $days]);
        $prev_orders = (int)$db->fetchColumn(
            "SELECT COUNT(*) FROM orders WHERE created_at >= date('now', '-' || CAST(? AS integer) || ' days') AND created_at < date('now', '-' || (CAST(? AS integer) - 1) || ' days') AND order_status != 'cancelled' AND payment_status != 'refunded'",
            [$days * 2 - 1, $days]);

        Response::success([
            'days' => $days,
            'daily_revenue' => $daily,
            'by_category' => $by_category,
            'current' => ['revenue' => $current_revenue, 'orders' => $current_orders],
            'previous' => ['revenue' => $prev_revenue, 'orders' => $prev_orders],
        ]);
    })(),

    /* Store settings — backed by the `settings` key/value table (see
       backend/config/schema.sql). Only these specific, seeded keys are
       readable/writable through this endpoint; everything else on the
       admin Settings page (Stripe secret keys, SMTP credentials, JWT
       secret, session timeout, 2FA) is genuine .env-level infrastructure
       config or an unimplemented feature, and is intentionally NOT exposed
       here — see the Future Production Configuration note in .env.example. */
    $sub === 'settings' => (function() use ($method, $body, $db) {
        $editable = ['store_name', 'store_email', 'currency', 'tax_rate', 'items_per_page', 'free_shipping_threshold', 'default_payment'];

        if ($method === 'GET') {
            $rows = $db->fetchAll('SELECT setting_key, setting_val, setting_group FROM settings WHERE setting_key IN (' . implode(',', array_fill(0, count($editable), '?')) . ')', $editable);
            $out = [];
            foreach ($rows as $r) { $out[$r['setting_key']] = $r['setting_val']; }
            Response::success($out);
            return;
        }

        if ($method === 'PUT') {
            foreach ($body as $key => $val) {
                if (!in_array($key, $editable, true)) continue; // silently ignore unknown/disallowed keys
                $db->execute(
                    'INSERT INTO settings (setting_key, setting_val) VALUES (?, ?) ON CONFLICT (setting_key) DO UPDATE SET setting_val = EXCLUDED.setting_val',
                    [$key, (string)$val]
                );
            }
            Response::success(null, 'Settings updated.');
            return;
        }

        Response::error('Method not allowed.', 405);
    })(),

    /* Export Weekly Report */
    $sub === 'export-weekly' && $method === 'GET' => (function() use ($db) {
        date_default_timezone_set('Asia/Karachi');
        $orders = $db->fetchAll("
            SELECT o.order_id, o.order_number, u.first_name, u.last_name, 
                   o.subtotal, o.discount_amount, o.online_discount_amount, o.tax, o.shipping_cost, o.total_amount, 
                   o.payment_method, o.order_status, o.created_at,
                   (SELECT COALESCE(SUM(COALESCE(NULLIF(oi.cost_at_purchase, 0), p.cost_price, 0) * oi.quantity), 0) FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = o.order_id) AS total_cost
            FROM orders o 
            JOIN users u ON o.user_id = u.user_id 
            WHERE o.created_at >= datetime('now', '-7 days') 
            ORDER BY o.created_at DESC
        ");
        $customers = $db->fetchAll("SELECT user_id, first_name, last_name, email, phone, created_at FROM users WHERE user_type = 'customer' AND created_at >= datetime('now', '-7 days') ORDER BY created_at DESC");
        $inventory = $db->fetchAll("SELECT product_id, product_name, sku, cost_price, price, stock_quantity FROM products WHERE is_active = TRUE ORDER BY stock_quantity ASC");
        
        $out = fopen('php://output', 'w');
        
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="weekly_report_'.date('Y-m-d').'.html"');
        
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mithooos Weekly Report - '.date('Y-m-d').'</title>
    <style>
        body { font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f3f4f6; color: #1f2937; margin: 0; padding: 40px 20px; }
        .container { max-width: 1300px; margin: 0 auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); overflow: hidden; padding: 40px; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #e5e7eb; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #111827; font-size: 32px; letter-spacing: -0.025em; }
        .header p { margin: 8px 0 0; color: #6b7280; font-size: 16px; }
        
        h2 { color: #4f46e5; font-size: 20px; margin: 40px 0 16px; border-left: 4px solid #4f46e5; padding-left: 12px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 14px; }
        th { background: #f8fafc; color: #475569; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 16px; text-align: left; border-bottom: 2px solid #e2e8f0; }
        td { padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #334155; }
        tr:nth-child(even) td { background-color: #f8fafc; }
        
        .badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .badge.delivered { background: #d1fae5; color: #065f46; }
        .badge.pending { background: #fef3c7; color: #92400e; }
        .badge.processing { background: #dbeafe; color: #1e40af; }
        .badge.cancelled { background: #fee2e2; color: #991b1b; }
        .badge.easypaisa { background: #e0e7ff; color: #3730a3; }
        .badge.cod { background: #f3f4f6; color: #1f2937; }
        
        .low-stock { color: #dc2626; font-weight: bold; }
        .profit { color: #059669; font-weight: 700; }
        .expense { color: #dc2626; }
        
        .currency { font-family: monospace; font-size: 15px; }
        
        /* Print styles */
        @media print {
            body { background: #fff; padding: 0; }
            .container { box-shadow: none; padding: 0; max-width: 100%; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Mithooos Weekly Report</h1>
        <p>Generated on <strong>'.date('F j, Y, g:i a').'</strong></p>
    </div>

    <!-- SECTION 1: ORDERS -->
    <h2>Weekly Orders</h2>
    <table>
        <thead>
            <tr>
                <th>Order No.</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Subtotal</th>
                <th>Discount</th>
                <th>Tax</th>
                <th>Shipping</th>
                <th>Total Sale</th>
                <th>Net Sale (Excl. Tax/Ship)</th>
                <th>Order Profit</th>
                <th>Method</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($orders as $o) {
            $statusClass = strtolower($o['order_status']);
            $methodClass = strtolower($o['payment_method']);
            $total_discount = $o['discount_amount'] + $o['online_discount_amount'];
            
            // Net Sale is the actual revenue from products (Subtotal - Discount)
            $net_sale = (float)$o['subtotal'] - (float)$total_discount;
            
            // True Profit = Net Sale - Cost of goods
            $profit = $net_sale - (float)$o['total_cost'];
            
            $html .= '<tr>
                <td><strong>'.$o['order_number'].'</strong></td>
                <td>'.htmlspecialchars($o['first_name'] . ' ' . $o['last_name']).'</td>
                <td>'.date('M d, Y', strtotime($o['created_at'])).'</td>
                <td class="currency">Rs. '.number_format($o['subtotal'], 2).'</td>
                <td class="currency">Rs. '.number_format($total_discount, 2).'</td>
                <td class="currency expense">+Rs. '.number_format($o['tax'], 2).'</td>
                <td class="currency expense">+Rs. '.number_format($o['shipping_cost'], 2).'</td>
                <td class="currency"><strong>Rs. '.number_format($o['total_amount'], 2).'</strong></td>
                <td class="currency">Rs. '.number_format($net_sale, 2).'</td>
                <td class="currency profit">Rs. '.number_format($profit, 2).'</td>
                <td><span class="badge '.$methodClass.'">'.strtoupper($o['payment_method']).'</span></td>
                <td><span class="badge '.$statusClass.'">'.strtoupper($o['order_status']).'</span></td>
            </tr>';
        }

        $html .= '</tbody>
    </table>

    <!-- SECTION 2: CUSTOMERS -->
    <h2>New Customers (Last 7 Days)</h2>
    <table>
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Email Address</th>
                <th>Phone Number</th>
                <th>Joined Date</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($customers as $c) {
            $html .= '<tr>
                <td><strong>'.htmlspecialchars($c['first_name'] . ' ' . $c['last_name']).'</strong></td>
                <td>'.htmlspecialchars($c['email']).'</td>
                <td>'.htmlspecialchars($c['phone'] ?? 'N/A').'</td>
                <td>'.date('M d, Y h:i A', strtotime($c['created_at'])).'</td>
            </tr>';
        }

        $html .= '</tbody>
    </table>

    <!-- SECTION 3: INVENTORY -->
    <h2>Inventory Snapshot</h2>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>SKU</th>
                <th>Cost Price</th>
                <th>Selling Price</th>
                <th>Unit Profit</th>
                <th>Current Stock</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($inventory as $i) {
            $stock = $i['stock_quantity'];
            $stockHtml = ($stock <= 5) ? '<span class="low-stock">'.$stock.' (LOW STOCK)</span>' : $stock;
            $unit_profit = (float)$i['price'] - (float)$i['cost_price'];
            
            $html .= '<tr>
                <td><strong>'.htmlspecialchars($i['product_name']).'</strong></td>
                <td>'.htmlspecialchars($i['sku']).'</td>
                <td class="currency">Rs. '.number_format($i['cost_price'], 2).'</td>
                <td class="currency">Rs. '.number_format($i['price'], 2).'</td>
                <td class="currency profit">Rs. '.number_format($unit_profit, 2).'</td>
                <td>'.$stockHtml.'</td>
            </tr>';
        }

        $html .= '</tbody>
    </table>
</div>
</body>
</html>';
        
        echo $html;
        exit;
    })(),

    /* Cleanup Screenshots */
    $sub === 'cleanup-screenshots' && $method === 'POST' => (function() use ($db) {
        $old_orders = $db->fetchAll("SELECT order_id, payment_proof_url FROM orders WHERE payment_proof_url IS NOT NULL AND created_at < datetime('now', '-7 days')");
        
        $deleted = 0;
        foreach ($old_orders as $o) {
            $url = $o['payment_proof_url'];
            $filename = basename($url);
            $filepath = UPLOAD_DIR . '/payments/' . $filename;
            if (file_exists($filepath)) {
                @unlink($filepath);
            }
            $db->execute("UPDATE orders SET payment_proof_url = NULL WHERE order_id = ?", [$o['order_id']]);
            $deleted++;
        }
        Response::success(compact('deleted'), "Successfully deleted $deleted old screenshots.");
    })(),

    default => Response::error("Admin endpoint '$sub' not found.", 404),
};
