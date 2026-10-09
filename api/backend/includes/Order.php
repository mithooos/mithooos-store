<?php
class Order {
    public function __construct(private Database $db) {}

    public function create(int $user_id, array $items, array $totals, array $ship, array $bill, string $pay_method, string $shipping_method = 'standard', ?string $payment_proof_url = null, ?string $idempotency_key = null): array {
        $order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $inv = new Inventory($this->db);

        $this->db->beginTransaction();
        try {
            $proofData = null;
            $proofMime = null;
            if ($payment_proof_url && str_starts_with($payment_proof_url, 'data:')) {
                [$meta, $b64] = explode(',', $payment_proof_url, 2);
                $proofMime = str_replace('data:', '', explode(';', $meta)[0]);
                $proofData = base64_decode($b64);
                $payment_proof_url = null; // update later
            }

            $order_id = $this->db->insert(
                'INSERT INTO orders (user_id, order_number, subtotal, tax, shipping_cost, discount_amount, online_discount_amount,
                 total_amount, payment_method, payment_status, shipping_address, billing_address, payment_proof_url, payment_proof_data, payment_proof_mime, notes)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
                [$user_id, $order_number, $totals['subtotal'], $totals['tax'] ?? 0,
                 $totals['shipping'] ?? 0, $totals['discount'] ?? 0, $totals['online_discount'] ?? 0, $totals['total'],
                 $pay_method, 'pending', json_encode($ship), json_encode($bill), $payment_proof_url, $proofData, $proofMime, $idempotency_key]
            );

            if ($proofData) {
                $this->db->execute("UPDATE orders SET payment_proof_url = ? WHERE order_id = ?", ["/backend/api/image.php?type=payment&id={$order_id}", $order_id]);
            }

            foreach ($items as $item) {
                // Reserve stock
                $resId = $inv->reserveStock(
                    (int)$item['product_id'], 
                    !empty($item['variant_id']) ? (int)$item['variant_id'] : null, 
                    $user_id, 
                    null, 
                    (int)$item['quantity']
                );

                $this->db->insert(
                    'INSERT INTO order_items (order_id, product_id, variant_id, product_name, product_sku, price_at_purchase, cost_at_purchase,
                    quantity, item_total)
                     VALUES (?,?,?,?,?,?,?,?,?)',
                    [$order_id, $item['product_id'], $item['variant_id'] ?? null, $item['product_name'], 
                     $item['sku'] ?? null,
                     $item['final_price'] ?? $item['price'], $item['cost_price'] ?? 0, $item['quantity'],
                     ($item['final_price'] ?? $item['price']) * $item['quantity']]
                );
                
                // Fulfill the reservation immediately since the order is placed
                $inv->fulfillReservation($resId, $user_id);
            }

