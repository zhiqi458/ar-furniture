<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/admin_layout.php';

require_auth();

$id = (int)($_GET['id'] ?? 0);
$category = ['name' => '', 'slug' => '', 'sort_order' => 0, 'is_active' => 1];

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch() ?: $category;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $sort_order = (int)$_POST['sort_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE categories SET name=?, slug=?, sort_order=?, is_active=? WHERE id=?");
        $stmt->execute([$name, $slug, $sort_order, $is_active, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, sort_order, is_active) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $sort_order, $is_active]);
    }
    header("Location: categories.php");
    exit();
}

render_admin_header('编辑分类');
?>
<h2><?= $id > 0 ? '编辑分类' : '添加分类' ?></h2>
<form method="POST" class="bg-white p-4 border rounded">
    <div class="mb-3">
        <label class="form-label">分类名称</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($category['name']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($category['slug']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">排序顺序</label>
        <input type="number" name="sort_order" class="form-control" value="<?= $category['sort_order'] ?>">
    </div>
    <div class="form-check mb-3">
        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?= $category['is_active'] ? 'checked' : '' ?>>
        <label class="form-check-label" for="is_active">激活分类</label>
    </div>
    <button type="submit" class="btn btn-primary">保存</button>
</form>
<?php render_admin_footer(); ?>