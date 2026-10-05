<?php
require_once '../includes/auth.php';
require_once '../includes/products.php';
require_once '../includes/admin_layout.php';

require_auth();
$products = get_all_products(0, false);

render_admin_header('产品管理');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>产品列表</h2>
    <a href="product_edit.php" class="btn btn-success">+ 添加新产品</a>
</div>

<table class="table table-bordered table-striped bg-white">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>缩略图</th>
            <th>名称</th>
            <th>分类</th>
            <th>价格</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><img src="../<?= htmlspecialchars($p['thumb_path']) ?>" style="height: 40px;" alt=""></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['category_name']) ?></td>
            <td><?= format_price($p['price'], $p['currency']) ?></td>
            <td>
                <span class="badge <?= $p['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                    <?= $p['is_active'] ? '已激活' : '禁用' ?>
                </span>
            </td>
            <td>
                <a href="product_edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-primary">编辑</a>
                <a href="product_delete.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('确定删除该产品？');">删除</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php render_admin_footer(); ?>