<?php
/**
 * Public Blog API endpoint
 * GET /api/blog       - List published posts
 * GET /api/blog/:id   - Get single post by ID or Slug
 */

$bm = new Blog($db);

if ($method === 'GET') {
    if ($id !== null) {
        $post = $bm->findByIdOrSlug($id);
        if (!$post) {
            Response::error('Blog post not found.', 404);
            return;
        }
        
        // Only allow viewing published posts unless admin
        if (!$post['is_published']) {
            $user = $auth->user();
            if (!$user || $user['role'] !== 'admin') {
                Response::error('Blog post not found.', 404);
                return;
            }
        }
        
        $bm->incrementViewCount($post['post_id']);
        Response::success($post);
        return;
    }

    // List published posts
    $limit  = max(1, min(50, (int)($query['limit'] ?? 10)));
    $offset = max(0, (int)($query['offset'] ?? 0));
    
    $filters = [
        'is_published' => true,
        'category' => $query['category'] ?? null,
        'search' => $query['search'] ?? null
    ];
    
    $res = $bm->list($filters, $limit, $offset);
    Response::success($res);
    return;
}

Response::error('Method not allowed.', 405);
