<?php
class Inventory {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    /**
     * Get the available stock for a product or variant.
     * Available stock = on_hand_stock - reserved_stock.
     */
    public function getAvailableStock(int $productId, ?int $variantId = null): int {
        $query = 'SELECT (quantity - reserved_quantity) AS available_stock FROM inventory_items WHERE product_id = ?';
        $params = [$productId];

        if ($variantId !== null) {
            $query .= ' AND variant_id = ?';
            $params[] = $variantId;
        } else {
            $query .= ' AND variant_id IS NULL';
        }

        $stock = $this->db->fetchColumn($query, $params);
        return $stock !== false ? (int)$stock : 0;
    }

    /**
     * Initialize inventory for a new product or variant.
     */
    public function initInventory(int $productId, ?int $variantId = null, int $initialStock = 0): void {
        $query = 'SELECT inventory_id FROM inventory_items WHERE product_id = ?';
        $params = [$productId];
        if ($variantId) { $query .= ' AND variant_id = ?'; $params[] = $variantId; } else { $query .= ' AND variant_id IS NULL'; }
        if (!$this->db->fetchOne($query, $params)) {
            $this->db->insert(
                'INSERT INTO inventory_items (product_id, variant_id, quantity) VALUES (?, ?, ?)',
                [$productId, $variantId, $initialStock]
            );
        }
    }

    public function setAbsoluteStock(int $productId, ?int $variantId, int $quantity): void {
        $query = 'SELECT inventory_id FROM inventory_items WHERE product_id = ?';
        $params = [$productId];
        if ($variantId) { $query .= ' AND variant_id = ?'; $params[] = $variantId; } else { $query .= ' AND variant_id IS NULL'; }
        $existing = $this->db->fetchOne($query, $params);
        if ($existing) {
            $this->db->execute('UPDATE inventory_items SET quantity = ? WHERE inventory_id = ?', [$quantity, $existing['inventory_id']]);
        } else {
            $this->initInventory($productId, $variantId, $quantity);
        }
    }

