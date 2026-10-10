<?php
$user=$auth->require(); $uid=(int)$user['user_id'];
match($method){
    'GET'=>(function()use($db,$uid){
        $items=$db->fetchAll("SELECT w.*,p.product_name,p.slug,ROUND(p.price - (p.price * p.discount_percentage / 100), 2) AS final_price,p.rating,(SELECT image_url FROM product_images WHERE product_id=p.product_id AND is_primary=1 LIMIT 1) AS image FROM wishlists w JOIN products p ON w.product_id=p.product_id WHERE w.user_id=?",[$uid]);
        Response::success($items);
    })(),
    'POST'=>(function()use($db,$uid,$body){
        $pid=(int)($body['product_id']??0);
        if(!$pid){Response::error('product_id required.');return;}
        $ex=$db->fetchOne('SELECT wishlist_id FROM wishlists WHERE user_id=? AND product_id=?',[$uid,$pid]);
        if($ex){$db->execute('DELETE FROM wishlists WHERE user_id=? AND product_id=?',[$uid,$pid]);Response::success(['added'=>false],'Removed.');}
        else{$db->insert('INSERT INTO wishlists(user_id,product_id)VALUES(?,?)',[$uid,$pid]);Response::success(['added'=>true],'Added.');}
    })(),
    'DELETE'=>(function()use($db,$uid,$id){
        if(!$id){Response::error('ID required.');return;}
        $db->execute('DELETE FROM wishlists WHERE wishlist_id=? AND user_id=?',[(int)$id,$uid]);
        Response::success(null,'Removed.');
    })(),
    default=>Response::error('Method not allowed.',405),
};
