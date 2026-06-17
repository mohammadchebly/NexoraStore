<?php
require_once "../components/connention.php";
require_once "../components/auth.php";
require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare("UPDATE categories SET isDeleted = 0 WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $stmt = $pdo->prepare("UPDATE products SET category_id = NULL WHERE category_id = :id");
    $stmt->execute([':id' => $id]);
}

header("Location: ../admin-pannel.php");
exit;
