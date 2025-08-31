# Database Schema Documentation

## 🗄️ **هيكل قاعدة البيانات - Project Management System**

---

## 📊 **Database Overview**

### **Database Name:** `taskoctoper`

### **Engine:** MySQL 8.0+

### **Character Set:** utf8mb4

### **Collation:** utf8mb4_unicode_ci

---

## 🏗️ **Tables Structure**

### **1. users**

```sql
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
);
```

**Description:** جدول المستخدمين الأساسي

-   **id**: المعرف الفريد للمستخدم
-   **name**: اسم المستخدم
-   **email**: البريد الإلكتروني (فريد)
-   **email_verified_at**: تاريخ التحقق من البريد الإلكتروني
-   **password**: كلمة المرور المشفرة
-   **remember_token**: رمز تذكر تسجيل الدخول

---

### **2. projects**

```sql
CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `project_manager_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('Open','In Progress','Completed') DEFAULT 'Open',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_approved` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projects_project_manager_id_foreign` (`project_manager_id`),
  CONSTRAINT `projects_project_manager_id_foreign` FOREIGN KEY (`project_manager_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
```

**Description:** جدول المشاريع

-   **id**: المعرف الفريد للمشروع
-   **name**: اسم المشروع
-   **description**: وصف المشروع
-   **project_manager_id**: معرف مدير المشروع (مرتبط بـ users)
-   **status**: حالة المشروع (Open, In Progress, Completed)
-   **start_date**: تاريخ بداية المشروع
-   **end_date**: تاريخ نهاية المشروع
-   **is_approved**: حالة الموافقة على المشروع (NULL = معلق، 1 = معتمد، 0 = مرفوض)

---

### **3. tasks**

```sql
CREATE TABLE `tasks` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_user_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('To Do','In Progress','Done') DEFAULT 'To Do',
  `priority` enum('Low','Medium','High','Urgent') DEFAULT 'Medium',
  `due_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_project_id_foreign` (`project_id`),
  KEY `tasks_assigned_user_id_foreign` (`assigned_user_id`),
  CONSTRAINT `tasks_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_assigned_user_id_foreign` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
```

**Description:** جدول المهام

-   **id**: المعرف الفريد للمهمة
-   **title**: عنوان المهمة
-   **description**: وصف المهمة
-   **project_id**: معرف المشروع (مرتبط بـ projects)
-   **assigned_user_id**: معرف المستخدم المكلف (مرتبط بـ users)
-   **status**: حالة المهمة (To Do, In Progress, Done)
-   **priority**: أولوية المهمة (Low, Medium, High, Urgent)
-   **due_date**: تاريخ استحقاق المهمة

---

### **4. project_approvals**

```sql
CREATE TABLE `project_approvals` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `approver_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('approved','rejected') NOT NULL,
  `comments` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_approvals_project_id_foreign` (`project_id`),
  KEY `project_approvals_approver_id_foreign` (`approver_id`),
  CONSTRAINT `project_approvals_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_approvals_approver_id_foreign` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
```

**Description:** جدول موافقات المشاريع

-   **id**: المعرف الفريد للموافقة
-   **project_id**: معرف المشروع (مرتبط بـ projects)
-   **approver_id**: معرف الموافق (مرتبط بـ users)
-   **status**: حالة الموافقة (approved, rejected)
-   **comments**: تعليقات الموافقة

---

### **5. task_attachments**

```sql
CREATE TABLE `task_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` bigint(20) UNSIGNED NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_attachments_task_id_foreign` (`task_id`),
  CONSTRAINT `task_attachments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
);
```

**Description:** جدول مرفقات المهام

-   **id**: المعرف الفريد للمرفق
-   **task_id**: معرف المهمة (مرتبط بـ tasks)
-   **file_name**: اسم الملف
-   **file_path**: مسار الملف في النظام
-   **file_size**: حجم الملف بالبايت
-   **mime_type**: نوع الملف

---

### **6. notifications**

```sql
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
);
```

**Description:** جدول الإشعارات

-   **id**: المعرف الفريد للإشعار (UUID)
-   **type**: نوع الإشعار
-   **notifiable_type**: نوع الكائن المستلم للإشعار
-   **notifiable_id**: معرف الكائن المستلم للإشعار
-   **data**: بيانات الإشعار (JSON)
-   **read_at**: تاريخ قراءة الإشعار

---

### **7. roles**

```sql
CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
);
```

**Description:** جدول الأدوار

-   **id**: المعرف الفريد للدور
-   **name**: اسم الدور
-   **guard_name**: اسم الحارس (web, api)

---

### **8. permissions**

```sql
CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
);
```

**Description:** جدول الصلاحيات

-   **id**: المعرف الفريد للصلاحية
-   **name**: اسم الصلاحية
-   **guard_name**: اسم الحارس

---

### **9. model_has_roles**

```sql
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_type`,`model_id`),
  KEY `model_has_roles_model_type_model_id_index` (`model_type`,`model_id`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
);
```

**Description:** جدول ربط الأدوار بالمستخدمين

-   **role_id**: معرف الدور
-   **model_type**: نوع النموذج (App\Models\User)
-   **model_id**: معرف النموذج

---

### **10. model_has_permissions**

```sql
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_type`,`model_id`),
  KEY `model_has_permissions_model_type_model_id_index` (`model_type`,`model_id`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
);
```

