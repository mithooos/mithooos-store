<?php
$user=$auth->getUser();
$sid=$_SERVER['HTTP_X_SESSION_ID']??bin2hex(random_bytes(16));
$cart=new Cart($db,$user?(int)$user['user_id']:null,$user?null:$sid);
match($method){
    'GET'=>(function()use($cart){Response::success($cart->totals(0.10,$_GET['coupon']??null, null, $_GET['shipping_method']??'standard'));})(),
    'POST'=>(function()use($cart,$body){
        $v=Validator::make($body)->required('product_id')->numeric('product_id');
        if($v->fails()){Response::error('Validation failed.',422,$v->errors());return;}
        $cart->add((int)$body['product_id'], (int)($body['quantity']??1), isset($body['variant_id']) ? (int)$body['variant_id'] : null);
        Response::success($cart->totals(0.10,$_GET['coupon']??null, null, $_GET['shipping_method']??'standard'),'Item added to cart.');
    })(),
    'PUT'=>(function()use($cart,$id,$body){
        if(!$id){Response::error('Cart item ID required.');return;}
        $ok=$cart->update((int)$id,(int)($body['quantity']??1));
        if(!$ok){Response::error('Cart item not found or out of stock.',404);return;}
        Response::success($cart->totals(0.10,$_GET['coupon']??null, null, $_GET['shipping_method']??'standard'),'Cart updated.');
    })(),
    'DELETE'=>(function()use($cart,$id){
        if($id){
            $ok=$cart->remove((int)$id);
            if(!$ok){Response::error('Cart item not found.',404);return;}
            Response::success($cart->totals(0.10,$_GET['coupon']??null, null, $_GET['shipping_method']??'standard'),'Item removed.');
        }
        else{$cart->clear();Response::success($cart->totals(0.10,$_GET['coupon']??null, null, $_GET['shipping_method']??'standard'),'Cart cleared.');}
    })(),
    default=>Response::error('Method not allowed.',405),
};