            $this->db->commit();
            return compact('order_id', 'order_number');
        } catch (\Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function findById(int $id, ?int $user_id = null): array|false {
        $sql  = 'SELECT o.*, u.first_name, u.last_name, u.email, u.phone, COALESCE(p.status, o.payment_status) AS payment_status, p.transaction_id FROM orders o JOIN users u ON o.user_id = u.user_id LEFT JOIN payments p ON o.order_id = p.order_id WHERE o.order_id = ?';
        $bind = [$id];
        if ($user_id !== null) { $sql .= ' AND o.user_id = ?'; $bind[] = $user_id; }
        $order = $this->db->fetchOne($sql, $bind);
        if (!$order) return false;
        $order['items'] = $this->db->fetchAll('SELECT * FROM order_items WHERE order_id = ?', [$id]);
        return $order;
    }

    public function findByOrderNumber(string $order_number, ?int $user_id = null): array|false {
        $order_number = trim($order_number);
        if ($order_number === '') return false;

        $sql = 'SELECT o.*, u.first_name, u.last_name, u.email, u.phone, COALESCE(p.status, o.payment_status) AS payment_status, p.transaction_id FROM orders o JOIN users u ON o.user_id = u.user_id LEFT JOIN payments p ON o.order_id = p.order_id WHERE o.order_number = ?';
        $bind = [$order_number];

        if ($user_id !== null) {
            $sql .= ' AND o.user_id = ?';
            $bind[] = $user_id;
        }

        $order = $this->db->fetchOne($sql, $bind);
        if (!$order) return false;

        $order['items'] = $this->db->fetchAll('SELECT * FROM order_items WHERE order_id = ?', [$order['order_id']]);
        return $order;
    }

    public function userOrders(int $user_id, int $limit = 20, int $offset = 0): array {
        return $this->db->fetchAll(
            'SELECT o.*, COALESCE(p.status, o.payment_status) AS payment_status, p.transaction_id, (SELECT COUNT(*) FROM order_items WHERE order_id = o.order_id) AS item_count
             FROM orders o LEFT JOIN payments p ON o.order_id = p.order_id WHERE o.user_id = ? ORDER BY o.created_at DESC LIMIT ? OFFSET ?',
            [$user_id, $limit, $offset]
        );
    }

    public function updateStatus(int $id, ?string $order_status = null, ?string $payment_status = null, ?string $tracking = null): bool {
        $this->db->beginTransaction();
        try {
            $updated = false;
            if ($order_status !== null) {
                $current = $this->db->fetchOne('SELECT order_status FROM orders WHERE order_id = ?', [$id]);
                if (!$current) throw new Exception("Order not found");
                
                $valid_transitions = [
                    'pending' => ['confirmed', 'processing', 'cancelled'],
                    'confirmed' => ['processing', 'cancelled'],
                    'processing' => ['shipped', 'cancelled'],
                    'shipped' => ['delivered', 'cancelled', 'refunded'],
                    'delivered' => ['refunded'],
                    'cancelled' => ['refunded'],
                    'refunded' => []
                ];
                
                $curr_status = $current['order_status'];
                if ($curr_status !== $order_status && !in_array($order_status, $valid_transitions[$curr_status] ?? [])) {
                    throw new Exception("Invalid order status transition from {$curr_status} to {$order_status}.");
                }
                
                if ($curr_status !== $order_status) {
                    $this->db->execute('UPDATE orders SET order_status = ?, updated_at = CURRENT_TIMESTAMP WHERE order_id = ?', [$order_status, $id]);
                    $updated = true;
                }
            }
            
            if ($payment_status !== null) {
                $current_pay = $this->db->fetchOne('SELECT payment_status FROM orders WHERE order_id = ?', [$id]);
                if ($current_pay) {
                    $curr_pay_status = $current_pay['payment_status'];
                    $valid_pay_transitions = [
                        'pending' => ['completed', 'failed'],
                        'completed' => ['refunded'],
                        'failed' => [],
                        'refunded' => []
                    ];
                    
                    if ($curr_pay_status !== $payment_status && !in_array($payment_status, $valid_pay_transitions[$curr_pay_status] ?? [])) {
                        throw new Exception("Invalid payment status transition from {$curr_pay_status} to {$payment_status}.");
                    }
                    
                    if ($curr_pay_status !== $payment_status) {
                        $this->db->execute('UPDATE orders SET payment_status = ?, updated_at = CURRENT_TIMESTAMP WHERE order_id = ?', [$payment_status, $id]);
                        $this->db->execute('UPDATE payments SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE order_id = ?', [$payment_status, $id]);
                        $updated = true;
                    }
                }
            }
            
            $this->db->commit();
            return $updated;
        } catch (\Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function adminList(array $f = [], int $limit = 20, int $offset = 0): array {
        $where = ['1=1']; $bind = [];
        if (!empty($f['status']) && $f['status'] !== 'all')  { $where[] = 'o.order_status = ?';  $bind[] = $f['status']; }
        if (!empty($f['payment_status']) && $f['payment_status'] !== 'all') { 
            $where[] = 'COALESCE(p.status, o.payment_status) = ?'; $bind[] = $f['payment_status'];
            if ($f['payment_status'] === 'pending') {
                $where[] = "o.order_status != 'cancelled'";
            }
        }
        if (!empty($f['payment_method']) && $f['payment_method'] !== 'all') { $where[] = 'o.payment_method = ?'; $bind[] = $f['payment_method']; }
        if (!empty($f['search']))  { $where[] = '(o.order_number LIKE ? OR u.email LIKE ?)'; $s = "%{$f['search']}%"; $bind[] = $s; $bind[] = $s; }
        $w     = implode(' AND ', $where);
        
        $countSql = "SELECT COUNT(*) FROM orders o JOIN users u ON o.user_id = u.user_id LEFT JOIN payments p ON o.order_id = p.order_id WHERE $w";
        $total = (int)$this->db->fetchColumn($countSql, $bind);
        
        $sql = "SELECT o.*, u.first_name, u.last_name, u.email, COALESCE(p.status, o.payment_status) AS payment_status, p.transaction_id,
                (SELECT COUNT(*) FROM order_items WHERE order_id = o.order_id) AS item_count
                FROM orders o JOIN users u ON o.user_id = u.user_id 
                LEFT JOIN payments p ON o.order_id = p.order_id
                WHERE $w ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
        
        $rows  = $this->db->fetchAll($sql, [...$bind, $limit, $offset]);
        
        // Ensure default payment_status if missing
        foreach ($rows as &$row) {
            if (!isset($row['payment_status'])) {
                $row['payment_status'] = 'pending';
            }
        }
        return compact('rows', 'total');
    }
}
