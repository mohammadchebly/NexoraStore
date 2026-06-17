<?php
require_once "../components/connention.php";
require_once "../components/auth.php";
require_admin();

$id = (int)($_POST['id'] ?? 0);
$categoryName = trim($_POST['categoryName'] ?? '');

if ($id <= 0 || $categoryName === '') {
    $_SESSION['error'] = "Please enter a valid category name.";
    header("Location: ../admin-pannel.php");
    exit;
}

try {
    $slug = unique_category_slug($pdo, $categoryName, $id);
    $stmt = $pdo->prepare("UPDATE categories SET category_name = :category_name, category_slug = :category_slug WHERE id = :id");
    $stmt->execute([':category_name' => $categoryName, ':category_slug' => $slug, ':id' => $id]);
    $_SESSION['success'] = "Category updated successfully.";
} catch (PDOException $exception) {
    $_SESSION['error'] = "Database error while updating category.";
}

header("Location: ../admin-pannel.php");
exit;
