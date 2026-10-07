<?php
if($method==='GET'){
    $pid=(int)($query['product_id']??$id??0);
    if(!$pid){
        ['limit'=>$limit,'page'=>$page,'offset'=>$offset]=Pagination::params($query);
        $rows=$db->fetchAll('SELECT r.*,u.first_name,u.last_name, p.product_name, p.slug FROM product_reviews r JOIN users u ON r.user_id=u.user_id JOIN products p ON r.product_id=p.product_id WHERE r.is_approved=TRUE AND r.rating >= 4 ORDER BY r.created_at DESC LIMIT ? OFFSET ?',[$limit,$offset]);
        $total=(int)$db->fetchColumn('SELECT COUNT(*) FROM product_reviews r WHERE r.is_approved=TRUE AND r.rating >= 4');
        echo json_encode(['success'=>true,'data'=>$rows,'pagination'=>Pagination::meta($total,$limit,$page)]);
        return;
    }
    ['limit'=>$limit,'page'=>$page,'offset'=>$offset]=Pagination::params($query);
    $rows=$db->fetchAll('SELECT r.*,u.first_name,u.last_name FROM product_reviews r JOIN users u ON r.user_id=u.user_id WHERE r.product_id=? AND r.is_approved=TRUE ORDER BY r.created_at DESC LIMIT ? OFFSET ?',[$pid,$limit,$offset]);
    $total=(int)$db->fetchColumn('SELECT COUNT(*) FROM product_reviews WHERE product_id=? AND is_approved=TRUE',[$pid]);
    echo json_encode(['success'=>true,'data'=>$rows,'pagination'=>Pagination::meta($total,$limit,$page)]);
    return;
}
if($method==='POST'){
    $user=$auth->require();
    $v=Validator::make($body)->required('product_id')->required('rating')->in('rating',['1','2','3','4','5']);
    if($v->fails()){Response::error('Validation failed.',422,$v->errors());return;}
    $dup=$db->fetchOne('SELECT review_id FROM product_reviews WHERE product_id=? AND user_id=?',[(int)$body['product_id'],(int)$user['user_id']]);
    if($dup){Response::error('Already reviewed.');return;}
    $verified=(bool)$db->fetchOne('SELECT 1 FROM orders o JOIN order_items oi ON o.order_id=oi.order_id WHERE o.user_id=? AND oi.product_id=? AND o.order_status="delivered"',[(int)$user['user_id'],(int)$body['product_id']]);
    $db->insert('INSERT INTO product_reviews(product_id,user_id,rating,title,review_text,is_verified_purchase)VALUES(?,?,?,?,?,?)',[(int)$body['product_id'],(int)$user['user_id'],(int)$body['rating'],$body['title']??null,$body['review_text']??null,(bool)$verified]);
    (new Product($db))->refreshRating((int)$body['product_id']);
    Response::success(null,'Review submitted.',201);
    return;
}
Response::error('Method not allowed.',405);
