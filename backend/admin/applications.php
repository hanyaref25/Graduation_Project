<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $applicationId = (int) ($_POST['application_id'] ?? 0);
        $status = (string) ($_POST['status'] ?? '');

        if ($applicationId <= 0 || !in_array($status, ['pending', 'approved', 'rejected'], true)) {
            throw new RuntimeException('بيانات غير صحيحة');
        }

        $statement = db()->prepare('UPDATE volunteer_applications SET status = :status, updated_at = NOW() WHERE id = :id');
        $statement->execute(['status' => $status, 'id' => $applicationId]);

        $info = db()->prepare('SELECT full_name FROM volunteer_applications WHERE id = :id');
        $info->execute(['id' => $applicationId]);
        $row = $info->fetch();

        $notification = db()->prepare(
            'INSERT INTO notifications (user_id, title, body, type, is_read, created_at, updated_at)
             VALUES (NULL, :title, :body, :type, 0, NOW(), NOW())'
        );
        $notification->execute([
            'title' => 'تغيير حالة طلب',
            'body' => 'تم تحديث حالة طلب ' . ($row['full_name'] ?? 'أحد المتقدمين') . ' إلى ' . $status,
            'type' => 'application',
        ]);

        redirectWithMessage('applications.php', 'تم تحديث حالة الطلب');
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$applications = db()->query(
    'SELECT va.*, a.title AS activity_title
     FROM volunteer_applications va
     LEFT JOIN activities a ON a.id = va.activity_id
     ORDER BY va.created_at DESC'
)->fetchAll();

adminLayoutStart('طلبات الانضمام');
?>
<h1 class="page-title">طلبات الانضمام</h1>
<?php flashMessage(is_string($success) ? $success : null, is_string($error) ? $error : null); ?>

<div class="card">
    <table>
        <thead>
            <tr><th>الاسم</th><th>البريد</th><th>الكلية</th><th>الهاتف</th><th>النشاط</th><th>الرسالة</th><th>الحالة</th><th>إجراء</th></tr>
        </thead>
        <tbody>
            <?php foreach ($applications as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($item['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($item['college'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $item['phone'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($item['activity_title'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($item['message'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="badge"><?= htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td>
                        <form method="post" class="row-actions">
                            <input type="hidden" name="application_id" value="<?= (int) $item['id'] ?>">
                            <select name="status">
                                <option value="pending" <?= $item['status'] === 'pending' ? 'selected' : '' ?>>pending</option>
                                <option value="approved" <?= $item['status'] === 'approved' ? 'selected' : '' ?>>approved</option>
                                <option value="rejected" <?= $item['status'] === 'rejected' ? 'selected' : '' ?>>rejected</option>
                            </select>
                            <button type="submit">حفظ</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php adminLayoutEnd(); ?>
