<?php

declare(strict_types=1);

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../helpers.php';

function adminUser(): array
{
    $user = currentUser();

    if (!$user || ($user['role'] ?? '') !== 'admin') {
        header('Location: login.php');
        exit;
    }

    return $user;
}

function adminLayoutStart(string $title): void
{
    $user = adminUser();
    ?>
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
        <style>
            body { font-family: Tahoma, Arial, sans-serif; background: #f8fafc; margin: 0; color: #0f172a; }
            a { text-decoration: none; }
            .top { display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; background: #0f172a; color: #fff; }
            .top .brand { font-weight: 700; }
            .top .meta { display: flex; gap: 14px; align-items: center; }
            .top .meta a { color: #fff; }
            .shell { display: grid; grid-template-columns: 240px 1fr; min-height: calc(100vh - 64px); }
            .side { background: #111827; padding: 20px 14px; }
            .side a { display: block; color: #e5e7eb; padding: 12px 14px; border-radius: 10px; margin-bottom: 6px; }
            .side a:hover { background: #1f2937; }
            .main { padding: 24px; }
            .page-title { margin-top: 0; margin-bottom: 16px; }
            .card { background: #fff; padding: 18px; border-radius: 14px; box-shadow: 0 6px 20px rgba(15, 23, 42, .06); margin-bottom: 18px; }
            .success { background: #dcfce7; color: #166534; padding: 10px 12px; border-radius: 10px; margin-bottom: 14px; }
            .error { background: #fee2e2; color: #991b1b; padding: 10px 12px; border-radius: 10px; margin-bottom: 14px; }
            .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
            .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
            label { display: block; margin: 10px 0 6px; font-weight: 700; }
            input, textarea, select { width: 100%; padding: 11px 12px; box-sizing: border-box; border: 1px solid #d1d5db; border-radius: 10px; background: #fff; }
            textarea { min-height: 110px; resize: vertical; }
            button { padding: 11px 18px; border: 0; background: #0f766e; color: #fff; border-radius: 10px; cursor: pointer; }
            .danger { background: #b91c1c; }
            .muted { color: #475569; font-size: 14px; }
            .inline { display: inline-block; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border-bottom: 1px solid #e2e8f0; padding: 10px 8px; text-align: right; vertical-align: top; }
            .row-actions { display: flex; gap: 8px; flex-wrap: wrap; }
            .row-actions form { display: inline-block; }
            .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 12px; background: #e2e8f0; }
            img.preview { max-width: 140px; max-height: 90px; border-radius: 10px; object-fit: cover; }
            @media (max-width: 980px) {
                .shell { grid-template-columns: 1fr; }
                .grid-2, .grid-3 { grid-template-columns: 1fr; }
            }
        </style>
    </head>
    <body>
        <div class="top">
            <div class="brand">لوحة إدارة المبادرات</div>
            <div class="meta">
                <span><?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?></span>
                <a href="dashboard.php">الرئيسية</a>
                <a href="logout.php">تسجيل خروج</a>
            </div>
        </div>
        <div class="shell">
            <div class="side">
                <a href="dashboard.php">Dashboard</a>
                <a href="activities.php">إدارة الأنشطة</a>
                <a href="applications.php">طلبات الانضمام</a>
                <a href="announcements.php">الإعلانات</a>
                <a href="reports.php">التقارير</a>
                <a href="notifications.php">الإشعارات</a>
                <a href="gallery.php">المعرض</a>
            </div>
            <div class="main">
    <?php
}

function adminLayoutEnd(): void
{
    ?>
            </div>
        </div>
    </body>
    </html>
    <?php
}

function flashMessage(?string $success, ?string $error): void
{
    if ($success) {
        echo '<div class="success">' . htmlspecialchars($success, ENT_QUOTES, 'UTF-8') . '</div>';
    }

    if ($error) {
        echo '<div class="error">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div>';
    }
}

function redirectWithMessage(string $path, string $message, string $type = 'success'): never
{
    $separator = str_contains($path, '?') ? '&' : '?';
    header('Location: ' . $path . $separator . $type . '=' . urlencode($message));
    exit;
}

function saveUploadedImage(string $fieldName): ?string
{
    if (!isset($_FILES[$fieldName]) || ($_FILES[$fieldName]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($_FILES[$fieldName]['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('فشل رفع الصورة');
    }

    $tmpPath = (string) $_FILES[$fieldName]['tmp_name'];
    $original = (string) $_FILES[$fieldName]['name'];
    $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($extension, $allowed, true)) {
        throw new RuntimeException('نوع الصورة غير مدعوم');
    }

    $uploadDir = realpath(__DIR__ . '/../uploads');

    if ($uploadDir === false) {
        throw new RuntimeException('مجلد الرفع غير موجود');
    }

    $newName = uniqid('img_', true) . '.' . $extension;
    $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $newName;

    if (!move_uploaded_file($tmpPath, $targetPath)) {
        throw new RuntimeException('تعذر حفظ الصورة على الخادم');
    }

    return 'uploads/' . $newName;
}
