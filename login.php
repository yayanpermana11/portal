<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    // Ganti sesuai keinginan
    if ($username === 'yayanpermana' && $password === '123456') {
        $_SESSION['logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - Portal Link Sekolah</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: #fff; padding: 30px; border-radius: 14px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); width: 320px; }
        h2 { margin: 0 0 20px; text-align: center; }
        .form-group { margin-bottom: 14px; }
        label { font-weight: 600; display: block; margin-bottom: 4px; }
        input { width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 0.95rem; outline: none; }
        input:focus { border-color: #4f46e5; }
        button { width: 100%; padding: 11px; background: #4f46e5; color: #fff; font-weight: 600; border: none; border-radius: 50px; cursor: pointer; font-size: 1rem; }
        button:hover { background: #4338ca; }
        .error { color: #ef4444; font-size: 0.9rem; text-align: center; margin-bottom: 10px; }
        .back-link { display: block; text-align: center; margin-top: 16px; color: #4f46e5; text-decoration: none; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🔐 Login Admin</h2>
        <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <form method="post">
            <div class="form-group"><label>👤 Username</label><input type="text" name="username" required autofocus></div>
            <div class="form-group"><label>🔑 Password</label><input type="password" name="password" required></div>
            <button type="submit">Masuk</button>
        </form>
        <a href="index.php" class="back-link">← Kembali ke Portal</a>
    </div>
</body>
</html>