**Description:** جدول ربط الصلاحيات بالمستخدمين

-   **permission_id**: معرف الصلاحية
-   **model_type**: نوع النموذج
-   **model_id**: معرف النموذج

---

### **11. role_has_permissions**

```sql
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
);
```

**Description:** جدول ربط الصلاحيات بالأدوار

-   **permission_id**: معرف الصلاحية
-   **role_id**: معرف الدور

---

### **12. personal_access_tokens**

```sql
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL UNIQUE,
  `abilities` text,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
);
```

**Description:** جدول رموز الوصول الشخصية (Sanctum)

-   **id**: المعرف الفريد للرمز
-   **tokenable_type**: نوع النموذج
-   **tokenable_id**: معرف النموذج
-   **name**: اسم الرمز
-   **token**: الرمز المشفر
-   **abilities**: القدرات الممنوحة
-   **last_used_at**: آخر استخدام
-   **expires_at**: تاريخ انتهاء الصلاحية

---

### **13. cache**

```sql
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
);
```

**Description:** جدول التخزين المؤقت

-   **key**: مفتاح التخزين المؤقت
-   **value**: القيمة المخزنة
-   **expiration**: تاريخ انتهاء الصلاحية

---

### **14. jobs**

```sql
CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
);
```

**Description:** جدول الوظائف الخلفية

-   **id**: المعرف الفريد للوظيفة
-   **queue**: اسم الطابور
-   **payload**: بيانات الوظيفة
-   **attempts**: عدد المحاولات
-   **reserved_at**: وقت الحجز
-   **available_at**: وقت التوفر
-   **created_at**: وقت الإنشاء

---

## 🔗 **Relationships**

### **One-to-Many Relationships:**

-   **User → Projects**: مستخدم واحد يمكن أن يكون مدير لعدة مشاريع
-   **User → Tasks**: مستخدم واحد يمكن أن يكون مكلف بعدة مهام
-   **Project → Tasks**: مشروع واحد يحتوي على عدة مهام
-   **Project → Project Approvals**: مشروع واحد يمكن أن يكون له عدة موافقات
-   **Task → Task Attachments**: مهمة واحدة يمكن أن يكون لها عدة مرفقات

### **Many-to-Many Relationships:**

-   **Users ↔ Roles**: مستخدمون يمكن أن يكون لهم عدة أدوار
-   **Users ↔ Permissions**: مستخدمون يمكن أن يكون لهم عدة صلاحيات
-   **Roles ↔ Permissions**: أدوار يمكن أن يكون لها عدة صلاحيات

---

## 📊 **Indexes**

### **Primary Indexes:**

-   جميع الجداول تحتوي على `id` كـ Primary Key

### **Foreign Key Indexes:**

-   `projects.project_manager_id` → `users.id`
-   `tasks.project_id` → `projects.id`
-   `tasks.assigned_user_id` → `users.id`
-   `project_approvals.project_id` → `projects.id`
-   `project_approvals.approver_id` → `users.id`
-   `task_attachments.task_id` → `tasks.id`

### **Unique Indexes:**

