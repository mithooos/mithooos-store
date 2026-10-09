<?php
/**
 * Admin Blog API
 * Requires $admin_user to be set from router.php
 */

$bm = new Blog($db);

switch ($method) {
    case 'GET':
        if ($id !== null) {
            $post = $bm->findByIdOrSlug($id);
            if (!$post) Response::error('Blog post not found.', 404);
            else Response::success($post);
            break;
        }

        $limit  = max(1, min(100, (int)($query['limit'] ?? 20)));
        $offset = max(0, (int)($query['offset'] ?? 0));
        
        $filters = [];
        if (isset($query['is_published']) && $query['is_published'] !== 'all') {
            $filters['is_published'] = filter_var($query['is_published'], FILTER_VALIDATE_BOOLEAN);
        }
        if (!empty($query['search'])) {
            $filters['search'] = $query['search'];
        }

        $res = $bm->list($filters, $limit, $offset);
        Response::success($res);
        break;

    case 'POST':
        $v = Validator::make($body)
            ->required('title')
            ->required('content');
            
        if ($v->fails()) {
            Response::error('Invalid input.', 422, $v->errors());
            break;
        }

        $new_id = $bm->create($body, $admin_user['user_id']);
        Response::success(['post_id' => $new_id], 'Blog post created successfully.', 201);
        break;

    case 'PUT':
    case 'PATCH':
        if ($id === null) {
            Response::error('Post ID required.', 400);
            break;
        }

        if (empty($body)) {
            Response::error('No data provided to update.', 400);
            break;
        }

        $updated = $bm->update((int)$id, $body);
        if ($updated) Response::success(null, 'Blog post updated successfully.');
        else Response::error('Post not found or no changes made.', 404);
        break;

    case 'DELETE':
        if ($id === null) {
            Response::error('Post ID required.', 400);
            break;
        }

        $deleted = $bm->delete((int)$id);
        if ($deleted) Response::success(null, 'Blog post deleted successfully.');
        else Response::error('Post not found.', 404);
        break;

    default:
        Response::error('Method not allowed.', 405);
}
