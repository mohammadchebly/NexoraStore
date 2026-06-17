<?php
require_once "components/connention.php";
require_once "components/auth.php";
require_admin();

$productsSql = "SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.isDeleted = 1 ORDER BY p.date_created DESC";
$products = $pdo->query($productsSql)->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT * FROM categories WHERE isDeleted = 1 ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora Store - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <a class="brand" href="index.html"><span>N</span>Nexora Store</a>
            <nav class="side-menu">
                <a href="admin-pannel.php" class="active">Dashboard</a>
                <a href="index.html">View Store</a>
                <a href="logout.php">Logout</a>
            </nav>
        </aside>

        <main class="admin-main">
            <header class="admin-top">
                <div>
                    <h1>Admin Dashboard</h1>
                    <p>Welcome, <?= e($_SESSION['admin_username']) ?>. Manage products and categories securely.</p>
                </div>
                <a href="logout.php" class="btn ghost">Logout</a>
            </header>

            <?php if (!empty($_SESSION['error'])): ?>
                <div class="alert error"><?= e($_SESSION['error']) ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['success'])): ?>
                <div class="alert success"><?= e($_SESSION['success']) ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <section class="metrics">
                <div class="metric"><span>Total Products</span><strong><?= count($products) ?></strong></div>
                <div class="metric"><span>Total Categories</span><strong><?= count($categories) ?></strong></div>
                <div class="metric"><span>Total Orders</span><strong>0</strong></div>
                <div class="metric"><span>Total Revenue</span><strong>$0</strong></div>
            </section>

            <section class="admin-grid categories-admin">
                <div class="admin-card">
                    <h2>Add Category</h2>
                    <form action="actions/addCategoryAction.php" method="POST" class="admin-form">
                        <input type="text" placeholder="Category Name, e.g. iPhone" name="categoryName" required>
                        <button type="submit" class="btn primary full">Add Category</button>
                    </form>
                </div>

                <div class="panel table-wrap">
                    <h2>Category List</h2>
                    <table>
                        <thead><tr><th>Name</th><th>Slug</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php if (!$categories): ?>
                                <tr><td colspan="3" class="empty">No categories yet.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td>
                                        <form action="actions/editCategoryAction.php" method="POST" class="inline-form">
                                            <input type="hidden" name="id" value="<?= (int)$category['id'] ?>">
                                            <input type="text" name="categoryName" value="<?= e($category['category_name']) ?>" required>
                                            <button class="btn ghost" type="submit">Save</button>
                                        </form>
                                    </td>
                                    <td><?= e($category['category_slug']) ?></td>
                                    <td><a href="actions/deleteCategory.php?id=<?= (int)$category['id'] ?>" class="btn danger" onclick="return confirm('Delete this category? Products in it will become uncategorized.')">Delete</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="admin-grid">
                <div class="admin-card">
                    <h2>Add Product</h2>
                    <form action="actions/addProductActions.php" method="POST" class="admin-form" enctype="multipart/form-data">
                        <input type="text" placeholder="Product Name" name="productName" required>
                        <textarea placeholder="Product Description" name="productDescription" required></textarea>
                        <input type="number" min="0" step="0.01" placeholder="Product Price" name="productPrice" required>
                        <select name="categoryId" required>
                            <option value="">Choose Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int)$category['id'] ?>"><?= e($category['category_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="file" name="ProductImage" accept="image/png,image/jpeg,image/webp" required>
                        <button type="submit" class="btn primary full">Add Product</button>
                    </form>
                </div>

                <div class="panel table-wrap">
                    <h2>Product List</h2>
                    <table>
                        <thead>
                            <tr><th>Image</th><th>Name</th><th>Category</th><th>Description</th><th>Price</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php if (!$products): ?>
                                <tr><td colspan="6" class="empty">No products yet.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><img src="images/<?= e($product['product_image']) ?>" alt=""></td>
                                    <td><?= e($product['product_name']) ?></td>
                                    <td><?= e($product['category_name'] ?? 'Uncategorized') ?></td>
                                    <td><?= e($product['product_description']) ?></td>
                                    <td>$<?= e($product['product_price']) ?></td>
                                    <td class="actions">
                                        <a href="editProduct.php?id=<?= (int)$product['id'] ?>" class="btn ghost">Edit</a>
                                        <a href="actions/deleteProduct.php?id=<?= (int)$product['id'] ?>" class="btn danger" onclick="return confirm('Delete this product?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
