<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_auth();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    // 查询获取文件路径以便清理本地无用文件
    $stmt = $pdo->prepare("SELECT glb_path, usdz_path, thumb_path FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch();

    if ($p) {
        if ($p['glb_path'] && file_exists('../' . $p['glb_path'])) @unlink('../' . $p['glb_path']);
        if ($p['usdz_path'] && file_exists('../' . $p['usdz_path'])) @unlink('../' . $p['usdz_path']);
        if ($p['thumb_path'] && file_exists('../' . $p['thumb_path'])) @unlink('../' . $p['thumb_path']);

        $delStmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $delStmt->execute([$id]);
    }
}
header("Location: index.php");
exit();