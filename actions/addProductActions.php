<?php
require_once "../components/connention.php";
require_once "../components/auth.php";
require_admin();

$productName = trim($_POST['productName'] ?? '');
$productDescription = trim($_POST['productDescription'] ?? '');
$productPrice = trim($_POST['productPrice'] ?? '');
$categoryId = (int)($_POST['categoryId'] ?? 0);

if ($productName === '' || $productDescription === '' || $productPrice === '' || $categoryId <= 0) {
    $_SESSION['error'] = "Please fill all product fields and choose a category.";
    header("Location: ../admin-pannel.php");
    exit;
}

if (!isset($_FILES['ProductImage']) || !$_FILES['ProductImage']['name']) {
    $_SESSION['error'] = "Please upload an image.";
    header("Location: ../admin-pannel.php");
    exit;
}

$fileType = strtolower(pathinfo($_FILES['ProductImage']['name'], PATHINFO_EXTENSION));
$allowedTypes = ['png', 'jpg', 'jpeg', 'webp'];

if (!in_array($fileType, $allowedTypes, true)) {
    $_SESSION['error'] = "Wrong format. Upload png, jpg, jpeg, or webp.";
    header("Location: ../admin-pannel.php");
    exit;
}

if (!getimagesize($_FILES['ProductImage']['tmp_name'])) {
    $_SESSION['error'] = "Uploaded file is not a real image.";
    header("Location: ../admin-pannel.php");
    exit;
}

if ($_FILES['ProductImage']['size'] > 5000000) {
    $_SESSION['error'] = "Image too large. Maximum size is 5MB.";
    header("Location: ../admin-pannel.php");
    exit;
}

$imgName = "IMG_" . bin2hex(random_bytes(10)) . '.' . $fileType;
$destination = "../images/" . $imgName;

if (!move_uploaded_file($_FILES['ProductImage']['tmp_name'], $destination)) {
    $_SESSION['error'] = "Image upload failed.";
    header("Location: ../admin-pannel.php");
    exit;
}

try {
    $sql = "INSERT INTO products (category_id, product_name, product_description, product_price, product_image) VALUES (:category_id, :product_name, :product_description, :product_price, :product_image)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':category_id' => $categoryId,
        ':product_name' => $productName,
        ':product_description' => $productDescription,
        ':product_price' => $productPrice,
        ':product_image' => $imgName
    ]);
} catch (PDOException $exception) {
    $_SESSION['error'] = "Database error while adding product.";
}

header("Location: ../admin-pannel.php");
exit;
