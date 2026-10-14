<?php
/**
 * MITHOOOS — Public Product Reviews API
 * Handled via /api/reviews
 */

declare(strict_types=1);

if ($method === 'GET') {
    $pid = (int)($query['product_id'] ?? $id ?? 0);
    $currentUser = $auth->getUser();
    $currentUserId = $currentUser ? (int)$currentUser['user_id'] : 0;

    // Homepage / Catalog featured reviews (when product_id is omitted)
    if (!$pid) {
        ['limit' => $limit, 'page' => $page, 'offset' => $offset] = Pagination::params($query);
        $rows = $db->fetchAll(
            "SELECT r.*,
                    r.comment AS review_text,
                    COALESCE(NULLIF(r.reviewer_name, ''), NULLIF(TRIM(COALESCE(u.first_name, '') || ' ' || COALESCE(u.last_name, '')), ''), 'Customer') AS reviewer_display_name,
                    u.first_name, u.last_name,
                    p.product_name, p.slug
             FROM product_reviews r
             LEFT JOIN users u ON r.user_id = u.user_id
             JOIN products p ON r.product_id = p.product_id
             WHERE r.status = 'approved' AND r.rating >= 4
             ORDER BY r.created_at DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
        $total = (int)$db->fetchColumn("SELECT COUNT(*) FROM product_reviews r WHERE r.status = 'approved' AND r.rating >= 4");
        echo json_encode([
            'success'    => true,
            'data'       => $rows,
            'pagination' => Pagination::meta($total, $limit, $page)
        ]);
        return;
    }

    ['limit' => $limit, 'page' => $page, 'offset' => $offset] = Pagination::params($query);

    // If user is logged in, show approved reviews plus their own review (even if pending)
    if ($currentUserId > 0) {
        $whereSql = "r.product_id = ? AND (r.status = 'approved' OR r.user_id = ?)";
        $whereParams = [$pid, $currentUserId];
    } else {
        $whereSql = "r.product_id = ? AND r.status = 'approved'";
        $whereParams = [$pid];
    }

    $rows = $db->fetchAll(
        "SELECT r.*,
                r.comment AS review_text,
                COALESCE(NULLIF(r.reviewer_name, ''), NULLIF(TRIM(COALESCE(u.first_name, '') || ' ' || COALESCE(u.last_name, '')), ''), 'Customer') AS reviewer_display_name,
                u.first_name, u.last_name
         FROM product_reviews r
         LEFT JOIN users u ON r.user_id = u.user_id
         WHERE $whereSql
         ORDER BY (CASE WHEN r.user_id = ? THEN 0 ELSE 1 END), r.created_at DESC
         LIMIT ? OFFSET ?",
        array_merge($whereParams, [$currentUserId, $limit, $offset])
    );

    $total = (int)$db->fetchColumn("SELECT COUNT(*) FROM product_reviews r WHERE $whereSql", $whereParams);

    // Compute star distribution & approved stats for summary
    $starRows = $db->fetchAll(
        "SELECT rating, COUNT(*) as cnt FROM product_reviews WHERE product_id = ? AND status = 'approved' GROUP BY rating",
        [$pid]
    );
    $starCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
    $approvedTotal = 0;
    $sumRating = 0;
    foreach ($starRows as $sr) {
        $star = (int)$sr['rating'];
        $c = (int)$sr['cnt'];
        if (isset($starCounts[$star])) {
            $starCounts[$star] = $c;
        }
        $approvedTotal += $c;
        $sumRating += ($star * $c);
    }
    $avgRating = $approvedTotal > 0 ? round($sumRating / $approvedTotal, 1) : 0;

    echo json_encode([
        'success'    => true,
        'data'       => $rows,
        'pagination' => Pagination::meta($total, $limit, $page),
        'summary'    => [
            'average_rating' => $avgRating,
            'total_reviews'  => $approvedTotal,
            'star_counts'    => $starCounts,
            'user_review_submitted' => $currentUserId > 0 ? (bool)$db->fetchOne("SELECT review_id FROM product_reviews WHERE product_id = ? AND user_id = ?", [$pid, $currentUserId]) : false,
        ]
    ]);
    return;
}

if ($method === 'POST') {
    $user = $auth->require();
    $v = Validator::make($body)
        ->required('product_id')
        ->required('rating')
        ->in('rating', ['1', '2', '3', '4', '5']);

    if ($v->fails()) {
        Response::error('Validation failed.', 422, $v->errors());
        return;
    }

    $pid = (int)$body['product_id'];
    $userId = (int)$user['user_id'];

    // Prevent duplicate reviews for the same product by the same user
    $dup = $db->fetchOne('SELECT review_id FROM product_reviews WHERE product_id = ? AND user_id = ?', [$pid, $userId]);
    if ($dup) {
        Response::error('You have already submitted a review for this product.', 400);
        return;
    }

    // Check if user purchased this product and order was delivered
    $verified = (bool)$db->fetchOne(
        "SELECT 1 FROM orders o JOIN order_items oi ON o.order_id = oi.order_id WHERE o.user_id = ? AND oi.product_id = ? AND o.order_status = 'delivered'",
        [$userId, $pid]
    );

    $title = trim((string)($body['title'] ?? '')) ?: null;
    $comment = trim((string)($body['review_text'] ?? $body['comment'] ?? '')) ?: null;
    $rating = (int)$body['rating'];
    $status = 'approved'; // Standard production flow: published immediately, moderated by admin
    $now = date('c');

    $db->insert(
        'INSERT INTO product_reviews (product_id, user_id, rating, title, comment, status, is_verified_purchase, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [$pid, $userId, $rating, $title, $comment, $status, (int)$verified, $now, $now]
    );

    (new Product($db))->refreshRating($pid);

    Response::success(null, 'Your review has been published. Thank you for your feedback!', 201);
    return;
}

Response::error('Method not allowed.', 405);
