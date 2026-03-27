<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

$summary = [
    'activities' => (int) db()->query('SELECT COUNT(*) FROM activities')->fetchColumn(),
    'applications' => (int) db()->query('SELECT COUNT(*) FROM volunteer_applications')->fetchColumn(),
    'pending' => (int) db()->query("SELECT COUNT(*) FROM volunteer_applications WHERE status = 'pending'")->fetchColumn(),
    'announcements' => (int) db()->query('SELECT COUNT(*) FROM announcements')->fetchColumn(),
    'reports' => (int) db()->query('SELECT COUNT(*) FROM activity_reports')->fetchColumn(),
    'notifications' => (int) db()->query('SELECT COUNT(*) FROM notifications WHERE is_read = 0')->fetchColumn(),
];

$applications = db()->query(
    'SELECT va.full_name, va.email, va.status, a.title AS activity_title
     FROM volunteer_applications va
     LEFT JOIN activities a ON a.id = va.activity_id
     ORDER BY va.created_at DESC
     LIMIT 8'
)->fetchAll();

$announcements = db()->query(
    'SELECT title, published_at, is_important
     FROM announcements
     ORDER BY published_at DESC
     LIMIT 5'
)->fetchAll();

adminLayoutStart('لوحة التحكم');
?>
<h1 class="page-title">Dashboard</h1>

<div class="grid-3">
    <div class="card">الأنشطة<br><strong><?= $summary['activities'] ?></strong></div>
    <div class="card">الطلبات<br><strong><?= $summary['applications'] ?></strong></div>
    <div class="card">قيد المراجعة<br><strong><?= $summary['pending'] ?></strong></div>
    <div class="card">الإعلانات<br><strong><?= $summary['announcements'] ?></strong></div>
    <div class="card">التقارير<br><strong><?= $summary['reports'] ?></strong></div>
    <div class="card">إشعارات غير مقروءة<br><strong><?= $summary['notifications'] ?></strong></div>
</div>

<div class="grid-2">
    <div class="card">
        <h2>آخر الطلبات</h2>
        <table>
            <thead>
                <tr><th>الاسم</th><th>البريد</th><th>النشاط</th><th>الحالة</th></tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($item['email'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($item['activity_title'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="badge"><?= htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>آخر الإعلانات</h2>
        <table>
            <thead>
                <tr><th>العنوان</th><th>التاريخ</th><th>مهم</th></tr>
            </thead>
            <tbody>
                <?php foreach ($announcements as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $item['published_at'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $item['is_important'] === 1 ? 'نعم' : 'لا' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php adminLayoutEnd(); ?>
