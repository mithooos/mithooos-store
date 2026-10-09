<?php
declare(strict_types=1);

class Blog {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function list(array $f = [], int $limit = 20, int $offset = 0): array {
        $where = ['1=1'];
        $bind = [];

        if (isset($f['is_published'])) {
            $where[] = 'b.is_published = ?';
            $bind[] = $f['is_published'] ? '1' : '0';
        }

        if (!empty($f['category'])) {
            $where[] = 'b.category = ?';
            $bind[] = $f['category'];
        }

        if (!empty($f['search'])) {
            $where[] = '(b.title LIKE ? OR b.content LIKE ?)';
            $s = "%{$f['search']}%";
            $bind[] = $s;
            $bind[] = $s;
        }

        $w = implode(' AND ', $where);
        
        $total = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM blog_posts b WHERE $w", $bind);
        
        $sql = "SELECT b.*, u.first_name, u.last_name 
                FROM blog_posts b 
                LEFT JOIN users u ON b.author_id = u.user_id 
                WHERE $w 
                ORDER BY b.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $rows = $this->db->fetchAll($sql, [...$bind, $limit, $offset]);

        return compact('rows', 'total');
    }

    public function findByIdOrSlug(string $idOrSlug): array|false {
        $sql = "SELECT b.*, u.first_name, u.last_name 
                FROM blog_posts b 
                LEFT JOIN users u ON b.author_id = u.user_id 
                WHERE b.post_id = ? OR b.slug = ?";
        return $this->db->fetchOne($sql, [(int)$idOrSlug, $idOrSlug]);
    }

    public function incrementViewCount(int $id): void {
        $this->db->execute("UPDATE blog_posts SET view_count = view_count + 1 WHERE post_id = ?", [$id]);
    }

    public function create(array $data, int $author_id): int {
        $slug = $data['slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['title']), '-'));
        
        // Ensure unique slug
        $base = $slug;
        $i = 1;
        while ($this->db->fetchColumn("SELECT COUNT(*) FROM blog_posts WHERE slug = ?", [$slug]) > 0) {
            $slug = $base . '-' . $i++;
        }

        $imgUrl = $data['featured_image_url'] ?? null;
        $imgData = null;
        $imgMime = null;
        if ($imgUrl && str_starts_with($imgUrl, 'data:')) {
            [$meta, $b64] = explode(',', $imgUrl, 2);
            $imgMime = str_replace('data:', '', explode(';', $meta)[0]);
            $imgData = base64_decode($b64);
            $imgUrl = null;
        }

        $sql = "INSERT INTO blog_posts (author_id, title, slug, content, excerpt, featured_image_url, featured_image_data, featured_image_mime, category, is_published, published_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $published_at = !empty($data['is_published']) ? date('Y-m-d H:i:s') : null;

        $postId = (int)$this->db->insert($sql, [
            $author_id,
            $data['title'],
            $slug,
            $data['content'] ?? '',
            $data['excerpt'] ?? null,
            $imgUrl,
            $imgData,
            $imgMime,
            $data['category'] ?? null,
            !empty($data['is_published']) ? 1 : 0,
            $published_at
        ]);
        
        if ($imgData) {
            $this->db->execute("UPDATE blog_posts SET featured_image_url = ? WHERE post_id = ?", ["/backend/api/image.php?type=blog&id={$postId}", $postId]);
        }
        return $postId;
    }

    public function update(int $id, array $data): bool {
        $set = [];
        $bind = [];

        $allowed = ['title', 'slug', 'content', 'excerpt', 'featured_image_url', 'category', 'is_published'];
        
        $is_published_now = null;
        if (isset($data['is_published'])) {
            $current = $this->db->fetchOne("SELECT is_published FROM blog_posts WHERE post_id = ?", [$id]);
            if ($current && !$current['is_published'] && $data['is_published']) {
                $set[] = 'published_at = CURRENT_TIMESTAMP';
            }
        }

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                if ($field === 'featured_image_url' && $data[$field] && str_starts_with($data[$field], 'data:')) {
                    $imgUrl = $data[$field];
                    [$meta, $b64] = explode(',', $imgUrl, 2);
                    $imgMime = str_replace('data:', '', explode(';', $meta)[0]);
                    $imgData = base64_decode($b64);
                    
                    $set[] = "featured_image_data = ?";
                    $bind[] = $imgData;
                    $set[] = "featured_image_mime = ?";
                    $bind[] = $imgMime;
                    
                    $set[] = "featured_image_url = ?";
                    $bind[] = "/backend/api/image.php?type=blog&id={$id}";
                } else {
                    $set[] = "$field = ?";
                    $bind[] = $data[$field];
                }
            }
        }

        if (empty($set)) return false;

        $set[] = 'updated_at = CURRENT_TIMESTAMP';
        
        $sql = "UPDATE blog_posts SET " . implode(', ', $set) . " WHERE post_id = ?";
        $bind[] = $id;

        return (bool)$this->db->execute($sql, $bind);
    }

    public function delete(int $id): bool {
        return (bool)$this->db->execute("DELETE FROM blog_posts WHERE post_id = ?", [$id]);
    }
}
