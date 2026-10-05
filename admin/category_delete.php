<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_auth();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    // 检查分类下是否有关联的产品
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        die("该分类下尚有产品存在，无法直接删除。请先删除或重定向产品分类。");
    }

    $delStmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $delStmt->execute([$id]);
}
header("Location: categories.php");
exit();