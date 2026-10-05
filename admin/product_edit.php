<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/categories.php';
require_once '../includes/admin_layout.php';

require_auth();

$id = (int)($_GET['id'] ?? 0);
$categories = get_all_categories(false);
$product = [
    'name' => '', 'slug' => '', 'category_id' => '', 'description' => '',
    'price' => '0.00', 'currency' => 'RM', 'width_cm' => '0', 'height_cm' => '0',
    'depth_cm' => '0', 'is_active' => 1, 'glb_path' => '', 'usdz_path' => '', 'thumb_path' => ''
];

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $fetched = $stmt->fetch();
    if ($fetched) $product = $fetched;
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $category_id = (int)$_POST['category_id'];
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $currency = trim($_POST['currency']);
    $width_cm = (float)$_POST['width_cm'];
    $height_cm = (float)$_POST['height_cm'];
    $depth_cm = (float)$_POST['depth_cm'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $glb_path = $product['glb_path'];
    $usdz_path = $product['usdz_path'];
    $thumb_path = $product['thumb_path'];

    // 文件上传处理函数
    function handle_file_upload($file_key, $allowed_exts, $upload_dir) {
        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed_exts)) {
                $filename = bin2hex(random_bytes(8)) . '.' . $ext;
                $target = $upload_dir . $filename;
                if (move_uploaded_file($_FILES[$file_key]['tmp_name'], '../' . $target)) {
                    return $target;
                }
            }
        }
        return null;
    }

    $new_glb = handle_file_upload('glb_file', ['glb'], 'models/');
    if ($new_glb) $glb_path = $new_glb;

    $new_usdz = handle_file_upload('usdz_file', ['usdz'], 'models/');
    if ($new_usdz) $usdz_path = $new_usdz;

    $new_thumb = handle_file_upload('thumb_file', ['jpg', 'jpeg', 'png', 'webp'], 'uploads/thumbs/');
    if ($new_thumb) $thumb_path = $new_thumb;

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE products SET category_id=?, slug=?, name=?, description=?, price=?, currency=?, glb_path=?, usdz_path=?, thumb_path=?, width_cm=?, height_cm=?, depth_cm=?, is_active=? WHERE id=?");
        $stmt->execute([$category_id, $slug, $name, $description, $price, $currency, $glb_path, $usdz_path, $thumb_path, $width_cm, $height_cm, $depth_cm, $is_active, $id]);
        $msg = "更新成功！";
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (category_id, slug, name, description, price, currency, glb_path, usdz_path, thumb_path, width_cm, height_cm, depth_cm, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$category_id, $slug, $name, $description, $price, $currency, $glb_path, $usdz_path, $thumb_path, $width_cm, $height_cm, $depth_cm, $is_active]);
        header("Location: index.php");
        exit();
    }
}

render_admin_header($id > 0 ? '编辑产品' : '添加产品');
?>

<!-- 引入 model-viewer 解析 GLB 尺寸 -->
<script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.4.0/model-viewer.min.js"></script>

