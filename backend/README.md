# PHP Backend

هذا الباك إند مكتوب بـ PHP خام و MySQL، ويغطي:

- تسجيل دخول الإدارة
- إدارة الأنشطة
- استقبال ومراجعة طلبات الانضمام
- إدارة الإعلانات
- معرض صور وتقارير
- إشعارات
- ملخص Dashboard

## اسم قاعدة البيانات

`student_initiatives`

## الملفات المهمة

- `backend/api.php`
- `backend/database.php`
- `backend/config.php`
- `backend/database.sql`
- `backend/.env.example`
- `backend/admin/login.php`
- `backend/admin/dashboard.php`
- `backend/admin/activities.php`
- `backend/admin/applications.php`
- `backend/admin/announcements.php`
- `backend/admin/reports.php`
- `backend/admin/notifications.php`
- `backend/admin/gallery.php`

## التشغيل

1. أنشئ قاعدة بيانات MySQL أو فقط نفذ ملف `database.sql` لأنه ينشئ القاعدة والجداول.
2. انسخ `backend/.env.example` إلى `backend/.env`.
3. عدل بيانات الاتصال داخل `.env`.
4. شغل السيرفر من داخل مجلد `backend`:

```bash
php -S 127.0.0.1:8000
```

## لوحة الإدارة

الرابط:

```text
http://127.0.0.1:8000/admin/login.php
```

الحساب الافتراضي:

- البريد: `admin@initiative.com`
- كلمة المرور: `admin123`

## أهم المسارات

- `POST /api.php/auth/login`
- `POST /api.php/auth/logout`
- `GET /api.php/auth/me`
- `GET /api.php/activities`
- `GET /api.php/activities/{slug}`
- `POST /api.php/applications`
- `GET /api.php/announcements`
- `GET /api.php/galleries`
- `GET /api.php/notifications`
- `POST /api.php/notifications/{id}/read`
- `GET /api.php/dashboard/summary`
- `GET /api.php/admin/activities`
- `POST /api.php/admin/activities`
- `POST|PUT /api.php/admin/activities/{id}`
- `DELETE /api.php/admin/activities/{id}`
- `GET /api.php/admin/applications`
- `POST|PUT /api.php/admin/applications/{id}`
- `POST /api.php/admin/announcements`
- `POST|PUT /api.php/admin/announcements/{id}`
- `DELETE /api.php/admin/announcements/{id}`
- `POST /api.php/admin/galleries`
- `GET /api.php/reports`
- `POST /api.php/admin/reports`

## الصفحات الإدارية

- `admin/index.php`
- `admin/dashboard.php`
- `admin/activities.php`
- `admin/applications.php`
- `admin/announcements.php`
- `admin/reports.php`
- `admin/notifications.php`
- `admin/gallery.php`
