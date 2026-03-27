<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['delete_id'])) {
            $statement = db()->prepare('DELETE FROM activities WHERE id = :id');
            $statement->execute(['id' => (int) $_POST['delete_id']]);
            redirectWithMessage('activities.php', 'تم حذف النشاط');
        }

        $imagePath = trim((string) ($_POST['image_path'] ?? ''));
        $uploaded = saveUploadedImage('image_file');

        if ($uploaded !== null) {
            $imagePath = $uploaded;
        }

        if ($imagePath === '') {
            throw new RuntimeException('الصورة مطلوبة');
        }

        $data = [
            'title' => trim((string) $_POST['title']),
            'slug' => trim((string) $_POST['slug']),
            'short_description' => trim((string) $_POST['short_description']),
            'full_description' => trim((string) $_POST['full_description']),
            'image' => $imagePath,
            'icon' => trim((string) ($_POST['icon'] ?? '')),
            'color' => trim((string) ($_POST['color'] ?? '')),
            'category' => trim((string) ($_POST['category'] ?? '')),
            'location' => trim((string) ($_POST['location'] ?? '')),
            'starts_at' => $_POST['starts_at'] !== '' ? $_POST['starts_at'] : null,
            'ends_at' => $_POST['ends_at'] !== '' ? $_POST['ends_at'] : null,
            'max_participants' => (int) ($_POST['max_participants'] ?? 0),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($data['title'] === '' || $data['slug'] === '' || $data['short_description'] === '' || $data['full_description'] === '') {
            throw new RuntimeException('أكمل كل الحقول المطلوبة');
        }

        if (isset($_POST['activity_id']) && $_POST['activity_id'] !== '') {
            $statement = db()->prepare(
                'UPDATE activities
                 SET title = :title, slug = :slug, short_description = :short_description, full_description = :full_description,
                     image = :image, icon = :icon, color = :color, category = :category, location = :location,
                     starts_at = :starts_at, ends_at = :ends_at, max_participants = :max_participants,
                     is_featured = :is_featured, is_active = :is_active, updated_at = NOW()
                 WHERE id = :id'
            );
            $statement->execute($data + ['id' => (int) $_POST['activity_id']]);
            redirectWithMessage('activities.php', 'تم تحديث النشاط');
        }

        $statement = db()->prepare(
            'INSERT INTO activities
             (title, slug, short_description, full_description, image, icon, color, category, location, starts_at, ends_at, max_participants, is_featured, is_active, created_by, created_at, updated_at)
             VALUES
             (:title, :slug, :short_description, :full_description, :image, :icon, :color, :category, :location, :starts_at, :ends_at, :max_participants, :is_featured, :is_active, 1, NOW(), NOW())'
        );
        $statement->execute($data);
        redirectWithMessage('activities.php', 'تم إنشاء النشاط');
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$activityToEdit = null;

if ($editId > 0) {
    $statement = db()->prepare('SELECT * FROM activities WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $editId]);
    $activityToEdit = $statement->fetch() ?: null;
}

$activities = db()->query(
    'SELECT a.*, (SELECT COUNT(*) FROM volunteer_applications v WHERE v.activity_id = a.id) AS applications_count
     FROM activities a
     ORDER BY a.created_at DESC'
)->fetchAll();

adminLayoutStart('إدارة الأنشطة');
?>
<h1 class="page-title">إدارة الأنشطة</h1>
<?php flashMessage(is_string($success) ? $success : null, is_string($error) ? $error : null); ?>

<div class="card">
    <h2><?= $activityToEdit ? 'تعديل نشاط' : 'إضافة نشاط جديد' ?></h2>
    <form method="post" enctype="multipart/form-data">
        <?php if ($activityToEdit): ?>
            <input type="hidden" name="activity_id" value="<?= (int) $activityToEdit['id'] ?>">
        <?php endif; ?>
        <div class="grid-2">
            <div>
                <label>العنوان</label>
                <input type="text" name="title" value="<?= htmlspecialchars((string) ($activityToEdit['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div>
                <label>Slug</label>
                <input type="text" name="slug" value="<?= htmlspecialchars((string) ($activityToEdit['slug'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
        </div>

        <div class="grid-2">
            <div>
                <label>الوصف المختصر</label>
                <textarea name="short_description" required><?= htmlspecialchars((string) ($activityToEdit['short_description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div>
                <label>الوصف الكامل</label>
                <textarea name="full_description" required><?= htmlspecialchars((string) ($activityToEdit['full_description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
        </div>

        <div class="grid-3">
            <div>
                <label>أيقونة</label>
                <input type="text" name="icon" value="<?= htmlspecialchars((string) ($activityToEdit['icon'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                <label>لون</label>
                <input type="text" name="color" value="<?= htmlspecialchars((string) ($activityToEdit['color'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                <label>التصنيف</label>
                <input type="text" name="category" value="<?= htmlspecialchars((string) ($activityToEdit['category'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <div class="grid-3">
            <div>
                <label>المكان</label>
                <input type="text" name="location" value="<?= htmlspecialchars((string) ($activityToEdit['location'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                <label>بداية النشاط</label>
                <input type="datetime-local" name="starts_at" value="<?= htmlspecialchars(isset($activityToEdit['starts_at']) && $activityToEdit['starts_at'] ? str_replace(' ', 'T', substr((string) $activityToEdit['starts_at'], 0, 16)) : '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                <label>نهاية النشاط</label>
                <input type="datetime-local" name="ends_at" value="<?= htmlspecialchars(isset($activityToEdit['ends_at']) && $activityToEdit['ends_at'] ? str_replace(' ', 'T', substr((string) $activityToEdit['ends_at'], 0, 16)) : '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <div class="grid-3">
            <div>
                <label>عدد المشاركين</label>
                <input type="number" min="0" name="max_participants" value="<?= htmlspecialchars((string) ($activityToEdit['max_participants'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                <label>صورة من الملفات</label>
                <input type="file" name="image_file" accept=".jpg,.jpeg,.png,.gif,.webp">
            </div>
            <div>
                <label>أو مسار صورة موجود</label>
                <input type="text" name="image_path" value="<?= htmlspecialchars((string) ($activityToEdit['image'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <label><input type="checkbox" name="is_featured" <?= !empty($activityToEdit['is_featured']) ? 'checked' : '' ?>> مميز</label>
        <label><input type="checkbox" name="is_active" <?= !isset($activityToEdit['is_active']) || !empty($activityToEdit['is_active']) ? 'checked' : '' ?>> نشط</label>

        <?php if (!empty($activityToEdit['image'])): ?>
            <div style="margin:10px 0"><img class="preview" src="../<?= htmlspecialchars((string) $activityToEdit['image'], ENT_QUOTES, 'UTF-8') ?>" alt=""></div>
        <?php endif; ?>

        <button type="submit"><?= $activityToEdit ? 'حفظ التعديلات' : 'إضافة النشاط' ?></button>
    </form>
</div>

<div class="card">
    <h2>كل الأنشطة</h2>
    <table>
        <thead>
            <tr><th>الصورة</th><th>العنوان</th><th>التصنيف</th><th>المشاركات</th><th>نشط</th><th>إجراءات</th></tr>
        </thead>
        <tbody>
            <?php foreach ($activities as $item): ?>
                <tr>
                    <td><?php if (!empty($item['image'])): ?><img class="preview" src="../<?= htmlspecialchars((string) $item['image'], ENT_QUOTES, 'UTF-8') ?>" alt=""><?php endif; ?></td>
                    <td><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $item['category'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) $item['applications_count'] ?></td>
                    <td><?= (int) $item['is_active'] === 1 ? 'نعم' : 'لا' ?></td>
                    <td class="row-actions">
                        <a class="badge" href="activities.php?edit=<?= (int) $item['id'] ?>">تعديل</a>
                        <form method="post" onsubmit="return confirm('حذف هذا النشاط؟');">
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
