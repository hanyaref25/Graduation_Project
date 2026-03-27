<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['delete_id'])) {
            $statement = db()->prepare('DELETE FROM gallery_items WHERE id = :id');
            $statement->execute(['id' => (int) $_POST['delete_id']]);
            redirectWithMessage('gallery.php', 'تم حذف العنصر');
        }

        $imagePath = trim((string) ($_POST['existing_image'] ?? ''));
        $uploaded = saveUploadedImage('image_file');

        if ($uploaded !== null) {
            $imagePath = $uploaded;
        }

        if ($imagePath === '') {
            throw new RuntimeException('الصورة مطلوبة');
        }

        $statement = db()->prepare(
            'INSERT INTO gallery_items (activity_id, title, image_path, report_excerpt, captured_at, created_at, updated_at)
             VALUES (:activity_id, :title, :image_path, :report_excerpt, :captured_at, NOW(), NOW())'
        );
        $statement->execute([
            'activity_id' => $_POST['activity_id'] !== '' ? (int) $_POST['activity_id'] : null,
            'title' => trim((string) $_POST['title']),
            'image_path' => $imagePath,
            'report_excerpt' => trim((string) ($_POST['report_excerpt'] ?? '')),
            'captured_at' => $_POST['captured_at'] !== '' ? $_POST['captured_at'] : date('Y-m-d H:i:s'),
        ]);

        redirectWithMessage('gallery.php', 'تم إضافة عنصر للمعرض');
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$activities = db()->query('SELECT id, title FROM activities ORDER BY title ASC')->fetchAll();
$items = db()->query(
    'SELECT g.*, a.title AS activity_title
     FROM gallery_items g
     LEFT JOIN activities a ON a.id = g.activity_id
     ORDER BY g.captured_at DESC, g.id DESC'
)->fetchAll();

adminLayoutStart('المعرض');
?>
<h1 class="page-title">المعرض والتقارير المصورة</h1>
<?php flashMessage(is_string($success) ? $success : null, is_string($error) ? $error : null); ?>

<div class="card">
    <h2>إضافة عنصر جديد</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="grid-2">
            <div>
                <label>عنوان العنصر</label>
                <input type="text" name="title" required>
            </div>
            <div>
                <label>النشاط المرتبط</label>
                <select name="activity_id">
                    <option value="">بدون نشاط</option>
                    <?php foreach ($activities as $activity): ?>
                        <option value="<?= (int) $activity['id'] ?>"><?= htmlspecialchars($activity['title'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="grid-2">
            <div>
                <label>صورة من الملفات</label>
                <input type="file" name="image_file" accept=".jpg,.jpeg,.png,.gif,.webp">
            </div>
            <div>
                <label>أو مسار صورة موجودة</label>
                <input type="text" name="existing_image">
            </div>
        </div>
        <label>ملخص</label>
        <textarea name="report_excerpt"></textarea>
        <label>تاريخ الالتقاط</label>
        <input type="datetime-local" name="captured_at">
        <button type="submit">إضافة للمعرض</button>
    </form>
</div>

<div class="card">
    <h2>العناصر الحالية</h2>
    <table>
        <thead><tr><th>الصورة</th><th>العنوان</th><th>النشاط</th><th>الملخص</th><th>إجراء</th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php if (!empty($item['image_path'])): ?><img class="preview" src="../<?= htmlspecialchars((string) $item['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt=""><?php endif; ?></td>
                    <td><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($item['activity_title'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $item['report_excerpt'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <form method="post" onsubmit="return confirm('حذف هذا العنصر؟');">
                            <input type="hidden" name="delete_id" value="<?= (int) $item['id'] ?>">
                            <button class="danger" type="submit">حذف</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php adminLayoutEnd(); ?>
