<?php
class Cart {
    public function __construct(private Database $db, private ?int $user_id = null, private ?string $session_id = null) {}

    private function clause(): array {
        if (!empty($this->user_id)) return ['user_id = ?', [$this->user_id]];
        if (!empty($this->session_id)) return ['session_id = ?', [$this->session_id]];
        throw new RuntimeException('Cart requires user_id or session_id.');
    }

    public function add(int $product_id, int $qty = 1, ?int $variant_id = null): void {
        [$clause, $bind] = $this->clause();
        
        $vClause = $variant_id ? 'AND variant_id = ?' : 'AND variant_id IS NULL';
        $vBind = $variant_id ? [$variant_id] : [];
        
        // Stock check
        $inv = new Inventory($this->db);
        $available = $inv->getAvailableStock($product_id, $variant_id);
        
        $existing = $this->db->fetchOne(
            "SELECT cart_id, quantity FROM carts WHERE product_id = ? $vClause AND $clause",
            [$product_id, ...$vBind, ...$bind]
        );
        if ($existing) {
            $newQty = min($existing['quantity'] + $qty, $available);
            $this->db->execute('UPDATE carts SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE cart_id = ?', [$newQty, $existing['cart_id']]);
        } else {
            $col  = $this->user_id ? 'user_id' : 'session_id';
            $val  = $this->user_id ?? $this->session_id;
            $cappedQty = min($qty, $available);
            if ($cappedQty > 0) {
                $this->db->insert(
                    "INSERT INTO carts (product_id, variant_id, quantity, $col, expires_at) VALUES (?,?,?,?, datetime('now', '+30 days'))",
                    [$product_id, $variant_id, $cappedQty, $val]
                );
            }
        }
    }

    public function items(): array {
        $this->cleanupExpired();
        
        [$clause, $bind] = $this->clause();
        $items = $this->db->fetchAll(
            "SELECT c.*, p.product_name, p.price, p.cost_price,
             ROUND(p.price - (p.price * p.discount_percentage / 100), 2) AS final_price,
             p.discount_percentage, p.stock_quantity, p.sku AS p_sku,
             p.online_discount_enabled, p.online_discount_percentage, p.online_discount_start, p.online_discount_end,
             v.variant_name, v.size, v.color, v.price AS variant_price, v.sku AS v_sku,
             (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) AS image
             FROM carts c 
             JOIN products p ON c.product_id = p.product_id 
             LEFT JOIN product_variants v ON c.variant_id = v.variant_id
             WHERE c.$clause",
            $bind
        );
        
        $inv = new Inventory($this->db);
        foreach ($items as &$item) {
            // Auto-cap quantity to available stock
            $available = $inv->getAvailableStock($item['product_id'], $item['variant_id']);
            if ($item['quantity'] > $available) {
                $item['quantity'] = max($available, 0);
                if ($item['quantity'] === 0) {
                    $this->remove($item['cart_id']);
                } else {
                    $this->db->execute('UPDATE carts SET quantity = ? WHERE cart_id = ?', [$item['quantity'], $item['cart_id']]);
                }
            }
            if ($item['variant_id'] && $item['variant_price'] !== null) {
                // if the variant has a specific absolute price, apply the discount logic to it
                $discount = $item['discount_percentage'];
                $item['price'] = $item['variant_price'];
                $item['final_price'] = round($item['price'] - ($item['price'] * $discount / 100), 2);
            }
        }
        // filter out zero qty (removed items)
        $items = array_filter($items, fn($i) => $i['quantity'] > 0);
        return array_values($items);
    }

    public function update(int $cart_id, int $qty): bool {
        if ($qty <= 0) { return $this->remove($cart_id); }
        
        $item = $this->db->fetchOne('SELECT product_id, variant_id FROM carts WHERE cart_id = ?', [$cart_id]);
        if (!$item) return false;
        
        $inv = new Inventory($this->db);
        $available = $inv->getAvailableStock($item['product_id'], $item['variant_id']);
        $newQty = min($qty, $available);
        
        if ($newQty === 0) return $this->remove($cart_id);
        
        [$clause, $bind] = $this->clause();
        $affected = $this->db->execute("UPDATE carts SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE cart_id = ? AND $clause", [$newQty, $cart_id, ...$bind]);
        return $affected > 0;
    }

