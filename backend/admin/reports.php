<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            'activity_id' => (int) ($_POST['activity_id'] ?? 0),
            'title' => trim((string) ($_POST['title'] ?? '')),
            'summary' => trim((string) ($_POST['summary'] ?? '')),
            'participants_count' => (int) ($_POST['participants_count'] ?? 0),
            'report_date' => (string) ($_POST['report_date'] ?? date('Y-m-d')),
        ];

        if ($data['activity_id'] <= 0 || $data['title'] === '' || $data['summary'] === '') {
            throw new RuntimeException('أكمل بيانات التقرير');
        }

        $statement = db()->prepare(
            'INSERT INTO activity_reports (activity_id, title, summary, participants_count, report_date, created_by, created_at, updated_at)
             VALUES (:activity_id, :title, :summary, :participants_count, :report_date, 1, NOW(), NOW())'
        );
        $statement->execute($data);
        redirectWithMessage('reports.php', 'تم حفظ التقرير');
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$activities = db()->query('SELECT id, title FROM activities WHERE is_active = 1 ORDER BY title ASC')->fetchAll();
$reports = db()->query(
    'SELECT ar.*, a.title AS activity_title
     FROM activity_reports ar
     LEFT JOIN activities a ON a.id = ar.activity_id
     ORDER BY ar.report_date DESC, ar.id DESC'
)->fetchAll();

adminLayoutStart('التقارير');
?>
<h1 class="page-title">التقارير</h1>
<?php flashMessage(is_string($success) ? $success : null, is_string($error) ? $error : null); ?>

<div class="card">
    <h2>إضافة تقرير</h2>
    <form method="post">
        <div class="grid-2">
            <div>
                <label>النشاط</label>
                <select name="activity_id" required>
                    <option value="">اختر النشاط</option>
                    <?php foreach ($activities as $activity): ?>
                        <option value="<?= (int) $activity['id'] ?>"><?= htmlspecialchars($activity['title'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>تاريخ التقرير</label>
                <input type="date" name="report_date" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>
        <div class="grid-2">
            <div>
                <label>عنوان التقرير</label>
                <input type="text" name="title" required>
            </div>
            <div>
                <label>عدد المشاركين</label>
                <input type="number" min="0" name="participants_count" value="0">
            </div>
        </div>
        <label>الملخص</label>
        <textarea name="summary" required></textarea>
        <button type="submit">حفظ التقرير</button>
    </form>
</div>

<div class="card">
    <h2>كل التقارير</h2>
    <table>
        <thead><tr><th>العنوان</th><th>النشاط</th><th>عدد المشاركين</th><th>التاريخ</th><th>الملخص</th></tr></thead>
        <tbody>
            <?php foreach ($reports as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($item['activity_title'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) $item['participants_count'] ?></td>
                    <td><?= htmlspecialchars((string) $item['report_date'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($item['summary'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php adminLayoutEnd(); ?>
