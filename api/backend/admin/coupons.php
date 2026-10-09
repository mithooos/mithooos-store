<?php
if ($method === 'GET') {
    $rows = $db->fetchAll('SELECT * FROM coupons ORDER BY created_at DESC');
    Response::success($rows);
    return;
}
if ($method === 'POST') {
    $v=Validator::make($body)->required('code')->required('discount_type')->in('discount_type',['percentage','fixed'])->required('discount_value')->positive('discount_value');
    if ($v->fails()) { Response::error('Validation failed.',422,$v->errors()); return; }
    $body['code'] = strtoupper(trim($body['code']));
    $exists = $db->fetchOne('SELECT coupon_id FROM coupons WHERE code=?',[$body['code']]);
    if ($exists) { Response::error('Coupon code already exists.'); return; }
    $cid = $db->insert('INSERT INTO coupons(code,description,discount_type,discount_value,minimum_purchase,max_usage,valid_from,valid_until)VALUES(?,?,?,?,?,?,?,?)',
        [$body['code'],$body['description']??null,$body['discount_type'],(float)$body['discount_value'],(float)($body['minimum_purchase']??0),(int)($body['max_usage']??-1),$body['valid_from']??null,$body['valid_until']??null]);
    Response::success(['coupon_id'=>$cid],'Coupon created.',201);
    return;
}
if ($method === 'PUT' && $id) {
    $db->execute('UPDATE coupons SET is_active=?,description=?,discount_value=?,minimum_purchase=?,valid_until=? WHERE coupon_id=?',
        [(int)($body['is_active']??1),$body['description']??null,(float)($body['discount_value']??0),(float)($body['minimum_purchase']??0),$body['valid_until']??null,(int)$id]);
    Response::success(null,'Coupon updated.');
    return;
}
if ($method === 'DELETE' && $id) {
    $db->execute('DELETE FROM coupons WHERE coupon_id=?',[(int)$id]);
    Response::success(null,'Coupon deleted.');
    return;
}
Response::error('Method not allowed.',405);
