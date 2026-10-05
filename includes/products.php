<?php
require_once __DIR__ . '/db.php';

function get_all_products(int $category_id = 0, bool $only_active = true): array {
    global $pdo;
    $sql = "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
    $params = [];

    if ($only_active) {
        $sql .= " AND p.is_active = 1 AND c.is_active = 1";
    }
    if ($category_id > 0) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category_id;
    }
    $sql .= " ORDER BY p.sort_order ASC, p.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function get_product_by_slug(string $slug): ?array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.slug = ? AND p.is_active = 1 LIMIT 1");
    $stmt->execute([$slug]);
    $res = $stmt->fetch();
    return $res ?: null;
}

function format_price(float $price, string $currency = 'RM'): string {
    return $currency . ' ' . number_format($price, 2);
}