<h2><?= $id > 0 ? '编辑产品' : '添加产品' ?></h2>
<?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="bg-white p-4 border rounded">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">产品名称</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Slug (别名URL)</label>
            <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($product['slug']) ?>" required>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">所属分类</label>
            <select name="category_id" class="form-select" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">价格</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">货币符号</label>
            <input type="text" name="currency" class="form-control" value="<?= htmlspecialchars($product['currency']) ?>" required>
        </div>
    </div>

    <!-- 尺寸输入区域 + 自动计算提示按钮 -->
    <div class="card p-3 mb-3 bg-light border">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label fw-bold mb-0">产品尺寸 (厘米 / cm)</label>
            <span id="auto-size-status" class="badge bg-secondary">请先选择或上传 GLB 模型文件</span>
        </div>
        <div class="row">
            <div class="col-md-4 mb-2">
                <label class="form-label small">宽度 (Width - X)</label>
                <input type="number" step="0.1" name="width_cm" id="width_cm" class="form-control" value="<?= $product['width_cm'] ?>">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label small">高度 (Height - Y)</label>
                <input type="number" step="0.1" name="height_cm" id="height_cm" class="form-control" value="<?= $product['height_cm'] ?>">
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label small">深度 (Depth - Z)</label>
                <input type="number" step="0.1" name="depth_cm" id="depth_cm" class="form-control" value="<?= $product['depth_cm'] ?>">
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">产品描述</label>
        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">GLB 3D模型文件 (.glb)</label>
            <input type="file" name="glb_file" id="glb_file" class="form-control" accept=".glb">
            <?php if (!empty($product['glb_path'])): ?>
                <small class="text-muted">已存在模型: <?= htmlspecialchars($product['glb_path']) ?></small>
            <?php endif; ?>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">USDZ 3D模型文件 (iOS可选 .usdz)</label>
            <input type="file" name="usdz_file" class="form-control" accept=".usdz">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">产品缩略图</label>
            <input type="file" name="thumb_file" class="form-control" accept="image/*">
        </div>
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?= $product['is_active'] ? 'checked' : '' ?>>
        <label class="form-check-label" for="is_active">启用该产品</label>
    </div>

    <button type="submit" class="btn btn-primary">保存产品</button>
</form>

<!-- 隐藏的模型解析器：改用移出屏幕外的方式，避免 display:none 导致事件不触发 -->
<model-viewer id="temp-model-parser" style="position: absolute; width: 1px; height: 1px; left: -9999px; top: -9999px;"></model-viewer>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const glbFileInput = document.getElementById("glb_file");
    const parser = document.getElementById("temp-model-parser");
    const statusBadge = document.getElementById("auto-size-status");
    
    const inputWidth = document.getElementById("width_cm");
    const inputHeight = document.getElementById("height_cm");
    const inputDepth = document.getElementById("depth_cm");

    // 设置超时机制：防止一直卡在“正在读取...”状态
    let loadTimeout = null;

    // 当用户选择新的 .glb 文件时触发
    glbFileInput.addEventListener("change", (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const modelUrl = URL.createObjectURL(file);

        // 清理上一次的超时定时器
        if (loadTimeout) clearTimeout(loadTimeout);

        statusBadge.className = "badge bg-warning text-dark";
        statusBadge.textContent = "正在读取 3D 模型尺寸...";

        // 使用临时 Blob URL，避免大型 GLB 转成 Data URL 后解析失败
        parser.src = modelUrl;

        // 超时保护：8秒内没响应则提示手动输入
        loadTimeout = setTimeout(() => {
            statusBadge.className = "badge bg-danger";
            statusBadge.textContent = "读取超时，请手动输入尺寸";
            URL.revokeObjectURL(modelUrl);
        }, 8000);
    });

    // 监听模型解析完成事件
    parser.addEventListener("load", () => {
        if (loadTimeout) clearTimeout(loadTimeout);

        try {
            // 获取模型实际物理尺寸 (单位: 米)
            const size = parser.getDimensions();
            
            if (size && (size.x || size.y || size.z)) {
                // 转换为 厘米 (cm)
                inputWidth.value = (size.x * 100).toFixed(1);
                inputHeight.value = (size.y * 100).toFixed(1);
                inputDepth.value = (size.z * 100).toFixed(1);

                statusBadge.className = "badge bg-success";
                statusBadge.textContent = "已根据 3D 模型自动提取真实尺寸！";
            } else {
                throw new Error("尺寸数据为空");
            }
        } catch (err) {
            console.error("尺寸计算失败:", err);
            statusBadge.className = "badge bg-danger";
            statusBadge.textContent = "无法提取模型尺寸，请手动填写";
        }
        URL.revokeObjectURL(parser.src);
    });

    // 监听模型解析错误事件
    parser.addEventListener("error", (err) => {
        if (loadTimeout) clearTimeout(loadTimeout);
        console.error("3D 模型加载出错:", err);
        statusBadge.className = "badge bg-danger";
        statusBadge.textContent = "3D 模型解析失败，请检查文件格式";
        URL.revokeObjectURL(parser.src);
    });
});
</script>

<?php render_admin_footer(); ?>