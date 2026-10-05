<?php
function render_admin_header(string $title = '管理后台') {
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - AR 家具管理后台</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-link navbar-brand fw-bold" href="index.php">AR 家具管理后台</a>
    <div class="navbar-nav ms-auto">
      <a class="nav-link" href="index.php">产品管理</a>
      <a class="nav-link" href="categories.php">分类管理</a>
      <a class="nav-link" href="../index.php" target="_blank">查看前台</a>
      <a class="nav-link text-danger" href="logout.php">退出登录</a>
    </div>
  </div>
</nav>
<div class="container">
<?php
}

function render_admin_footer() {
?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
}