<?php
$om=new Order($db);
if($method==='GET'){
    $user=$auth->getUser();

    if (isset($query['order_number']) && !empty($query['order_number'])) {
        if (!$user) { Response::error('Unauthorized.', 401); return; }
        $checkUserId = ($user['user_type'] === 'admin') ? null : (int)$user['user_id'];
        $o = $om->findByOrderNumber((string)$query['order_number'], $checkUserId);
        if (!$o) { Response::error('Order not found.', 404); return; }
        Response::success($o);
        return;
    }

    if($id){
        if (is_numeric($id)) {
            if (!$user) { Response::error('Unauthorized.',401); return; }
            $o=$om->findById((int)$id,(int)$user['user_id']);
            if(!$o){Response::error('Order not found.',404);return;}
            Response::success($o);
            return;
        }

        if (!$user) { Response::error('Unauthorized.', 401); return; }
        $checkUserId = ($user['user_type'] === 'admin') ? null : (int)$user['user_id'];
        $o = $om->findByOrderNumber((string)$id, $checkUserId);
        if (!$o) { Response::error('Order not found.', 404); return; }
        Response::success($o);
        return;
    }

    if (!$user) { Response::error('Unauthorized.', 401); return; }
    ['limit'=>$limit,'page'=>$page,'offset'=>$offset]=Pagination::params($query);
    Response::success($om->userOrders((int)$user['user_id'],$limit,$offset));
    return;
}
if($method==='POST'){
    $user=$auth->require();
    $v=Validator::make($body)->required('shipping_address')->required('payment_method');
    if($v->fails()){Response::error('Validation failed.',422,$v->errors());return;}
    
    // Ensure valid payment method
    if (!in_array($body['payment_method'], ['cod', 'easypaisa'])) {
        Response::error('Invalid payment method.', 400);
        return;
    }
    
    // Require payment proof for easypaisa
    if ($body['payment_method'] === 'easypaisa' && empty($body['payment_proof_url'])) {
        Response::error('Payment screenshot is required for Easypaisa payments.', 400);
        return;
    }
    
    // Deep validate shipping address
    $shipReq = ['first_name', 'last_name', 'email', 'phone', 'street', 'city', 'state', 'zip', 'country'];
    $ship = $body['shipping_address'];
    foreach ($shipReq as $f) {
        if (empty($ship[$f])) {
            Response::error('Validation failed.', 422, ['shipping_address' => "Field $f is required."]);
            return;
        }
    }
    
    // Strict Pakistani phone number validation (e.g. 03xx-xxxxxxx, 03xxxxxxxx)
    $phoneRegex = '/^((\+92)|(0092))?-?03[0-9]{2}-?[0-9]{7}$|^03[0-9]{9}$/';
    if (!preg_match($phoneRegex, str_replace(' ', '', $ship['phone']))) {
        Response::error('Validation failed.', 422, ['shipping_address' => "Invalid Pakistani phone number format."]);
        return;
    }

    $cart=new Cart($db,(int)$user['user_id']);
    $totals=$cart->totals(0.10, $body['coupon']??null, $body['payment_method'] ?? null, $body['shipping_method']??'standard');
    if(empty($totals['items'])){Response::error('Cart is empty.');return;}
    $items=array_map(fn($i)=>['product_id'=>$i['product_id'],'variant_id'=>$i['variant_id'],'sku'=>$i['v_sku']??$i['p_sku']??'','product_name'=>$i['product_name'],'price'=>$i['final_price'],'cost_price'=>$i['cost_price'],'quantity'=>$i['quantity']],$totals['items']);
    $bill=$body['billing_same']??false ? $body['shipping_address'] : ($body['billing_address']??$body['shipping_address']);
    
    $idempotencyKey = $body['idempotency_key'] ?? null;

    $db->beginTransaction();
    try {
        $r=$om->create((int)$user['user_id'],$items,$totals,$body['shipping_address'],$bill,$body['payment_method'],$body['shipping_method']??'standard', $body['payment_proof_url']??null, $idempotencyKey);
        
        // Insert into payments table for completeness (although order tracking handles most of it)
        $db->insert(
            'INSERT INTO payments (order_id, payment_method, amount, status) VALUES (?,?,?,?)',
            [$r['order_id'], $body['payment_method'], $totals['total'], 'pending']
        );
        
        $cart->clear();
        $db->commit();
    } catch (\PDOException $e) {
        $db->rollBack();
        if ($e->getCode() === '23505') { // Unique violation (idempotency_key)
            Response::error('Duplicate order detected. Please do not submit the form twice.', 409);
        } else {
            error_log('Order creation failed: ' . $e->getMessage());
            Response::error('An error occurred while creating your order.', 500);
        }
        return;
    } catch (\Throwable $e) {
        $db->rollBack();
        error_log('Order creation failed: ' . $e->getMessage());
        Response::error($e->getMessage() ?: 'An error occurred while processing your order.', 400);
        return;
    }
    
    try {
        $orderInfo = $om->findById((int)$r['order_id'], (int)$user['user_id']);
        if ($orderInfo) {
            $emailService = new Email();
            $emailService->orderConfirmation($orderInfo, $user);
        }
    } catch (\Throwable $e) {
        error_log('Order confirmation email failed: ' . $e->getMessage());
    }

    Response::success($r,'Order placed.',201);
    return;
}
Response::error('Method not allowed.',405);