    /**
     * Reserve stock during checkout.
     * Returns reservation_id on success, or throws Exception if not enough stock.
     */
    public function reserveStock(int $productId, ?int $variantId, ?int $userId, ?string $sessionId, int $quantity, int $expiresMinutes = 15): int {
        $this->db->beginTransaction();
        try {
            // Lock the inventory row
            $query = 'SELECT inventory_id, (quantity - reserved_quantity) AS available_stock FROM inventory_items WHERE product_id = ?';
            $params = [$productId];
            if ($variantId) {
                $query .= ' AND variant_id = ?';
                $params[] = $variantId;
            } else {
                $query .= ' AND variant_id IS NULL';
            }
            $query .= ' FOR UPDATE';

            $item = $this->db->fetchOne($query, $params);

            if (!$item || $item['available_stock'] < $quantity) {
                throw new RuntimeException('Insufficient stock available.');
            }

            // Update reserved stock
            $this->db->execute(
                'UPDATE inventory_items SET reserved_quantity = reserved_quantity + ? WHERE inventory_id = ?',
                [$quantity, $item['inventory_id']]
            );

            // Create reservation record
            $expiresAt = date('Y-m-d H:i:s', strtotime("+$expiresMinutes minutes"));
            $resId = $this->db->insert(
                'INSERT INTO inventory_reservations (product_id, variant_id, user_id, session_id, quantity, expires_at) VALUES (?, ?, ?, ?, ?, ?)',
                    [$productId, $variantId, $userId, $sessionId, $quantity, $expiresAt]
            );

            $this->db->commit();
            return (int)$resId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Fulfill a reservation (when order is placed and confirmed).
     */
    public function fulfillReservation(int $reservationId, ?int $userId = null): void {
        $this->db->beginTransaction();
        try {
            $res = $this->db->fetchOne('SELECT * FROM inventory_reservations WHERE reservation_id = ? FOR UPDATE', [$reservationId]);
            if (!$res || $res['status'] !== 'active') {
                throw new RuntimeException('Invalid or inactive reservation.');
            }

            // Find the inventory item
            $query = 'SELECT inventory_id FROM inventory_items WHERE product_id = ?';
            $params = [$res['product_id']];
            if ($res['variant_id']) {
                $query .= ' AND variant_id = ?';
                $params[] = $res['variant_id'];
            } else {
                $query .= ' AND variant_id IS NULL';
            }
            $query .= ' FOR UPDATE';
            $item = $this->db->fetchOne($query, $params);

            if (!$item) {
                throw new RuntimeException('Inventory item not found.');
            }

            // Deduct from reserved_quantity and quantity
            $this->db->execute(
                'UPDATE inventory_items SET reserved_quantity = reserved_quantity - ?, quantity = quantity - ? WHERE inventory_id = ?',
                [$res['quantity'], $res['quantity'], $item['inventory_id']]
            );

            // Log movement
            $this->logMovement($item['inventory_id'], 'sold', -$res['quantity'], 'Order placed', 'reservation', $reservationId, $userId);

            // Mark reservation fulfilled
            $this->db->execute('UPDATE inventory_reservations SET status = ? WHERE reservation_id = ?', ['fulfilled', $reservationId]);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Cancel an active reservation and restore available stock.
     */
    public function cancelReservation(int $reservationId): void {
        $this->db->beginTransaction();
        try {
            $res = $this->db->fetchOne('SELECT * FROM inventory_reservations WHERE reservation_id = ? FOR UPDATE', [$reservationId]);
            if (!$res || $res['status'] !== 'active') {
                $this->db->rollBack();
                return; // Nothing to cancel
            }

            $query = 'SELECT inventory_id FROM inventory_items WHERE product_id = ?';
            $params = [$res['product_id']];
            if ($res['variant_id']) {
                $query .= ' AND variant_id = ?';
                $params[] = $res['variant_id'];
            } else {
                $query .= ' AND variant_id IS NULL';
            }
            $query .= ' FOR UPDATE';
            $item = $this->db->fetchOne($query, $params);

            if ($item) {
                // Restore available stock by reducing reserved quantity
                $this->db->execute(
                    'UPDATE inventory_items SET reserved_quantity = reserved_quantity - ? WHERE inventory_id = ?',
                    [$res['quantity'], $item['inventory_id']]
                );
            }

            $this->db->execute('UPDATE inventory_reservations SET status = ? WHERE reservation_id = ?', ['expired', $reservationId]);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Manually adjust stock levels (e.g., received stock, damages).
     */
    public function adjustStock(int $productId, ?int $variantId, int $quantityChange, string $type, string $reason, ?int $userId = null): void {
        $this->db->beginTransaction();
        try {
            $query = 'SELECT inventory_id FROM inventory_items WHERE product_id = ?';
            $params = [$productId];
            if ($variantId) {
                $query .= ' AND variant_id = ?';
                $params[] = $variantId;
            } else {
                $query .= ' AND variant_id IS NULL';
            }
            $query .= ' FOR UPDATE';
            $item = $this->db->fetchOne($query, $params);

            if (!$item) {
                // If it doesn't exist, try to initialize it
                $this->initInventory($productId, $variantId, 0);
                $item = $this->db->fetchOne($query, $params);
            }

            $this->db->execute(
                'UPDATE inventory_items SET quantity = quantity + ? WHERE inventory_id = ?',
                [$quantityChange, $item['inventory_id']]
            );

            // Sync legacy columns to keep frontend fast/easy for now
            if ($variantId) {
                $this->db->execute('UPDATE product_variants SET stock_quantity = stock_quantity + ? WHERE variant_id = ?', [$quantityChange, $variantId]);
            } else {
                $this->db->execute('UPDATE products SET stock_quantity = stock_quantity + ? WHERE product_id = ?', [$quantityChange, $productId]);
            }

            $this->logMovement($item['inventory_id'], $type, $quantityChange, $reason, 'manual', null, $userId);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function logMovement(int $inventoryId, string $type, int $qty, string $reason, string $refType, ?int $refId, ?int $userId): void {
        $this->db->insert(
            'INSERT INTO inventory_movements (inventory_id, movement_type, quantity_change, reason, reference_type, reference_id, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$inventoryId, $type, $qty, $reason, $refType, $refId, $userId]
        );
    }
}
