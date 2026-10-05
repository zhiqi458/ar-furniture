<?php
require_once 'includes/products.php';
require_once 'includes/categories.php';

$cat_id = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$categories = get_all_categories(true);
$products = get_all_products($cat_id, true);

// 动态获取当前全路径地址
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$current_url = "$protocol://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AR 家具展厅 - 在线体验真实空间摆放</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="index.php">AR Furniture Space</a>
    <a class="btn btn-outline-secondary btn-sm" href="admin/login.php">管理员登录</a>
  </div>
</nav>

<div class="container my-4">
    <!-- Hero Banner -->
    <div class="hero-section mb-5">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold">买前试摆，即刻体验 AR 效果</h1>
                <p class="lead">通过手机摄像头，轻松将真实尺寸的家具摆放到您的房间中。</p>
            </div>
            <div class="col-md-4 text-center qr-block">
                <div class="qr-container shadow">
                    <div id="qrcode"></div>
                    <small class="text-muted d-block mt-2">手机扫码实时预览</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Filter -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="index.php" class="btn <?= $cat_id === 0 ? 'btn-primary' : 'btn-outline-primary' ?>">全部</a>
        <?php foreach ($categories as $cat): ?>
            <a href="index.php?cat=<?= $cat['id'] ?>" class="btn <?= $cat_id === (int)$cat['id'] ? 'btn-primary' : 'btn-outline-primary' ?>">
                <?= htmlspecialchars($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Product Grid -->
    <div class="row g-4">
        <?php if (empty($products)): ?>
            <div class="col-12"><div class="alert alert-info">该分类下暂无产品。</div></div>
        <?php else: ?>
            <?php foreach ($products as $item): ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card product-card h-100 shadow-sm">
                        <img src="<?= htmlspecialchars($item['thumb_path'] ?: 'https://via.placeholder.com/300x200?text=No+Image') ?>" class="card-img-top" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-light text-secondary mb-2 align-self-start"><?= htmlspecialchars($item['category_name']) ?></span>
                            <h5 class="card-title text-truncate"><?= htmlspecialchars($item['name']) ?></h5>
                            <p class="card-text fw-bold text-primary"><?= format_price($item['price'], $item['currency']) ?></p>
                            <a href="product.php?slug=<?= urlencode($item['slug']) ?>" class="btn btn-outline-primary mt-auto">在实景中预览 →</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="js/qr.js"></script>
<script>
    generateQRCode("qrcode", "<?= $current_url ?>");
</script>
</body>
</html>