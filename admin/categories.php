<?php
require_once '../includes/auth.php';
require_once '../includes/categories.php';
require_once '../includes/admin_layout.php';

require_auth();
$categories = get_all_categories(false);

render_admin_header('分类管理');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>分类管理</h2>
    <a href="category_edit.php" class="btn btn-success">+ 添加新分类</a>
</div>

<table class="table table-bordered bg-white">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>分类名称</th>
            <th>Slug</th>
            <th>排序</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $c): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['name']) ?></td>
            <td><?= htmlspecialchars($c['slug']) ?></td>
            <td><?= $c['sort_order'] ?></td>
            <td><?= $c['is_active'] ? '激活' : '禁用' ?></td>
            <td>
                <a href="category_edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-primary">编辑</a>
                <a href="category_delete.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('确定删除该分类吗？');">删除</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php render_admin_footer(); ?>