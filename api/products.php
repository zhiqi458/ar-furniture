<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../includes/products.php';

try {
    $cat_id = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
    $products = get_all_products($cat_id, true);

    echo json_encode([
        'success' => true,
        'data' => $products
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '无法获取产品列表。'
    ]);
}