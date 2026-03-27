<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['delete_id'])) {
            $statement = db()->prepare('DELETE FROM announcements WHERE id = :id');
            $statement->execute(['id' => (int) $_POST['delete_id']]);
            redirectWithMessage('announcements.php', 'تم حذف الإعلان');
        }

        $data = [
            'title' => trim((string) $_POST['title']),
            'content' => trim((string) $_POST['content']),
            'published_at' => $_POST['published_at'] !== '' ? $_POST['published_at'] : date('Y-m-d H:i:s'),
            'is_important' => isset($_POST['is_important']) ? 1 : 0,
        ];

        if ($data['title'] === '' || $data['content'] === '') {
            throw new RuntimeException('أكمل العنوان والمحتوى');
        }

        if (!empty($_POST['announcement_id'])) {
            $statement = db()->prepare(
                'UPDATE announcements
                 SET title = :title, content = :content, published_at = :published_at, is_important = :is_important, updated_at = NOW()
                 WHERE id = :id'
            );
            $statement->execute($data + ['id' => (int) $_POST['announcement_id']]);
            redirectWithMessage('announcements.php', 'تم تحديث الإعلان');
        }

        $statement = db()->prepare(
            'INSERT INTO announcements (title, content, published_at, is_important, created_by, created_at, updated_at)
             VALUES (:title, :content, :published_at, :is_important, 1, NOW(), NOW())'
        );
        $statement->execute($data);
        redirectWithMessage('announcements.php', 'تم إنشاء الإعلان');
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$announcementToEdit = null;
if ($editId > 0) {
    $statement = db()->prepare('SELECT * FROM announcements WHERE id = :id');
    $statement->execute(['id' => $editId]);
    $announcementToEdit = $statement->fetch() ?: null;
}

$announcements = db()->query('SELECT * FROM announcements ORDER BY published_at DESC')->fetchAll();

adminLayoutStart('الإعلانات');
?>
<h1 class="page-title">إدارة الإعلانات</h1>
<?php flashMessage(is_string($success) ? $success : null, is_string($error) ? $error : null); ?>

<div class="card">
    <h2><?= $announcementToEdit ? 'تعديل إعلان' : 'إضافة إعلان' ?></h2>
    <form method="post">
        <?php if ($announcementToEdit): ?>
            <input type="hidden" name="announcement_id" value="<?= (int) $announcementToEdit['id'] ?>">
        <?php endif; ?>
        <label>العنوان</label>
        <input type="text" name="title" value="<?= htmlspecialchars((string) ($announcementToEdit['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
        <label>المحتوى</label>
        <textarea name="content" required><?= htmlspecialchars((string) ($announcementToEdit['content'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
        <label>تاريخ النشر</label>
        <input type="datetime-local" name="published_at" value="<?= htmlspecialchars(isset($announcementToEdit['published_at']) && $announcementToEdit['published_at'] ? str_replace(' ', 'T', substr((string) $announcementToEdit['published_at'], 0, 16)) : '', ENT_QUOTES, 'UTF-8') ?>">
        <label><input type="checkbox" name="is_important" <?= !empty($announcementToEdit['is_important']) ? 'checked' : '' ?>> إعلان مهم</label>
        <button type="submit"><?= $announcementToEdit ? 'حفظ التعديلات' : 'إضافة الإعلان' ?></button>
    </form>
</div>

<div class="card">
    <h2>كل الإعلانات</h2>
    <table>
        <thead><tr><th>العنوان</th><th>النشر</th><th>مهم</th><th>إجراءات</th></tr></thead>
        <tbody>
            <?php foreach ($announcements as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $item['published_at'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) $item['is_important'] === 1 ? 'نعم' : 'لا' ?></td>
                    <td class="row-actions">
                        <a class="badge" href="announcements.php?edit=<?= (int) $item['id'] ?>">تعديل</a>
                        <form method="post" onsubmit="return confirm('حذف هذا الإعلان؟');">
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
