<?php
require_once __DIR__ . '/db.php';

function get_all_categories(bool $only_active = true): array {
    global $pdo;
    $sql = "SELECT * FROM categories";
    if ($only_active) {
        $sql .= " WHERE is_active = 1";
    }
    $sql .= " ORDER BY sort_order ASC, id ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}