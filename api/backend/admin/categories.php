<?php
if ($method === 'GET') {
    $rows = $db->fetchAll(
        'SELECT * FROM categories WHERE is_active = TRUE ORDER BY display_order, category_name'
    );
    Response::success($rows);
    return;
}

if ($method === 'POST') {
    $v = Validator::make($body)
        ->required('category_name')
        ->required('slug');

    if ($v->fails()) {
        Response::error('Validation failed.', 422, $v->errors());
        return;
    }

    $name = trim((string) $body['category_name']);
    $slug = trim((string) $body['slug']);
    $description = trim((string) ($body['description'] ?? '')) ?: null;
    $coverImageUrl = trim((string) ($body['cover_image_url'] ?? '')) ?: null;
    $displayOrder = isset($body['display_order']) ? (int) $body['display_order'] : 0;

    $exists = $db->fetchOne('SELECT category_id FROM categories WHERE slug = ? OR category_name = ?', [$slug, $name]);
    if ($exists) {
        Response::error('A category with this name or slug already exists.', 409);
        return;
    }

    $categoryId = $db->insert(
        'INSERT INTO categories (category_name, slug, description, cover_image_url, display_order, is_active) VALUES (?, ?, ?, ?, ?, 1)',
        [$name, $slug, $description, $coverImageUrl, $displayOrder]
    );

    Response::success(['category_id' => $categoryId], 'Category created.', 201);
    return;
}

if ($method === 'PUT' && $id) {
    $id = (int) $id;
    $updates = ['updated_at = CURRENT_TIMESTAMP'];
    $bind = [];

    foreach (['category_name', 'slug', 'description', 'display_order', 'cover_image_url', 'is_active'] as $field) {
        if (array_key_exists($field, $body)) {
            $updates[] = "$field = ?";
            $bind[] = $field === 'is_active' ? (bool) $body[$field] : $body[$field];
        }
    }

    if (!$bind) {
        Response::error('No category fields were provided.', 422);
        return;
    }

    $bind[] = $id;
    $db->execute('UPDATE categories SET ' . implode(', ', $updates) . ' WHERE category_id = ?', $bind);
    Response::success(null, 'Category updated.');
    return;
}

if ($method === 'DELETE' && $id) {
    $db->execute('UPDATE categories SET is_active = FALSE WHERE category_id = ?', [(int) $id]);
    Response::success(null, 'Category deleted.');
    return;
}

Response::error('Method not allowed.', 405);
