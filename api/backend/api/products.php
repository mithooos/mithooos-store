<?php
$product = new Product($db);
if ($method==='GET') {
    if ($id) {
        $p = is_numeric($id) ? $product->find((int)$id) : $product->find($id,'slug');
        if (!$p) { Response::error('Product not found.',404); return; }
        Response::success($p);
    } else {
        ['limit'=>$limit,'page'=>$page,'offset'=>$offset] = Pagination::params($query);
        ['rows'=>$rows,'total'=>$total] = $product->list($query,$limit,$offset);
        echo json_encode(['success'=>true,'data'=>$rows,'pagination'=>Pagination::meta($total,$limit,$page)]);
    }
    return;
}
if ($method==='POST') {
    $auth->requireAdmin();
    $v=Validator::make($body)
        ->required('product_name')->required('category_id')->required('price')
        ->positive('price')->required('description')
        ->nonNegative('cost_price')->max('discount_percentage', 100)->max('online_discount_percentage', 100);
    if ($v->fails()) { Response::error('Validation failed.',422,$v->errors()); return; }
    $pid = $product->create($body);
    Response::success(['product_id'=>$pid],'Product created.',201);
    return;
}
if ($method==='PUT'&&$id) { 
    $auth->requireAdmin(); 
    $v=Validator::make($body)->nonNegative('cost_price')->max('discount_percentage', 100)->max('online_discount_percentage', 100);
    if ($v->fails()) { Response::error('Validation failed.',422,$v->errors()); return; }
    $product->update((int)$id,$body); 
    Response::success(null,'Product updated.'); 
    return; 
}
if ($method==='DELETE'&&$id) { $auth->requireAdmin(); $product->delete((int)$id); Response::success(null,'Product deleted.'); return; }
Response::error('Method not allowed.',405);
