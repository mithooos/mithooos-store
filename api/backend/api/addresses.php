<?php
$uid = (int)$auth->require()['user_id'];

function addressPayload(array $body): array {
    $required = ['full_name', 'phone', 'country', 'state', 'city', 'postal_code', 'street_address'];
    foreach ($required as $field) {
        if (trim((string)($body[$field] ?? '')) === '') {
            Response::error('Please complete all required address fields.', 422, [$field . ' is required.']);
            return [];
        }
    }
    return [
        trim($body['address_type'] ?? 'shipping'),
        trim($body['full_name']), trim($body['phone']), trim($body['country']),
        trim($body['state']), trim($body['city']), trim($body['postal_code']),
        trim($body['street_address']), trim($body['apartment_suite'] ?? ''),
        filter_var($body['is_default'] ?? false, FILTER_VALIDATE_BOOLEAN),
    ];
}

if ($method === 'GET') {
    $rows = $db->fetchAll('SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC', [$uid]);
    Response::success($rows);
    return;
}

if ($method === 'POST') {
    $payload = addressPayload($body);
    if (!$payload) return;
    if ($payload[9]) $db->execute('UPDATE addresses SET is_default = 0 WHERE user_id = ?', [$uid]);
    $id = $db->insert(
        'INSERT INTO addresses (user_id,address_type,full_name,phone,country,state,city,postal_code,street_address,apartment_suite,is_default) VALUES (?,?,?,?,?,?,?,?,?,?,?)',
        [$uid, ...$payload]
    );
    Response::success(['address_id' => $id], 'Address saved.', 201);
    return;
}

if ($id && ($method === 'PUT' || $method === 'DELETE')) {
    $owned = $db->fetchOne('SELECT address_id FROM addresses WHERE address_id = ? AND user_id = ?', [(int)$id, $uid]);
    if (!$owned) { Response::error('Address not found.', 404); return; }

    if ($method === 'DELETE') {
        $db->execute('DELETE FROM addresses WHERE address_id = ? AND user_id = ?', [(int)$id, $uid]);
        Response::success(null, 'Address deleted.');
        return;
    }

    $payload = addressPayload($body);
    if (!$payload) return;
    if ($payload[9]) $db->execute('UPDATE addresses SET is_default = FALSE WHERE user_id = ? AND address_id <> ?', [$uid, (int)$id]);
    $db->execute(
        'UPDATE addresses SET address_type=?,full_name=?,phone=?,country=?,state=?,city=?,postal_code=?,street_address=?,apartment_suite=?,is_default=?,updated_at=CURRENT_TIMESTAMP WHERE address_id=? AND user_id=?',
        [...$payload, (int)$id, $uid]
    );
    Response::success(null, 'Address updated.');
    return;
}

Response::error('Method not allowed.', 405);
