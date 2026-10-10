<?php
class Product {
    public function __construct(private Database $db) {}

    /* ── List with filters ── */
    public function list(array $f = [], int $limit = 20, int $offset = 0): array {
        $where = ['p.is_active = TRUE'];
        if (empty($f['admin_view'])) {
            $where[] = "p.status = 'published'";
        }
        $bind  = [];

        if (!empty($f['collection_id'])) { 
            $where[] = 'p.product_id IN (SELECT product_id FROM product_collections WHERE collection_id = ?)'; 
            $bind[] = (int)$f['collection_id']; 
        }
        if (!empty($f['category_id'])) { $where[] = 'p.category_id = ?'; $bind[] = (int)$f['category_id']; }
        if (isset($f['min_price']))    { $where[] = 'ROUND(p.price - (p.price * p.discount_percentage / 100), 2) >= ?'; $bind[] = (float)$f['min_price']; }
        if (isset($f['max_price']))    { $where[] = 'ROUND(p.price - (p.price * p.discount_percentage / 100), 2) <= ?'; $bind[] = (float)$f['max_price']; }
        if (!empty($f['is_featured'])) { $where[] = 'p.is_featured = TRUE'; }
        if (!empty($f['is_new']))      { $where[] = 'p.is_new = TRUE'; }
        if (!empty($f['is_sale']))     { $where[] = 'p.is_sale = TRUE'; }
        if (!empty($f['search'])) {
            $search = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($f['search']));
            $where[] = "(p.product_name LIKE ? ESCAPE '\\' OR p.description LIKE ? ESCAPE '\\')";
            $bind[]  = "%{$search}%";
            $bind[]  = "%{$search}%";
        }

        $order = match ($f['sort'] ?? 'newest') {
            'price_asc'  => 'ROUND(p.price - (p.price * p.discount_percentage / 100), 2) ASC',
            'price_desc' => 'ROUND(p.price - (p.price * p.discount_percentage / 100), 2) DESC',
            'rating'     => 'p.rating DESC',
            'popular'    => 'p.view_count DESC',
            default      => 'p.created_at DESC',
        };

        $wSql  = implode(' AND ', $where);

        $rows = $this->db->fetchAll(
            "SELECT p.product_id, p.product_name, p.slug,
                    COUNT(*) OVER() AS total_count,
                    p.price, p.discount_percentage,
                    ROUND(p.price - (p.price * p.discount_percentage / 100), 2) AS final_price,
                    p.stock_quantity, p.is_featured, p.is_new, p.is_sale,
                    p.rating, p.review_count, c.category_name,
                    p.online_discount_enabled, p.online_discount_percentage,
                    p.online_discount_start, p.online_discount_end, p.online_discount_label,
                    (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = TRUE LIMIT 1) AS image
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.category_id
             WHERE $wSql ORDER BY $order LIMIT ? OFFSET ?",
            [...$bind, $limit, $offset]
        );

        if ($rows) {
            $total = (int)$rows[0]['total_count'];
            foreach ($rows as &$row) {
                unset($row['total_count']);
            }
            unset($row);
        } else {
            $total = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM products p WHERE $wSql", $bind);
        }

