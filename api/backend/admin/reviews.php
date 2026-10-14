<?php
/**
 * MITHOOOS Admin — Reviews Management API
 * Handled via /admin/reviews and /admin/reviews/{id}[/{action}]
 */

declare(strict_types=1);

$action = $segments[3] ?? null;
$productModel = new Product($db);

/* ─────────────────────────────────────────────────────────────
   GET /admin/reviews OR /admin/reviews/{id}
   ───────────────────────────────────────────────────────────── */
if ($method === 'GET') {
    if ($id) {
        $row = $db->fetchOne(
            "SELECT r.*,
                    r.comment AS review_text,
                    p.product_id, p.product_name, p.slug AS product_slug,
                    (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) AS product_image,
                    u.user_id, u.first_name, u.last_name, u.email AS user_email,
                    COALESCE(NULLIF(r.reviewer_name, ''), NULLIF(TRIM(COALESCE(u.first_name, '') || ' ' || COALESCE(u.last_name, '')), ''), 'Customer') AS display_name
             FROM product_reviews r
             JOIN products p ON r.product_id = p.product_id
             LEFT JOIN users u ON r.user_id = u.user_id
             WHERE r.review_id = ?",
            [(int)$id]
        );
        if (!$row) {
            Response::error('Review not found.', 404);
            return;
        }
        Response::success($row);
        return;
    }

    // List with search, filters, sort, and pagination
    $where = ['1=1'];
    $params = [];

    // Search query
    if (!empty($query['search'])) {
        $term = '%' . trim((string)$query['search']) . '%';
        $where[] = "(p.product_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR r.reviewer_name LIKE ? OR r.title LIKE ? OR r.comment LIKE ?)";
        array_push($params, $term, $term, $term, $term, $term, $term, $term);
    }

    // Status filter: all | pending | approved | rejected
    if (!empty($query['status']) && $query['status'] !== 'all') {
        $where[] = "r.status = ?";
        $params[] = $query['status'];
    }

    // Rating filter: all | 1..5
    if (!empty($query['rating']) && $query['rating'] !== 'all') {
        $where[] = "r.rating = ?";
        $params[] = (int)$query['rating'];
    }

    // Product filter
    if (!empty($query['product_id']) && $query['product_id'] !== 'all') {
        $where[] = "r.product_id = ?";
        $params[] = (int)$query['product_id'];
    }

    $w = implode(' AND ', $where);

    // Sorting
    $sort = match ($query['sort'] ?? 'newest') {
        'oldest'      => 'r.created_at ASC',
        'rating_desc' => 'r.rating DESC, r.created_at DESC',
        'rating_asc'  => 'r.rating ASC, r.created_at DESC',
        default       => 'r.created_at DESC',
    };

    ['limit' => $limit, 'page' => $page, 'offset' => $offset] = Pagination::params($query, 15);

    $rows = $db->fetchAll(
        "SELECT r.*,
                r.comment AS review_text,
                p.product_name,
                p.slug AS product_slug,
                (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) AS product_image,
                u.first_name,
                u.last_name,
                u.email AS user_email,
                COALESCE(NULLIF(r.reviewer_name, ''), NULLIF(TRIM(COALESCE(u.first_name, '') || ' ' || COALESCE(u.last_name, '')), ''), 'Customer') AS display_name
         FROM product_reviews r
         JOIN products p ON r.product_id = p.product_id
         LEFT JOIN users u ON r.user_id = u.user_id
         WHERE $w
         ORDER BY $sort
         LIMIT ? OFFSET ?",
        [...$params, $limit, $offset]
    );

    $total = (int)$db->fetchColumn(
        "SELECT COUNT(*) FROM product_reviews r
         JOIN products p ON r.product_id = p.product_id
         LEFT JOIN users u ON r.user_id = u.user_id
         WHERE $w",
        $params
    );

    // Overall metrics for status tabs / summary cards (unfiltered for stable dashboard counters)
    $stats = [
        'total'         => (int)$db->fetchColumn("SELECT COUNT(*) FROM product_reviews"),
        'pending'       => (int)$db->fetchColumn("SELECT COUNT(*) FROM product_reviews WHERE status = 'pending'"),
        'approved'      => (int)$db->fetchColumn("SELECT COUNT(*) FROM product_reviews WHERE status = 'approved'"),
        'rejected'      => (int)$db->fetchColumn("SELECT COUNT(*) FROM product_reviews WHERE status = 'rejected'"),
        'with_replies'  => (int)$db->fetchColumn("SELECT COUNT(*) FROM product_reviews WHERE admin_reply IS NOT NULL AND TRIM(admin_reply) != ''"),
        'avg_rating'    => (float)($db->fetchColumn("SELECT ROUND(COALESCE(AVG(rating), 0), 1) FROM product_reviews WHERE status = 'approved'") ?: 0),
    ];

    // Lightweight product list for dropdown filters and manual review creation
    $products = $db->fetchAll("SELECT product_id, product_name FROM products ORDER BY product_name ASC");

    echo json_encode([
        'success'    => true,
        'data'       => $rows,
        'pagination' => Pagination::meta($total, $limit, $page),
        'stats'      => $stats,
        'products'   => $products,
    ]);
    return;
}

