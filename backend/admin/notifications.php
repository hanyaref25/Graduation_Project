<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['notification_id'])) {
            $statement = db()->prepare('UPDATE notifications SET is_read = 1, updated_at = NOW() WHERE id = :id');
            $statement->execute(['id' => (int) $_POST['notification_id']]);
            redirectWithMessage('notifications.php', 'تم تعليم الإشعار كمقروء');
        }

        if (isset($_POST['title'], $_POST['body'])) {
            $title = trim((string) $_POST['title']);
            $body = trim((string) $_POST['body']);
            $type = trim((string) ($_POST['type'] ?? 'general'));

            if ($title === '' || $body === '') {
                throw new RuntimeException('أكمل بيانات الإشعار');
            }

            $statement = db()->prepare(
                'INSERT INTO notifications (user_id, title, body, type, is_read, created_at, updated_at)
                 VALUES (NULL, :title, :body, :type, 0, NOW(), NOW())'
            );
            $statement->execute([
                'title' => $title,
                'body' => $body,
                'type' => $type !== '' ? $type : 'general',
            ]);

            redirectWithMessage('notifications.php', 'تم إنشاء الإشعار');
        }
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$notifications = db()->query('SELECT * FROM notifications ORDER BY created_at DESC')->fetchAll();

adminLayoutStart('الإشعارات');
?>
<h1 class="page-title">الإشعارات</h1>
<?php flashMessage(is_string($success) ? $success : null, is_string($error) ? $error : null); ?>

<div class="card">
    <h2>إنشاء إشعار</h2>
    <form method="post">
        <div class="grid-2">
            <div>
                <label>العنوان</label>
                <input type="text" name="title" required>
            </div>
            <div>
                <label>النوع</label>
                <input type="text" name="type" value="general">
            </div>
        </div>
        <label>المحتوى</label>
        <textarea name="body" required></textarea>
        <button type="submit">إرسال الإشعار</button>
    </form>
</div>

<div class="card">
    <h2>كل الإشعارات</h2>
    <table>
        <thead><tr><th>العنوان</th><th>النوع</th><th>المحتوى</th><th>الحالة</th><th>إجراء</th></tr></thead>
        <tbody>
            <?php foreach ($notifications as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $item['type'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($item['body'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) $item['is_read'] === 1 ? 'مقروء' : 'غير مقروء' ?></td>
                    <td>
                        <?php if ((int) $item['is_read'] === 0): ?>
                            <form method="post">
                                <input type="hidden" name="notification_id" value="<?= (int) $item['id'] ?>">
                                <button type="submit">تعليم كمقروء</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php adminLayoutEnd(); ?>
