<?php

declare(strict_types=1);

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';

startSessionIfNeeded();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    sendJson(['message' => 'ok']);
}

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/api.php';
$path = '/';

if (isset($_SERVER['PATH_INFO'])) {
    $path = (string) $_SERVER['PATH_INFO'];
} else {
    $basePosition = strpos($requestUri, $scriptName);

    if ($basePosition !== false) {
        $path = substr($requestUri, $basePosition + strlen($scriptName)) ?: '/';
    }
}

$path = strtok($path, '?') ?: '/';
$path = '/' . trim($path, '/');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    if ($method === 'POST' && $path === '/auth/login') {
        $data = readRequestData();
        $errors = requireFields($data, ['email', 'password']);

        if ($errors) {
            sendJson(['message' => 'بيانات تسجيل الدخول غير مكتملة', 'errors' => $errors], 422);
        }

        $statement = db()->prepare(
            'SELECT id, full_name, email, password_hash, role, is_active
             FROM users
             WHERE email = :email
             LIMIT 1'
        );
        $statement->execute(['email' => trim((string) $data['email'])]);
        $user = $statement->fetch();

        if (!$user || !(bool) $user['is_active'] || !passwordMatches((string) $data['password'], (string) $user['password_hash'])) {
            sendJson(['message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'], 401);
        }

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        sendJson(['message' => 'تم تسجيل الدخول بنجاح', 'data' => $_SESSION['user']]);
    }

    if ($method === 'POST' && $path === '/auth/logout') {
        $_SESSION = [];
        session_destroy();
        sendJson(['message' => 'تم تسجيل الخروج']);
    }

    if ($method === 'GET' && $path === '/auth/me') {
        sendJson(['data' => currentUser()]);
    }

    if ($method === 'GET' && $path === '/health') {
        sendJson(['status' => 'ok', 'service' => 'student-initiative-platform-php-api']);
    }

    if ($method === 'GET' && $path === '/activities') {
        $statement = db()->query(
            'SELECT id, title, slug, short_description, full_description, image, icon, color, category, location, starts_at, ends_at, max_participants, is_featured, is_active
             FROM activities
             WHERE is_active = 1
             ORDER BY is_featured DESC, starts_at ASC'
        );

        $activities = array_map(function (array $row): array {
            $countStatement = db()->prepare('SELECT COUNT(*) FROM volunteer_applications WHERE activity_id = :activity_id');
            $countStatement->execute(['activity_id' => $row['id']]);
            $participantCount = (int) $countStatement->fetchColumn();

            return [
                'id' => (int) $row['id'],
                'title' => $row['title'],
                'slug' => $row['slug'],
                'description' => $row['short_description'],
                'fullDescription' => $row['full_description'],
                'image' => $row['image'],
                'icon' => $row['icon'],
                'color' => $row['color'],
                'category' => $row['category'],
                'location' => $row['location'],
                'startsAt' => $row['starts_at'],
                'endsAt' => $row['ends_at'],
                'maxParticipants' => (int) $row['max_participants'],
                'participantCount' => $participantCount,
                'availableSeats' => max((int) $row['max_participants'] - $participantCount, 0),
            ];
        }, $statement->fetchAll());

        sendJson(['data' => $activities]);
    }

    if ($method === 'GET' && preg_match('#^/activities/([^/]+)$#', $path, $matches) === 1) {
        $slug = urldecode($matches[1]);
        $statement = db()->prepare(
            'SELECT id, title, slug, short_description, full_description, image, icon, color, category, location, starts_at, ends_at, max_participants
             FROM activities
             WHERE slug = :slug AND is_active = 1
             LIMIT 1'
        );
        $statement->execute(['slug' => $slug]);
        $activity = $statement->fetch();

        if (!$activity) {
            sendJson(['message' => 'النشاط غير موجود'], 404);
        }

        $featuresStatement = db()->prepare('SELECT title FROM gallery_items WHERE activity_id = :activity_id ORDER BY captured_at DESC LIMIT 4');
        $featuresStatement->execute(['activity_id' => $activity['id']]);
        $features = array_values(array_filter(array_column($featuresStatement->fetchAll(), 'title')));

        sendJson([
            'data' => [
                'id' => (int) $activity['id'],
                'title' => $activity['title'],
                'slug' => $activity['slug'],
                'description' => $activity['short_description'],
                'fullDescription' => $activity['full_description'],
                'image' => $activity['image'],
                'icon' => $activity['icon'],
                'color' => $activity['color'],
                'category' => $activity['category'],
                'location' => $activity['location'],
                'startsAt' => $activity['starts_at'],
                'endsAt' => $activity['ends_at'],
                'maxParticipants' => (int) $activity['max_participants'],
                'features' => $features,
            ],
        ]);
    }

    if ($method === 'POST' && $path === '/applications') {
        $data = readRequestData();
        $errors = requireFields($data, ['full_name', 'email', 'college', 'message']);

        if (!empty($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'البريد الإلكتروني غير صالح';
        }

        if ($errors) {
            sendJson(['message' => 'بيانات غير مكتملة', 'errors' => $errors], 422);
        }

        $statement = db()->prepare(
            'INSERT INTO volunteer_applications (activity_id, full_name, email, college, phone, message, status, created_at, updated_at)
             VALUES (:activity_id, :full_name, :email, :college, :phone, :message, :status, NOW(), NOW())'
        );
        $statement->execute([
            'activity_id' => jsonInputValue($data, 'activity_id'),
            'full_name' => trim((string) $data['full_name']),
            'email' => trim((string) $data['email']),
            'college' => trim((string) $data['college']),
            'phone' => trim((string) jsonInputValue($data, 'phone', '')),
            'message' => trim((string) $data['message']),
            'status' => 'pending',
        ]);

        sendJson(['message' => 'تم استلام طلبك بنجاح', 'data' => ['id' => (int) db()->lastInsertId(), 'status' => 'pending']], 201);
    }

    if ($method === 'GET' && $path === '/admin/applications') {
        requireAdmin();
        $statement = db()->query(
            'SELECT va.id, va.full_name, va.email, va.college, va.phone, va.message, va.status, va.created_at,
                    a.title AS activity_title, a.slug AS activity_slug
             FROM volunteer_applications va
             LEFT JOIN activities a ON a.id = va.activity_id
             ORDER BY va.created_at DESC'
        );

        $items = array_map(function (array $row): array {
            return [
                'id' => (int) $row['id'],
                'fullName' => $row['full_name'],
                'email' => $row['email'],
                'college' => $row['college'],
                'phone' => $row['phone'],
                'message' => $row['message'],
                'status' => $row['status'],
                'createdAt' => $row['created_at'],
                'activity' => $row['activity_slug'] ? ['title' => $row['activity_title'], 'slug' => $row['activity_slug']] : null,
            ];
        }, $statement->fetchAll());

        sendJson(['data' => $items]);
    }

    if (($method === 'PUT' || $method === 'POST') && preg_match('#^/admin/applications/([0-9]+)$#', $path, $matches) === 1) {
        requireAdmin();
        $applicationId = (int) $matches[1];
        $data = readRequestData();
        $status = (string) jsonInputValue($data, 'status', '');

        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
            sendJson(['message' => 'حالة الطلب غير صحيحة'], 422);
        }

        $statement = db()->prepare('UPDATE volunteer_applications SET status = :status, updated_at = NOW() WHERE id = :id');
        $statement->execute(['status' => $status, 'id' => $applicationId]);

        $appStatement = db()->prepare('SELECT full_name FROM volunteer_applications WHERE id = :id LIMIT 1');
        $appStatement->execute(['id' => $applicationId]);
        $application = $appStatement->fetch();

        $notificationStatement = db()->prepare(
            'INSERT INTO notifications (user_id, title, body, type, is_read, created_at, updated_at)
             VALUES (NULL, :title, :body, :type, 0, NOW(), NOW())'
        );
        $notificationStatement->execute([
            'title' => 'تحديث حالة طلب انضمام',
            'body' => 'تم تغيير حالة طلب ' . ($application['full_name'] ?? 'المتقدم') . ' إلى ' . $status,
            'type' => 'application',
        ]);

        sendJson(['message' => 'تم تحديث حالة الطلب']);
    }

    if ($method === 'GET' && $path === '/announcements') {
        $statement = db()->query('SELECT id, title, content, published_at, is_important FROM announcements ORDER BY is_important DESC, published_at DESC');
        sendJson(['data' => $statement->fetchAll()]);
    }

    if ($method === 'POST' && $path === '/admin/announcements') {
        requireAdmin();
        $data = readRequestData();
        $errors = requireFields($data, ['title', 'content']);

        if ($errors) {
            sendJson(['message' => 'بيانات الإعلان غير مكتملة', 'errors' => $errors], 422);
        }

        $statement = db()->prepare(
            'INSERT INTO announcements (title, content, published_at, is_important, created_at, updated_at)
             VALUES (:title, :content, :published_at, :is_important, NOW(), NOW())'
        );
        $statement->execute([
            'title' => trim((string) $data['title']),
            'content' => trim((string) $data['content']),
            'published_at' => jsonInputValue($data, 'published_at', date('Y-m-d H:i:s')),
            'is_important' => !empty($data['is_important']) ? 1 : 0,
        ]);

        sendJson(['message' => 'تم إنشاء الإعلان', 'data' => ['id' => (int) db()->lastInsertId()]], 201);
    }

    if (($method === 'PUT' || $method === 'POST') && preg_match('#^/admin/announcements/([0-9]+)$#', $path, $matches) === 1) {
        requireAdmin();
        $announcementId = (int) $matches[1];
        $data = readRequestData();
        $errors = requireFields($data, ['title', 'content']);

        if ($errors) {
            sendJson(['message' => 'بيانات الإعلان غير مكتملة', 'errors' => $errors], 422);
        }

        $statement = db()->prepare(
            'UPDATE announcements
             SET title = :title, content = :content, published_at = :published_at, is_important = :is_important, updated_at = NOW()
             WHERE id = :id'
        );
        $statement->execute([
            'id' => $announcementId,
            'title' => trim((string) $data['title']),
            'content' => trim((string) $data['content']),
            'published_at' => jsonInputValue($data, 'published_at', date('Y-m-d H:i:s')),
            'is_important' => !empty($data['is_important']) ? 1 : 0,
        ]);

        sendJson(['message' => 'تم تحديث الإعلان']);
    }

    if ($method === 'DELETE' && preg_match('#^/admin/announcements/([0-9]+)$#', $path, $matches) === 1) {
        requireAdmin();
        $statement = db()->prepare('DELETE FROM announcements WHERE id = :id');
        $statement->execute(['id' => (int) $matches[1]]);
        sendJson(['message' => 'تم حذف الإعلان']);
    }

    if ($method === 'GET' && $path === '/galleries') {
        $statement = db()->query(
            'SELECT g.id, g.title, g.image_path AS image, g.report_excerpt, g.captured_at, a.title AS activity_title, a.slug AS activity_slug
             FROM gallery_items g
             LEFT JOIN activities a ON a.id = g.activity_id
             ORDER BY g.captured_at DESC'
        );

        $items = array_map(function (array $row): array {
            return [
                'id' => (int) $row['id'],
                'title' => $row['title'],
                'image' => $row['image'],
                'reportExcerpt' => $row['report_excerpt'],
                'capturedAt' => $row['captured_at'],
                'activity' => $row['activity_slug'] ? ['title' => $row['activity_title'], 'slug' => $row['activity_slug']] : null,
            ];
        }, $statement->fetchAll());

        sendJson(['data' => $items]);
    }

    if ($method === 'POST' && $path === '/admin/galleries') {
        requireAdmin();
        $data = readRequestData();
        $errors = requireFields($data, ['title', 'image_path']);

        if ($errors) {
            sendJson(['message' => 'بيانات المعرض غير مكتملة', 'errors' => $errors], 422);
        }

        $statement = db()->prepare(
            'INSERT INTO gallery_items (activity_id, title, image_path, report_excerpt, captured_at, created_at, updated_at)
             VALUES (:activity_id, :title, :image_path, :report_excerpt, :captured_at, NOW(), NOW())'
        );
        $statement->execute([
            'activity_id' => jsonInputValue($data, 'activity_id'),
            'title' => trim((string) $data['title']),
            'image_path' => trim((string) $data['image_path']),
            'report_excerpt' => jsonInputValue($data, 'report_excerpt', null),
            'captured_at' => jsonInputValue($data, 'captured_at', date('Y-m-d H:i:s')),
        ]);

        sendJson(['message' => 'تمت إضافة عنصر المعرض', 'data' => ['id' => (int) db()->lastInsertId()]], 201);
    }

    if ($method === 'GET' && $path === '/notifications') {
        $statement = db()->query('SELECT id, title, body, type, is_read, created_at FROM notifications ORDER BY created_at DESC');
        sendJson(['data' => $statement->fetchAll()]);
    }

    if (($method === 'PUT' || $method === 'POST') && preg_match('#^/notifications/([0-9]+)/read$#', $path, $matches) === 1) {
        $statement = db()->prepare('UPDATE notifications SET is_read = 1, updated_at = NOW() WHERE id = :id');
        $statement->execute(['id' => (int) $matches[1]]);
        sendJson(['message' => 'تم تحديث الإشعار']);
    }

    if ($method === 'GET' && $path === '/reports') {
        requireAdmin();
        $statement = db()->query(
            'SELECT ar.id, ar.title, ar.summary, ar.participants_count, ar.report_date, a.title AS activity_title, a.slug AS activity_slug
             FROM activity_reports ar
             LEFT JOIN activities a ON a.id = ar.activity_id
             ORDER BY ar.report_date DESC'
        );
        sendJson(['data' => $statement->fetchAll()]);
    }

    if ($method === 'POST' && $path === '/admin/reports') {
        requireAdmin();
        $data = readRequestData();
        $errors = requireFields($data, ['activity_id', 'title', 'summary']);

        if ($errors) {
            sendJson(['message' => 'بيانات التقرير غير مكتملة', 'errors' => $errors], 422);
        }

        $statement = db()->prepare(
            'INSERT INTO activity_reports (activity_id, title, summary, participants_count, report_date, created_at, updated_at)
             VALUES (:activity_id, :title, :summary, :participants_count, :report_date, NOW(), NOW())'
        );
        $statement->execute([
            'activity_id' => (int) $data['activity_id'],
            'title' => trim((string) $data['title']),
            'summary' => trim((string) $data['summary']),
            'participants_count' => (int) jsonInputValue($data, 'participants_count', 0),
            'report_date' => jsonInputValue($data, 'report_date', date('Y-m-d')),
        ]);

        sendJson(['message' => 'تم حفظ التقرير', 'data' => ['id' => (int) db()->lastInsertId()]], 201);
    }

    if ($method === 'GET' && $path === '/admin/activities') {
        requireAdmin();
        $statement = db()->query(
            'SELECT id, title, slug, short_description, full_description, image, icon, color, category, location, starts_at, ends_at, max_participants, is_featured, is_active
             FROM activities
             ORDER BY created_at DESC'
        );
        sendJson(['data' => $statement->fetchAll()]);
    }

    if ($method === 'POST' && $path === '/admin/activities') {
        requireAdmin();
        $data = readRequestData();
        $errors = requireFields($data, ['title', 'slug', 'short_description', 'full_description', 'image']);

        if ($errors) {
            sendJson(['message' => 'بيانات النشاط غير مكتملة', 'errors' => $errors], 422);
        }

        $statement = db()->prepare(
            'INSERT INTO activities
             (title, slug, short_description, full_description, image, icon, color, category, location, starts_at, ends_at, max_participants, is_featured, is_active, created_at, updated_at)
             VALUES
             (:title, :slug, :short_description, :full_description, :image, :icon, :color, :category, :location, :starts_at, :ends_at, :max_participants, :is_featured, :is_active, NOW(), NOW())'
        );
        $statement->execute([
            'title' => trim((string) $data['title']),
            'slug' => trim((string) $data['slug']),
            'short_description' => trim((string) $data['short_description']),
            'full_description' => trim((string) $data['full_description']),
            'image' => trim((string) $data['image']),
            'icon' => jsonInputValue($data, 'icon', null),
            'color' => jsonInputValue($data, 'color', null),
            'category' => jsonInputValue($data, 'category', null),
            'location' => jsonInputValue($data, 'location', null),
            'starts_at' => jsonInputValue($data, 'starts_at', null),
            'ends_at' => jsonInputValue($data, 'ends_at', null),
            'max_participants' => (int) jsonInputValue($data, 'max_participants', 0),
            'is_featured' => !empty($data['is_featured']) ? 1 : 0,
            'is_active' => array_key_exists('is_active', $data) ? (!empty($data['is_active']) ? 1 : 0) : 1,
        ]);

        sendJson(['message' => 'تم إنشاء النشاط', 'data' => ['id' => (int) db()->lastInsertId()]], 201);
    }

    if (($method === 'PUT' || $method === 'POST') && preg_match('#^/admin/activities/([0-9]+)$#', $path, $matches) === 1) {
        requireAdmin();
        $activityId = (int) $matches[1];
        $data = readRequestData();
        $errors = requireFields($data, ['title', 'slug', 'short_description', 'full_description', 'image']);

        if ($errors) {
            sendJson(['message' => 'بيانات النشاط غير مكتملة', 'errors' => $errors], 422);
        }

        $statement = db()->prepare(
            'UPDATE activities
             SET title = :title, slug = :slug, short_description = :short_description, full_description = :full_description,
                 image = :image, icon = :icon, color = :color, category = :category, location = :location,
                 starts_at = :starts_at, ends_at = :ends_at, max_participants = :max_participants,
                 is_featured = :is_featured, is_active = :is_active, updated_at = NOW()
             WHERE id = :id'
        );
        $statement->execute([
            'id' => $activityId,
            'title' => trim((string) $data['title']),
            'slug' => trim((string) $data['slug']),
            'short_description' => trim((string) $data['short_description']),
            'full_description' => trim((string) $data['full_description']),
            'image' => trim((string) $data['image']),
            'icon' => jsonInputValue($data, 'icon', null),
            'color' => jsonInputValue($data, 'color', null),
            'category' => jsonInputValue($data, 'category', null),
            'location' => jsonInputValue($data, 'location', null),
            'starts_at' => jsonInputValue($data, 'starts_at', null),
            'ends_at' => jsonInputValue($data, 'ends_at', null),
            'max_participants' => (int) jsonInputValue($data, 'max_participants', 0),
            'is_featured' => !empty($data['is_featured']) ? 1 : 0,
            'is_active' => array_key_exists('is_active', $data) ? (!empty($data['is_active']) ? 1 : 0) : 1,
        ]);

        sendJson(['message' => 'تم تحديث النشاط']);
    }

    if ($method === 'DELETE' && preg_match('#^/admin/activities/([0-9]+)$#', $path, $matches) === 1) {
        requireAdmin();
        $statement = db()->prepare('DELETE FROM activities WHERE id = :id');
        $statement->execute(['id' => (int) $matches[1]]);
        sendJson(['message' => 'تم حذف النشاط']);
    }

    if ($method === 'GET' && $path === '/dashboard/summary') {
        $summary = [
            'activitiesCount' => (int) db()->query('SELECT COUNT(*) FROM activities')->fetchColumn(),
            'activeActivitiesCount' => (int) db()->query('SELECT COUNT(*) FROM activities WHERE is_active = 1')->fetchColumn(),
            'applicationsCount' => (int) db()->query('SELECT COUNT(*) FROM volunteer_applications')->fetchColumn(),
            'pendingApplicationsCount' => (int) db()->query("SELECT COUNT(*) FROM volunteer_applications WHERE status = 'pending'")->fetchColumn(),
            'announcementsCount' => (int) db()->query('SELECT COUNT(*) FROM announcements')->fetchColumn(),
            'reportsCount' => (int) db()->query('SELECT COUNT(*) FROM activity_reports')->fetchColumn(),
            'unreadNotificationsCount' => (int) db()->query('SELECT COUNT(*) FROM notifications WHERE is_read = 0')->fetchColumn(),
        ];

        sendJson(['data' => $summary]);
    }

    sendJson(['message' => 'المسار غير موجود'], 404);
} catch (PDOException $exception) {
    sendJson(['message' => 'فشل الاتصال بقاعدة البيانات', 'error' => $exception->getMessage()], 500);
} catch (Throwable $exception) {
    sendJson(['message' => 'حدث خطأ غير متوقع', 'error' => $exception->getMessage()], 500);
}