/* ─────────────────────────────────────────────────────────────
   POST /admin/reviews/{id}/reply  (Save / Update official reply)
   ───────────────────────────────────────────────────────────── */
if ($method === 'POST' && $id && $action === 'reply') {
    $reply = trim((string)($body['reply'] ?? $body['admin_reply'] ?? ''));
    if ($reply === '') {
        Response::error('Store reply text cannot be empty.', 422);
        return;
    }

    $rev = $db->fetchOne('SELECT review_id, product_id FROM product_reviews WHERE review_id = ?', [(int)$id]);
    if (!$rev) {
        Response::error('Review not found.', 404);
        return;
    }

    $now = date('c');
    $db->execute(
        "UPDATE product_reviews SET admin_reply = ?, admin_reply_at = ?, updated_at = ? WHERE review_id = ?",
        [$reply, $now, $now, (int)$id]
    );

    Response::success([
        'review_id'      => (int)$id,
        'admin_reply'    => $reply,
        'admin_reply_at' => $now,
    ], 'Store reply saved successfully.');
    return;
}

/* ─────────────────────────────────────────────────────────────
   POST /admin/reviews  (Manual Review Creation by Admin)
   ───────────────────────────────────────────────────────────── */
if ($method === 'POST' && !$id) {
    $v = Validator::make($body)
        ->required('product_id')
        ->required('rating')
        ->in('rating', ['1', '2', '3', '4', '5']);

    if ($v->fails()) {
        Response::error('Validation failed.', 422, $v->errors());
        return;
    }

    $pid = (int)$body['product_id'];
    $prod = $db->fetchOne('SELECT product_id FROM products WHERE product_id = ?', [$pid]);
    if (!$prod) {
        Response::error('Selected product does not exist.', 404);
        return;
    }

    $comment = trim((string)($body['comment'] ?? $body['review_text'] ?? ''));
    $title   = trim((string)($body['title'] ?? '')) ?: null;
    if ($comment === '' && !$title) {
        Response::error('Review comment or title is required.', 422);
        return;
    }

    $rating = (int)$body['rating'];
    $status = in_array($body['status'] ?? 'approved', ['approved', 'pending', 'rejected'], true) ? $body['status'] : 'approved';
    $reviewerName = trim((string)($body['reviewer_name'] ?? '')) ?: 'Verified Customer';
    $userId = !empty($body['user_id']) ? (int)$body['user_id'] : null;
    $isVerified = !empty($body['is_verified_purchase']) ? 1 : 0;
    $adminReply = trim((string)($body['admin_reply'] ?? '')) ?: null;
    $adminReplyAt = $adminReply ? date('c') : null;
    $now = date('c');

    $newId = $db->insert(
        "INSERT INTO product_reviews (product_id, user_id, reviewer_name, rating, title, comment, status, is_verified_purchase, admin_reply, admin_reply_at, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        [$pid, $userId, $reviewerName, $rating, $title, $comment, $status, $isVerified, $adminReply, $adminReplyAt, $now, $now]
    );

    $productModel->refreshRating($pid);

    Response::success(['review_id' => $newId], 'Review added successfully.', 201);
    return;
}

/* ─────────────────────────────────────────────────────────────
   PUT /admin/reviews/{id}  (Edit Review or Reply)
   ───────────────────────────────────────────────────────────── */
