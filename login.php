<?php
require_once "components/connention.php";
require_once "components/auth.php";

if (is_admin_logged_in()) {
    header('Location: admin-pannel.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admins WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: admin-pannel.php');
            exit;
        }

        $error = 'Invalid admin credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexora Store - Admin Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <main class="login-shell">
        <section class="login-card">
            <a class="brand mini" href="index.html"><span>N</span>Nexora Store</a>
            <h1>Admin Login</h1>
            <p></p>
            <?php if ($error): ?>
                <div class="alert error"><?= e($error) ?></div>
            <?php endif; ?>
            <form method="POST" class="login-form" autocomplete="off">
                <label>Username</label>
                <input type="text" name="username" placeholder="username" required>
                <label>Password</label>
                <input type="password" name="password" placeholder="password" required>
                <button type="submit" class="btn primary full">Login</button>
            </form>
            <small>admin/admin123</small>
        </section>
    </main>
</body>
</html>
