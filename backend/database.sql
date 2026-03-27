CREATE DATABASE IF NOT EXISTS student_initiatives CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_initiatives;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    phone VARCHAR(30) DEFAULT NULL,
    college VARCHAR(150) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS activities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    short_description VARCHAR(500) NOT NULL,
    full_description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    icon VARCHAR(20) DEFAULT NULL,
    color VARCHAR(20) DEFAULT NULL,
    category VARCHAR(100) DEFAULT NULL,
    location VARCHAR(255) DEFAULT NULL,
    starts_at DATETIME DEFAULT NULL,
    ends_at DATETIME DEFAULT NULL,
    max_participants INT UNSIGNED NOT NULL DEFAULT 0,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by INT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_activities_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS volunteer_applications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    activity_id INT UNSIGNED NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    college VARCHAR(150) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    message TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_volunteer_activity FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS announcements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    published_at DATETIME DEFAULT NULL,
    is_important TINYINT(1) NOT NULL DEFAULT 0,
    created_by INT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_announcements_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS gallery_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    activity_id INT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    report_excerpt TEXT DEFAULT NULL,
    captured_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_gallery_activity FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    type VARCHAR(100) DEFAULT 'general',
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS activity_reports (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    activity_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    summary TEXT NOT NULL,
    participants_count INT UNSIGNED NOT NULL DEFAULT 0,
    report_date DATE NOT NULL,
    created_by INT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reports_activity FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
    CONSTRAINT fk_reports_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO users (full_name, email, password_hash, role, phone, college, is_active)
VALUES
('Admin User', 'admin@initiative.com', 'admin123', 'admin', '01000000000', 'إدارة النظام', 1)
ON DUPLICATE KEY UPDATE email = VALUES(email);

INSERT INTO activities (title, slug, short_description, full_description, image, icon, color, category, location, starts_at, ends_at, max_participants, is_featured, is_active, created_by)
VALUES
('الأنشطة التعليمية', 'educational', 'ورش ودورات تساعد الطلاب على تطوير مهاراتهم الأكاديمية والمهنية.', 'نقدم ورش عمل وبرامج تدريبية لرفع جاهزية الطلاب لسوق العمل وتطوير قدراتهم التقنية والشخصية داخل المبادرة.', 'images/page2/close-up-people-planning-trip 1.png', '📘', '#243b6b', 'تعليمي', 'قاعة الأنشطة', '2026-04-03 10:00:00', '2026-04-03 13:00:00', 50, 1, 1, 1),
('الأنشطة الثقافية', 'cultural', 'مساحات تدعم الإبداع والهوية الثقافية للطلاب.', 'تنظم المبادرة فعاليات ثقافية ومسابقات أدبية وأمسيات فنية تعزز التعبير والإبداع في بيئة جامعية داعمة.', 'images/page2/pexels-spacex-586104 1.png', '🎨', '#b57a5c', 'ثقافي', 'مسرح الكلية', '2026-04-06 11:00:00', '2026-04-06 13:00:00', 40, 1, 1, 1),
('الأنشطة الرياضية', 'sports', 'بطولات وفعاليات رياضية لتعزيز روح الفريق والصحة.', 'نوفر بطولات ومنافسات رياضية متنوعة بين الطلاب بهدف تعزيز روح الفريق والانضباط وتشجيع النشاط البدني.', 'images/page2/men-play-socer-park-tournament-mini-footbal-guy-black-sportsuits 1.png', '⚽', '#27ae60', 'رياضي', 'الملعب الجامعي', '2026-04-09 09:00:00', '2026-04-09 13:00:00', 60, 1, 1, 1),
('الأنشطة التطوعية', 'volunteer', 'مبادرات تطوعية لخدمة المجتمع وتنمية حس المسؤولية.', 'نهتم بتنظيم حملات مجتمعية وزيارات إنسانية وأنشطة خدمية تتيح للطلاب إحداث أثر حقيقي داخل المجتمع.', 'images/page2/istockphoto-1427848338-612x612 1.png', '🤝', '#f2b705', 'تطوعي', 'خارج الحرم الجامعي', '2026-04-12 08:00:00', '2026-04-12 13:00:00', 80, 1, 1, 1)
ON DUPLICATE KEY UPDATE slug = VALUES(slug);

INSERT INTO announcements (title, content, published_at, is_important, created_by)
VALUES
('فتح باب التسجيل في الأنشطة التعليمية', 'بدأ التسجيل في ورش البرمجة والتأهيل لسوق العمل، والأولوية للطلاب الجدد.', '2026-03-26 09:00:00', 1, 1),
('لقاء تعريفي للمبادرة الأسبوع القادم', 'سيتم عقد لقاء تعريفي لشرح آلية الانضمام والأنشطة المتاحة خلال الفصل الدراسي.', '2026-03-27 12:00:00', 0, 1);

INSERT INTO gallery_items (activity_id, title, image_path, report_excerpt, captured_at)
SELECT id, CONCAT(title, ' - تقرير مصور'), image, 'ملخص سريع للنشاط، عدد المشاركين، والأثر الذي تحقق بعد التنفيذ.', '2026-03-25 10:00:00'
FROM activities
WHERE NOT EXISTS (SELECT 1 FROM gallery_items);

INSERT INTO activity_reports (activity_id, title, summary, participants_count, report_date, created_by)
SELECT id, CONCAT('تقرير ', title), 'تم تنفيذ النشاط بنجاح مع تفاعل جيد من الطلاب وتحقيق الأهداف الأساسية للمبادرة.', 25, '2026-03-26', 1
FROM activities
WHERE NOT EXISTS (SELECT 1 FROM activity_reports);

INSERT INTO notifications (user_id, title, body, type, is_read)
VALUES
(NULL, 'مرحبًا بك في لوحة الإدارة', 'يمكنك الآن متابعة الطلبات والأنشطة والإعلانات من الباك إند.', 'system', 0),
(NULL, 'تنبيه جديد', 'يوجد طلبات انضمام تحتاج إلى مراجعة من الإدارة.', 'application', 0);
