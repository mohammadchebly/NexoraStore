<?php
require_once "../components/connention.php";
require_once "../components/auth.php";
require_admin();

$categoryName = trim($_POST['categoryName'] ?? '');
if ($categoryName === '') {
    $_SESSION['error'] = "Please enter a category name.";
    header("Location: ../admin-pannel.php");
    exit;
}

try {
    $slug = unique_category_slug($pdo, $categoryName);
    $stmt = $pdo->prepare("INSERT INTO categories (category_name, category_slug) VALUES (:category_name, :category_slug)");
    $stmt->execute([':category_name' => $categoryName, ':category_slug' => $slug]);
    $_SESSION['success'] = "Category added successfully.";
} catch (PDOException $exception) {
    $_SESSION['error'] = "Database error while adding category.";
}

header("Location: ../admin-pannel.php");
exit;