if ($method === 'PUT' && $id) {
    $rev = $db->fetchOne('SELECT * FROM product_reviews WHERE review_id = ?', [(int)$id]);
    if (!$rev) {
        Response::error('Review not found.', 404);
        return;
    }

    // Direct reply update via PUT /admin/reviews/{id}/reply
    if ($action === 'reply') {
        $reply = trim((string)($body['reply'] ?? $body['admin_reply'] ?? ''));
        if ($reply === '') {
            Response::error('Store reply text cannot be empty.', 422);
            return;
        }
        $now = date('c');
        $db->execute(
            "UPDATE product_reviews SET admin_reply = ?, admin_reply_at = ?, updated_at = ? WHERE review_id = ?",
            [$reply, $now, $now, (int)$id]
        );
        Response::success([
            'review_id'      => (int)$id,
            'admin_reply'    => $reply,
            'admin_reply_at' => $now,
        ], 'Store reply saved.');
        return;
    }

    $updates = [];
    $params  = [];

    if (isset($body['status'])) {
        if (!in_array($body['status'], ['approved', 'pending', 'rejected'], true)) {
            Response::error('Invalid status. Allowed: approved, pending, rejected.', 422);
            return;
        }
        $updates[] = 'status = ?';
        $params[]  = $body['status'];
    }

    if (isset($body['rating'])) {
        $r = (int)$body['rating'];
        if ($r < 1 || $r > 5) {
            Response::error('Rating must be between 1 and 5.', 422);
            return;
        }
        $updates[] = 'rating = ?';
        $params[]  = $r;
    }

    if (array_key_exists('title', $body)) {
        $updates[] = 'title = ?';
        $params[]  = trim((string)$body['title']) ?: null;
    }

    if (array_key_exists('comment', $body) || array_key_exists('review_text', $body)) {
        $text = trim((string)($body['comment'] ?? $body['review_text'] ?? ''));
        $updates[] = 'comment = ?';
        $params[]  = $text ?: null;
    }

    if (array_key_exists('reviewer_name', $body)) {
        $updates[] = 'reviewer_name = ?';
        $params[]  = trim((string)$body['reviewer_name']) ?: null;
    }

    if (isset($body['is_verified_purchase'])) {
        $updates[] = 'is_verified_purchase = ?';
        $params[]  = !empty($body['is_verified_purchase']) ? 1 : 0;
    }

    if (array_key_exists('admin_reply', $body)) {
        $replyText = trim((string)$body['admin_reply']);
        $updates[] = 'admin_reply = ?';
        $params[]  = $replyText !== '' ? $replyText : null;
        $updates[] = 'admin_reply_at = ?';
        $params[]  = $replyText !== '' ? date('c') : null;
    }

    if (empty($updates)) {
        Response::error('No update parameters provided.', 422);
        return;
    }

    $updates[] = 'updated_at = ?';
    $params[]  = date('c');
    $params[]  = (int)$id;

    $db->execute("UPDATE product_reviews SET " . implode(', ', $updates) . " WHERE review_id = ?", $params);
    $productModel->refreshRating((int)$rev['product_id']);

    Response::success(null, 'Review updated successfully.');
    return;
}

/* ─────────────────────────────────────────────────────────────
   DELETE /admin/reviews/{id}  (Delete Review or Admin Reply)
   ───────────────────────────────────────────────────────────── */
if ($method === 'DELETE' && $id) {
    $rev = $db->fetchOne('SELECT review_id, product_id FROM product_reviews WHERE review_id = ?', [(int)$id]);
    if (!$rev) {
        Response::error('Review not found.', 404);
        return;
    }

    if ($action === 'reply') {
        $now = date('c');
        $db->execute(
            "UPDATE product_reviews SET admin_reply = NULL, admin_reply_at = NULL, updated_at = ? WHERE review_id = ?",
            [$now, (int)$id]
        );
        Response::success(null, 'Store reply deleted.');
        return;
    }

    $db->execute("DELETE FROM product_reviews WHERE review_id = ?", [(int)$id]);
    $productModel->refreshRating((int)$rev['product_id']);

    Response::success(null, 'Review deleted successfully.');
    return;
}

Response::error('Method not allowed.', 405);
