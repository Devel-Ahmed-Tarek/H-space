# 🚀 Project Management System (Laravel API)

## 🎉 Overview

A **complete Laravel API** for managing projects and tasks with advanced features such as roles, permissions, notifications, file uploads, and statistics.

---

## 📋 Features

* ✅ **Project Management** – Create, update, delete, approve projects
* ✅ **Task Management** – Create, assign, update, delete tasks
* ✅ **User System** – Register, login, logout, profile
* ✅ **Role & Permissions** – Roles: Admin, Project Manager, Developer, Designer, Tester
* ✅ **File Uploads** – Upload and delete task attachments
* ✅ **Notifications** – Fully integrated notification system
* ✅ **Statistics** – Detailed reports for projects and tasks
* ✅ **Unified API Responses** – Powered by `HelperFunc`

---

## ⚙️ Installation & Setup

### 1. Requirements

```bash
PHP >= 8.1
Composer
MySQL/PostgreSQL
Node.js & NPM
```

### 2. Install Project

```bash
git clone <repository-url>
cd task-octpber-
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### 3. Configure Database

Update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_management
DB_USERNAME=root
DB_PASSWORD=root
```

Run migrations and seeders:

```bash
php artisan migrate
php artisan db:seed --class=SimpleDataSeeder
php artisan storage:link
```

### 4. Run Project

```bash
php artisan serve   # Backend
npm run dev         # Frontend
```

---

## 🔐 Default Accounts

**Password for all users:** `password123`

👨‍💼 **Admins**

* [ahmed@company.com](mailto:ahmed@company.com)
* [fatima@company.com](mailto:fatima@company.com)

👨‍💻 **Project Managers**

* [mohamed@company.com](mailto:mohamed@company.com)
* [sara@company.com](mailto:sara@company.com)

👨‍💻 **Developers**

* [amira@company.com](mailto:amira@company.com)
* [karim@company.com](mailto:karim@company.com)

🎨 **Designer**

* [laila@company.com](mailto:laila@company.com)

🧪 **Tester**

* [rania@company.com](mailto:rania@company.com)

---

## 📚 API Endpoints

### 🔐 Authentication

```
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout
GET  /api/auth/profile
```

### 📋 Projects

```
GET    /api/projects
POST   /api/projects
GET    /api/projects/{id}
PUT    /api/projects/{id}
DELETE /api/projects/{id}
POST   /api/projects/{id}/approve
```

### ✅ Tasks

```
GET    /api/tasks
POST   /api/tasks
GET    /api/tasks/{id}
PUT    /api/tasks/{id}
DELETE /api/tasks/{id}
POST   /api/tasks/{id}/attachments
GET    /api/tasks/{id}/attachments/{attachment}/download
```

### 📊 Statistics

```
GET /api/stats
GET /api/stats/user
```

### 🔔 Notifications

```
GET    /api/notifications
GET    /api/notifications/unread-count
PATCH  /api/notifications/{id}/read
PATCH  /api/notifications/mark-all-read
DELETE /api/notifications/{id}
```

---

## 🧪 Running Tests

```bash
php artisan test
```

Unit Tests:

```bash
php artisan test tests/Unit/HelperFuncTest.php
```

Feature Tests:

```bash
php artisan test tests/Feature/SimpleApiTest.php
```

Expected:

```
Tests:    27 passed (81 assertions)
Duration: 1.41s
```

---

## 📝 Example Usage

### 1. Login

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"ahmed@company.com","password":"password123"}'
```

### 2. Get Projects

```bash
curl -X GET http://localhost:8000/api/projects \
  -H "Authorization: Bearer {token}"
```

### 3. Create Project

```bash
curl -X POST http://localhost:8000/api/projects \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"name":"New Project","description":"Project description","project_manager_id":3,"start_date":"2024-01-01","end_date":"2024-12-31"}'
```

### 4. Create Task

```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"title":"New Task","description":"Task description","project_id":1,"assigned_user_id":5,"priority":"High","due_date":"2024-06-30"}'
```

### 5. Upload Attachment

```bash
curl -X POST http://localhost:8000/api/tasks/1/attachments \
  -H "Authorization: Bearer {token}" \
  -F "file=@document.pdf"
```

---

## 🏗️ Project Structure

```
task-octpber-/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── AuthController.php
│   │   │   ├── ProjectController.php
│   │   │   ├── TaskController.php
│   │   │   ├── StatsController.php
│   │   │   └── NotificationController.php
│   │   ├── Requests/
│   │   │   ├── Auth/
│   │   │   ├── Project/
│   │   │   └── Task/
│   │   └── Helpers/
│   │       └── HelperFunc.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Project.php
│   │   └── Task.php
│   └── Notifications/
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
├── tests/
│   ├── Unit/
│   └── Feature/
└── README.md
```

---

## 🔧 HelperFunc Methods

```php
HelperFunc::sendResponse($status, $message, $data = null)
HelperFunc::paginateResponse($paginator, $message)
HelperFunc::uploadFile($path, $file)
HelperFunc::deleteFile($filePath)
HelperFunc::getImageUrl($path)
HelperFunc::getYouTubeThumbnail($videoId)
HelperFunc::formatDuration($seconds)
HelperFunc::parseDurationToSeconds($duration)
HelperFunc::prepareGoogleDriveLink($link)
HelperFunc::getPaginationParams($request)
HelperFunc::limit($value, $limit)
```

---

## 📊 Data Overview

* **Projects**: 4 (open, in-progress, completed, approved/unapproved)
* **Tasks**: 10 (different priorities & statuses)
* **Users**: 18 with different roles

---

## 🛠️ Troubleshooting

* **DB Error**

```bash
php artisan migrate:fresh --seed
```

* **Permissions Error**

```bash
php artisan db:seed --class=SimpleDataSeeder
```

* **Storage Error**

```bash
php artisan storage:link
```

---

## 📈 Performance Optimization

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

---

## 🤝 Contribution Guidelines

1. Follow **PSR-12** standards
2. Write **tests** for every new feature
3. Always use `HelperFunc` for API responses
4. Comments in **English**, user messages in **Arabic**
5. Create feature branches before commits

---

## 📞 Support

* 📧 Email: [support@company.com](mailto:support@company.com)
* 📱 Phone: +1234567890
* 💬 Slack: `#project-management`

---

## 🎯 Conclusion

The **Project Management System** is **100% production-ready** with:

* ✅ Full API
* ✅ Role-based access
* ✅ Secure file uploads
* ✅ Comprehensive statistics
* ✅ Complete test coverage
* ✅ Optimized performance
* ✅ Well-documented

**Ready for deployment!** 🚀
