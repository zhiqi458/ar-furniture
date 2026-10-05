<?php
require_once 'includes/products.php';

$slug = $_GET['slug'] ?? '';
$product = get_product_by_slug($slug);

if (!$product) {
    http_response_code(404);
    die("未找到对应产品。");
}

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$current_url = "$protocol://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - 3D/AR 效果展示</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <!-- Google <model-viewer> 标准库 -->
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.4.0/model-viewer.min.js"></script>
</head>
<body>

<div class="container my-4">
    <a href="index.php" class="btn btn-link text-decoration-none mb-3">← 返回目录</a>

    <div class="row g-4">
        <!-- 左侧 3D 模型展示 -->
        <div class="col-lg-7">
            <div class="position-relative">
                <model-viewer 
                    src="<?= htmlspecialchars($product['glb_path']) ?>" 
                    <?= !empty($product['usdz_path']) ? 'ios-src="' . htmlspecialchars($product['usdz_path']) . '"' : '' ?>
                    alt="<?= htmlspecialchars($product['name']) ?>"
                    ar 
                    ar-modes="webxr scene-viewer quick-look" 
                    camera-controls 
                    shadow-intensity="1"
                    touch-action="pan-y">
                    <button slot="ar-button" class="btn btn-success position-absolute bottom-0 start-50 translate-middle-x mb-3 shadow">
                        📱 在真实空间中查看 (AR)
                    </button>
                </model-viewer>
                <div id="model-error" class="alert alert-danger d-none mt-2">3D 模型文件加载失败，请检查模型文件格式。</div>
            </div>
            <div class="alert alert-light text-muted border mt-3 text-center small">
                支持兼容的 Android (WebXR / Scene Viewer) 和 iOS (Quick Look) 设备。
            </div>
        </div>

        <!-- 右侧 产品详细信息 -->
        <div class="col-lg-5 d-flex flex-column">
            <span class="badge bg-secondary mb-2 align-self-start"><?= htmlspecialchars($product['category_name']) ?></span>
            <h2 class="fw-bold"><?= htmlspecialchars($product['name']) ?></h2>
            <h3 class="text-primary my-3"><?= format_price($product['price'], $product['currency']) ?></h3>
            <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

            <div class="card border-light bg-light mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">产品真实尺寸：</h6>
                    <div class="row text-center">
                        <div class="col"><strong>宽</strong><br><?= (float)$product['width_cm'] ?> cm</div>
                        <div class="col"><strong>高</strong><br><?= (float)$product['height_cm'] ?> cm</div>
                        <div class="col"><strong>深</strong><br><?= (float)$product['depth_cm'] ?> cm</div>
                    </div>
                </div>
            </div>

            <!-- 手机跨屏转移 QR 码 -->
            <div class="qr-block mt-auto text-center p-3 border rounded bg-white">
                <h6>用手机扫码在房间中试摆</h6>
                <div id="product-qr" class="d-inline-block my-2"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="js/qr.js"></script>
<script src="js/app.js"></script>
<script>
    generateQRCode("product-qr", "<?= $current_url ?>");
</script>
</body>
</html>