        return compact('rows', 'total');
    }

    /* ── Single product ── */
    public function find(int|string $id, string $by = 'id'): array|false {
        $col  = $by === 'slug' ? 'p.slug' : 'p.product_id';
        $prod = $this->db->fetchOne(
            "SELECT p.*, ROUND(p.price - (p.price * p.discount_percentage / 100), 2) AS final_price, c.category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.category_id
             WHERE $col = ? AND p.is_active = TRUE", [$id]
        );
        if (!$prod) return false;

        $prod['images']     = $this->db->fetchAll('SELECT image_id, product_id, image_url, image_mime, alt_text, display_order, is_primary, created_at FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, display_order', [$prod['product_id']]);
        $prod['variants']   = $this->db->fetchAll('SELECT * FROM product_variants WHERE product_id = ? AND is_active = TRUE ORDER BY variant_id', [$prod['product_id']]);
        $prod['attributes'] = $this->db->fetchAll('SELECT attribute_type AS attr_name, attribute_value AS attr_value FROM product_attributes WHERE product_id = ?', [$prod['product_id']]);
        $prod['collections']= $this->db->fetchAll('SELECT c.collection_id, c.collection_name, c.slug FROM product_collections pc JOIN collections c ON pc.collection_id = c.collection_id WHERE pc.product_id = ? AND c.is_active = TRUE', [$prod['product_id']]);
        
        if (empty($f['admin_view'])) {
            // Increment views only for non-admin
            $this->db->execute('UPDATE products SET view_count = view_count + 1 WHERE product_id = ?', [$prod['product_id']]);
        }

        return $prod;
    }

    /* ── Create ── */
    public function create(array $data): int {
        $slug = $this->uniqueSlug($data['product_name']);
        
        $pid = $this->db->insert(
            'INSERT INTO products (product_name, slug, category_id, description, short_description,
             price, cost_price, discount_percentage, stock_quantity, sku, weight, is_featured, is_new, is_sale, 
             meta_title, meta_description, meta_keywords, status, is_active, published_at,
             online_discount_enabled, online_discount_percentage, online_discount_start, online_discount_end, online_discount_label)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $data['product_name'], $slug, $data['category_id'],
                $data['description'], $data['short_description'] ?? null,
                $data['price'], $data['cost_price'] ?? 0, $data['discount_percentage'] ?? 0,
                $data['stock_quantity'] ?? 0, $data['sku'] ?? null, $data['weight'] ?? null,
                (int)($data['is_featured'] ?? 0), (int)($data['is_new'] ?? 0), (int)($data['is_sale'] ?? 0),
                $data['meta_title'] ?? null, $data['meta_description'] ?? null, $data['meta_keywords'] ?? null,
                $data['status'] ?? 'published', (int)($data['is_active'] ?? 1),
                !empty($data['published_at']) ? $data['published_at'] : null,
                (int)($data['online_discount_enabled'] ?? 0), $data['online_discount_percentage'] ?? 0,
                !empty($data['online_discount_start']) ? $data['online_discount_start'] : null,
                !empty($data['online_discount_end']) ? $data['online_discount_end'] : null,
                $data['online_discount_label'] ?? null
            ]
        );

        $this->syncRelations($pid, $data);
        return $pid;
    }

    /* ── Update ── */
    public function update(int $id, array $data): bool {
        $fields = ['updated_at = CURRENT_TIMESTAMP'];
        $bind   = [];
        $allowed = ['product_name','category_id','description','short_description',
                    'price','cost_price','discount_percentage','stock_quantity','sku','weight','is_featured','is_new','is_sale','is_active',
                    'status','published_at','meta_title','meta_description','meta_keywords',
                    'online_discount_enabled','online_discount_percentage','online_discount_start','online_discount_end','online_discount_label'];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) { $fields[] = "$col = ?"; $bind[] = $data[$col] === '' ? null : $data[$col]; }
        }
        $bind[] = $id;
        $res = (bool)$this->db->execute('UPDATE products SET ' . implode(', ', $fields) . ' WHERE product_id = ?', $bind);
        $this->syncRelations($id, $data);
        return $res;
    }

    private function syncRelations(int $pid, array $data): void {
        if (isset($data['collections']) && is_array($data['collections'])) {
            $this->db->execute('DELETE FROM product_collections WHERE product_id = ?', [$pid]);
            foreach ($data['collections'] as $cid) {
                $this->db->insert('INSERT INTO product_collections (product_id, collection_id) VALUES (?,?) ON CONFLICT DO NOTHING', [$pid, $cid]);
            }
        }
        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $this->db->execute('DELETE FROM product_attributes WHERE product_id = ?', [$pid]);
            foreach ($data['attributes'] as $name => $val) {
                $this->db->insert('INSERT INTO product_attributes (product_id, attribute_type, attribute_value) VALUES (?,?,?)', [$pid, $name, $val]);
            }
        }
        if (isset($data['variants']) && is_array($data['variants'])) {
            $this->db->execute('DELETE FROM product_variants WHERE product_id = ?', [$pid]);
            foreach ($data['variants'] as $v) {
                $this->db->insert(
                    'INSERT INTO product_variants (product_id, sku, variant_name, size, color, price, stock_quantity) VALUES (?,?,?,?,?,?,?)',
                    [$pid, $v['sku'] ?? null, $v['variant_name'], $v['size'] ?? null, $v['color'] ?? null, $v['price'] ?? null, $v['stock_quantity'] ?? 0]
                );
            }
        }
    }

    /* ── Delete (soft) ── */
    public function delete(int $id): bool {
        return (bool)$this->db->execute("UPDATE products SET is_active = FALSE, status = 'archived' WHERE product_id = ?", [$id]);
    }

    /* ── Unique slug ── */
    private function uniqueSlug(string $name): string {
        $base = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
        $slug = $base;
        $i    = 1;
        while ($this->db->fetchOne('SELECT 1 FROM products WHERE slug = ?', [$slug]))
            $slug = "$base-" . $i++;
        return $slug;
    }

    /* ── Update rating after review ── */
    public function refreshRating(int $product_id): void {
        $this->db->execute(
            'UPDATE products SET
             rating       = (SELECT COALESCE(AVG(rating),0) FROM product_reviews WHERE product_id = products.product_id AND is_approved = TRUE),
             review_count = (SELECT COUNT(*) FROM product_reviews WHERE product_id = products.product_id AND is_approved = TRUE)
             WHERE product_id = ?',
            [$product_id]
        );
    }
}
