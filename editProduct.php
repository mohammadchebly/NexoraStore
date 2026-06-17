<?php
require_once "components/connention.php";
require_once "components/auth.php";
require_admin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id AND isDeleted = 1 LIMIT 1");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT * FROM categories WHERE isDeleted = 1 ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: admin-pannel.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | Nexora Store</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <main class="login-shell">
        <section class="login-card">
            <a class="brand mini" href="admin-pannel.php"><span>M</span>Nexora Store</a>
            <h1>Edit Product</h1>
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="alert error"><?= e($_SESSION['error']) ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <form action="actions/editProductAction.php" method="POST" class="admin-form" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
                <input type="text" name="productName" value="<?= e($product['product_name']) ?>" required>
                <textarea name="productDescription" required><?= e($product['product_description']) ?></textarea>
                <input type="number" min="0" step="0.01" name="productPrice" value="<?= e($product['product_price']) ?>" required>
                <select name="categoryId" required>
                    <option value="">Choose Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int)$category['id'] ?>" <?= (int)$product['category_id'] === (int)$category['id'] ? 'selected' : '' ?>><?= e($category['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Current image</label>
                <img src="images/<?= e($product['product_image']) ?>" alt="" style="width:100%;border-radius:18px;max-height:220px;object-fit:cover">
                <label>Replace image (optional)</label>
                <input type="file" name="ProductImage" accept="image/png,image/jpeg,image/webp">
                <button class="btn primary full" type="submit">Save Changes</button>
                <a class="btn ghost full" href="admin-pannel.php">Cancel</a>
            </form>
        </section>
    </main>
</body>
</html>
