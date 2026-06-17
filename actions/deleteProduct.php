<?php
require_once "../components/connention.php";
require_once "../components/auth.php";
require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $sql = "UPDATE products SET isDeleted = 0 WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
}

header("Location: ../admin-pannel.php");
exit;