    public function remove(int $cart_id): bool {
        [$clause, $bind] = $this->clause();
        $affected = $this->db->execute("DELETE FROM carts WHERE cart_id = ? AND $clause", [$cart_id, ...$bind]);
        return $affected > 0;
    }

    public function clear(): void {
        [$clause, $bind] = $this->clause();
        $this->db->execute("DELETE FROM carts WHERE $clause", $bind);
    }

    public function totals(float $tax_rate = 0.10, ?string $coupon = null, ?string $payment_method = null, string $shipping_method = 'standard'): array {
        $items    = $this->items();
        $subtotal = array_sum(array_map(fn($i) => $i['final_price'] * $i['quantity'], $items));
        $discount = 0;
        $online_discount = 0;

        // Calculate product-level online discounts if a qualifying payment method is selected
        if ($payment_method && $payment_method !== 'cod') {
            $now = time();
            foreach ($items as $i) {
                if (!empty($i['online_discount_enabled']) && $i['online_discount_enabled'] !== 'f' && $i['online_discount_enabled'] !== 'false') {
                    $start = $i['online_discount_start'] ? strtotime($i['online_discount_start']) : null;
                    $end = $i['online_discount_end'] ? strtotime($i['online_discount_end']) : null;
                    if ((!$start || $now >= $start) && (!$end || $now <= $end)) {
                        $online_discount += round(($i['final_price'] * $i['online_discount_percentage'] / 100) * $i['quantity'], 2);
                    }
                }
            }
        }

        if ($coupon) {
            $c = $this->db->fetchOne(
                'SELECT * FROM coupons WHERE code = ? AND is_active = 1 AND (valid_until IS NULL OR valid_until > CURRENT_TIMESTAMP) AND (max_usage = -1 OR used_count < max_usage)',
                [strtoupper($coupon)]
            );
            if ($c && $subtotal >= $c['minimum_purchase']) {
                $discount = $c['discount_type'] === 'percentage'
                    ? round($subtotal * $c['discount_value'] / 100, 2)
                    : min($c['discount_value'], $subtotal);
            }
        }

        $total_discount = $discount + $online_discount;
        
        $setting = $this->db->fetchOne("SELECT setting_val FROM settings WHERE setting_key = 'free_shipping_threshold'");
        $threshold = $setting && is_numeric($setting['setting_val']) ? (float)$setting['setting_val'] : 5000;
        
        $shipping = 0;
        if ($shipping_method === 'express') {
            $shipping = 500; // PKR 500
        } elseif ($shipping_method === 'overnight') {
            $shipping = 1000; // PKR 1000
        } else {
            // standard
            $shipping = ($subtotal - $total_discount) >= $threshold ? 0 : 250; // PKR 250 standard
        }
        
        $tax      = round(($subtotal - $total_discount) * $tax_rate, 2);
        $total    = round($subtotal - $total_discount + $shipping + $tax, 2);

        return compact('items', 'subtotal', 'discount', 'online_discount', 'shipping', 'tax', 'total');
    }

    /* Merge guest cart into user cart on login */
    public function mergeSession(string $session_id): void {
        if (!$this->user_id) return;
        $guest_items = $this->db->fetchAll('SELECT * FROM carts WHERE session_id = ?', [$session_id]);
        foreach ($guest_items as $item) {
            $this->add($item['product_id'], $item['quantity'], $item['variant_id']);
        }
        $this->db->execute('DELETE FROM carts WHERE session_id = ?', [$session_id]);
    }
    
    public function cleanupExpired(): void {
        $this->db->execute("DELETE FROM carts WHERE expires_at < CURRENT_TIMESTAMP");
    }
}