-   `users.email`
-   `roles.name_guard_name`
-   `permissions.name_guard_name`
-   `personal_access_tokens.token`

### **Composite Indexes:**

-   `notifications.notifiable_type_notifiable_id`
-   `model_has_roles.model_type_model_id`
-   `model_has_permissions.model_type_model_id`
-   `personal_access_tokens.tokenable_type_tokenable_id`

---

## 🔒 **Constraints**

### **Foreign Key Constraints:**

جميع العلاقات محمية بـ CASCADE DELETE:

-   عند حذف مستخدم، يتم حذف جميع مشاريعه ومهامه
-   عند حذف مشروع، يتم حذف جميع مهامه وموافقاته
-   عند حذف مهمة، يتم حذف جميع مرفقاتها

### **Check Constraints:**

-   `projects.status`: Open, In Progress, Completed
-   `tasks.status`: To Do, In Progress, Done
-   `tasks.priority`: Low, Medium, High, Urgent
-   `project_approvals.status`: approved, rejected

---

## 📈 **Sample Data**

### **Users Sample:**

```sql
INSERT INTO users (name, email, password, email_verified_at) VALUES
('أحمد محمد', 'ahmed@company.com', '$2y$10$...', NOW()),
('فاطمة علي', 'fatima@company.com', '$2y$10$...', NOW()),
('محمد حسن', 'mohamed@company.com', '$2y$10$...', NOW());
```

### **Projects Sample:**

```sql
INSERT INTO projects (name, description, project_manager_id, status, start_date, end_date, is_approved) VALUES
('تطوير موقع الشركة الإلكتروني', 'تطوير موقع إلكتروني حديث ومتجاوب', 3, 'In Progress', '2024-01-15', '2024-06-30', 1),
('تطبيق الهاتف المحمول للعملاء', 'تطوير تطبيق iOS و Android للعملاء', 4, 'Open', '2024-02-01', '2024-08-15', 1);
```

### **Tasks Sample:**

```sql
INSERT INTO tasks (title, description, project_id, assigned_user_id, status, priority, due_date) VALUES
('تصميم واجهة المستخدم', 'تصميم واجهة المستخدم الرئيسية', 1, 5, 'Done', 'High', '2024-02-15'),
('تطوير الصفحة الرئيسية', 'تطوير الصفحة الرئيسية باستخدام React.js', 1, 6, 'Done', 'High', '2024-03-01');
```

---

## 🚀 **Database Optimization**

### **Recommended Indexes:**

```sql
-- For better performance on status queries
CREATE INDEX idx_projects_status ON projects(status);
CREATE INDEX idx_tasks_status ON tasks(status);
CREATE INDEX idx_tasks_priority ON tasks(priority);
CREATE INDEX idx_tasks_due_date ON tasks(due_date);

-- For better performance on date range queries
CREATE INDEX idx_projects_dates ON projects(start_date, end_date);
CREATE INDEX idx_tasks_project_due ON tasks(project_id, due_date);
```

### **Partitioning Strategy:**

```sql
-- Partition tasks by year for better performance
ALTER TABLE tasks PARTITION BY RANGE (YEAR(due_date)) (
    PARTITION p2023 VALUES LESS THAN (2024),
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

---

## 🔧 **Maintenance**

### **Regular Maintenance Tasks:**

```sql
-- Clean up expired tokens
DELETE FROM personal_access_tokens WHERE expires_at < NOW();

-- Clean up old notifications (older than 1 year)
DELETE FROM notifications WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);

-- Clean up failed jobs (older than 1 day)
DELETE FROM jobs WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 DAY) AND attempts >= 3;
```

### **Backup Strategy:**

```bash
# Daily backup
mysqldump -u root -p taskoctoper > backup_$(date +%Y%m%d).sql

# Weekly backup with compression
mysqldump -u root -p taskoctoper | gzip > backup_$(date +%Y%m%d).sql.gz
```

---

## 📚 **Additional Resources**

-   **Migrations**: `database/migrations/`
-   **Seeders**: `database/seeders/`
-   **Models**: `app/Models/`
-   **Factories**: `database/factories/`

---

**تم تصميم قاعدة البيانات باستخدام أفضل الممارسات وأحدث التقنيات** 🚀
