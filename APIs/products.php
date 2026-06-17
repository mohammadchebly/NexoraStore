<?php
require_once "../components/connention.php";

header("Content-Type: application/json");

$categoryId = (int)($_GET['category_id'] ?? 0);
$params = [];
$where = "WHERE p.isDeleted = 1";

if ($categoryId > 0) {
    $where .= " AND p.category_id = :category_id";
    $params[':category_id'] = $categoryId;
}

$sql = "SELECT p.*, c.category_name, c.category_slug
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        $where
        ORDER BY p.date_created DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = $pdo->query("SELECT id, category_name, category_slug FROM categories WHERE isDeleted = 1 ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "message" => "fill this products in the index page",
    "products" => $products,
    "categories" => $categories
]);
