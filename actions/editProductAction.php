<?php
require_once "../components/connention.php";
require_once "../components/auth.php";
require_admin();

$id = (int)($_POST['id'] ?? 0);
$productName = trim($_POST['productName'] ?? '');
$productDescription = trim($_POST['productDescription'] ?? '');
$productPrice = trim($_POST['productPrice'] ?? '');
$categoryId = (int)($_POST['categoryId'] ?? 0);

if ($id <= 0 || $productName === '' || $productDescription === '' || $productPrice === '' || $categoryId <= 0) {
    $_SESSION['error'] = "Please fill all fields.";
    header("Location: ../admin-pannel.php");
    exit;
}

$imageSql = '';
$params = [
    ':id' => $id,
    ':category_id' => $categoryId,
    ':product_name' => $productName,
    ':product_description' => $productDescription,
    ':product_price' => $productPrice
];

if (isset($_FILES['ProductImage']) && $_FILES['ProductImage']['name']) {
    $fileType = strtolower(pathinfo($_FILES['ProductImage']['name'], PATHINFO_EXTENSION));
    $allowedTypes = ['png', 'jpg', 'jpeg', 'webp'];

    if (!in_array($fileType, $allowedTypes, true) || !getimagesize($_FILES['ProductImage']['tmp_name']) || $_FILES['ProductImage']['size'] > 5000000) {
        $_SESSION['error'] = "Invalid image. Use png, jpg, jpeg, or webp under 5MB.";
        header("Location: ../editProduct.php?id=" . $id);
        exit;
    }

    $imgName = "IMG_" . bin2hex(random_bytes(10)) . '.' . $fileType;
    move_uploaded_file($_FILES['ProductImage']['tmp_name'], "../images/" . $imgName);
    $imageSql = ', product_image = :product_image';
    $params[':product_image'] = $imgName;
}

$sql = "UPDATE products SET category_id = :category_id, product_name = :product_name, product_description = :product_description, product_price = :product_price $imageSql WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

header("Location: ../admin-pannel.php");
exit;
