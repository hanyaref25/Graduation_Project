<?php

declare(strict_types=1);

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../helpers.php';

startSessionIfNeeded();

if (currentUser() && (currentUser()['role'] ?? '') === 'admin') {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $statement = db()->prepare('SELECT id, full_name, email, password_hash, role, is_active FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => $email]);
    $user = $statement->fetch();

    if ($user && (bool) $user['is_active'] && $user['role'] === 'admin' && passwordMatches($password, (string) $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        header('Location: dashboard.php');
        exit;
    }

    $error = 'بيانات الدخول غير صحيحة';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول الإدارة</title>
    <style>
        body { font-family: Tahoma, Arial, sans-serif; background: #f4f6f8; margin: 0; }
        .wrap { max-width: 420px; margin: 80px auto; background: #fff; padding: 24px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,.08); }
        h1 { margin-top: 0; font-size: 24px; }
        label { display: block; margin: 12px 0 6px; }
        input { width: 100%; padding: 12px; border: 1px solid #d0d7de; border-radius: 10px; box-sizing: border-box; }
        button { width: 100%; margin-top: 16px; padding: 12px; background: #0f766e; color: #fff; border: 0; border-radius: 10px; cursor: pointer; }
        .error { background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 10px; margin-bottom: 12px; }
        .hint { color: #475569; font-size: 14px; margin-top: 12px; }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>تسجيل دخول الإدارة</h1>
        <?php if ($error !== ''): ?>
            <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <form method="post">
            <label>البريد الإلكتروني</label>
            <input type="email" name="email" required>
            <label>كلمة المرور</label>
            <input type="password" name="password" required>
            <button type="submit">دخول</button>
        </form>
        <div class="hint">الحساب الافتراضي موجود داخل ملف قاعدة البيانات.</div>
    </div>
</body>
</html>
