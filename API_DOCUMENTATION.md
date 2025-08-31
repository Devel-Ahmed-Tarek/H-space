# API Documentation - Project Management System

## 📚 **دليل API شامل لنظام إدارة المشاريع**

### 🔗 **Base URL**

```
http://task-octpber-.test/api
```

---

## 🔐 **Authentication**

### **Register User**

```http
POST /auth/register
```

**Request Body:**

```json
{
    "name": "مستخدم جديد",
    "email": "newuser@company.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "Developer"
}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم التسجيل بنجاح",
    "data": {
        "user": {
            "id": 1,
            "name": "مستخدم جديد",
            "email": "newuser@company.com",
            "roles": [...]
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### **Login User**

```http
POST /auth/login
```

**Request Body:**

```json
{
    "email": "ahmed@company.com",
    "password": "password123"
}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم تسجيل الدخول بنجاح",
    "data": {
        "user": {...},
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### **Get Profile**

```http
GET /auth/profile
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم جلب الملف الشخصي بنجاح",
    "data": {
        "id": 1,
        "name": "أحمد محمد",
        "email": "ahmed@company.com",
        "roles": [...],
        "managed_projects": [...],
        "assigned_tasks": [...]
    }
}
```

### **Logout**

```http
POST /auth/logout
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم تسجيل الخروج بنجاح"
}
```

---

## 📋 **Projects**

### **Get All Projects**

```http
GET /projects
```

**Headers:**

```
Authorization: Bearer {token}
```

**Query Parameters:**

-   `page` (optional): Page number for pagination
-   `per_page` (optional): Items per page (default: 15)

**Response:**

```json
{
    "status": 200,
    "msg": "تم جلب المشاريع بنجاح",
    "data": {
        "data": [
            {
                "id": 1,
                "name": "تطوير موقع الشركة الإلكتروني",
                "description": "تطوير موقع إلكتروني حديث ومتجاوب",
                "project_manager_id": 3,
                "status": "In Progress",
                "start_date": "2024-01-15",
                "end_date": "2024-06-30",
                "is_approved": true,
                "project_manager": {...},
                "tasks": [...]
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 15,
            "total": 4,
            "last_page": 1
        }
    }
}
```

### **Create Project**

```http
POST /projects
```

**Headers:**

```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**

```json
{
    "name": "مشروع جديد",
    "description": "وصف المشروع",
    "project_manager_id": 3,
    "start_date": "2024-01-01",
    "end_date": "2024-12-31"
}
```

**Response:**

```json
{
    "status": 201,
    "msg": "تم إنشاء المشروع بنجاح",
    "data": {
        "id": 5,
        "name": "مشروع جديد",
        "description": "وصف المشروع",
        "project_manager_id": 3,
        "status": "Open",
        "start_date": "2024-01-01",
        "end_date": "2024-12-31",
        "is_approved": null
    }
}
```

### **Get Project by ID**

```http
GET /projects/{id}
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم جلب المشروع بنجاح",
    "data": {
        "id": 1,
        "name": "تطوير موقع الشركة الإلكتروني",
        "description": "تطوير موقع إلكتروني حديث ومتجاوب",
        "project_manager_id": 3,
        "status": "In Progress",
        "start_date": "2024-01-15",
        "end_date": "2024-06-30",
        "is_approved": true,
        "project_manager": {...},
        "tasks": [...],
        "approvals": [...]
    }
}
```

### **Update Project**

```http
PUT /projects/{id}
```

**Headers:**

```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**

```json
{
    "name": "مشروع محدث",
    "description": "وصف محدث للمشروع",
    "project_manager_id": 3,
    "start_date": "2024-01-01",
    "end_date": "2024-12-31"
}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم تحديث المشروع بنجاح",
    "data": {...}
}
```

### **Delete Project**

```http
DELETE /projects/{id}
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم حذف المشروع بنجاح"
}
```

### **Approve/Reject Project**

```http
POST /projects/{id}/approve
```

**Headers:**

```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**

```json
{
    "status": "approved",
    "comments": "مشروع ممتاز، تمت الموافقة"
}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم اعتماد المشروع بنجاح",
    "data": {
        "project": {...},
        "approval": {...}
    }
}
```

---

## ✅ **Tasks**

### **Get All Tasks**

```http
GET /tasks
```

**Headers:**

```
Authorization: Bearer {token}
```

**Query Parameters:**

-   `page` (optional): Page number
-   `per_page` (optional): Items per page
-   `status` (optional): Filter by status (To Do, In Progress, Done)
-   `priority` (optional): Filter by priority (Low, Medium, High, Urgent)
-   `project_id` (optional): Filter by project ID

**Response:**

```json
{
    "status": 200,
    "msg": "تم جلب المهام بنجاح",
    "data": {
        "data": [
            {
                "id": 1,
                "title": "تصميم واجهة المستخدم",
                "description": "تصميم واجهة المستخدم الرئيسية",
                "project_id": 1,
                "assigned_user_id": 5,
                "status": "Done",
                "priority": "High",
                "due_date": "2024-02-15",
                "project": {...},
                "assigned_user": {...},
                "attachments": [...]
            }
        ],
        "pagination": {...}
    }
}
```

### **Create Task**

```http
POST /tasks
```

**Headers:**

```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**

```json
{
    "title": "مهمة جديدة",
    "description": "وصف المهمة",
    "project_id": 1,
    "assigned_user_id": 5,
    "priority": "High",
    "due_date": "2024-06-30"
}
```

**Response:**

```json
{
    "status": 201,
    "msg": "تم إنشاء المهمة بنجاح",
    "data": {
        "id": 11,
        "title": "مهمة جديدة",
        "description": "وصف المهمة",
        "project_id": 1,
        "assigned_user_id": 5,
        "status": "To Do",
        "priority": "High",
        "due_date": "2024-06-30"
    }
}
```

### **Get Task by ID**

```http
GET /tasks/{id}
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم جلب المهمة بنجاح",
    "data": {
        "id": 1,
        "title": "تصميم واجهة المستخدم",
        "description": "تصميم واجهة المستخدم الرئيسية",
        "project_id": 1,
        "assigned_user_id": 5,
        "status": "Done",
        "priority": "High",
        "due_date": "2024-02-15",
        "project": {...},
        "assigned_user": {...},
        "attachments": [...]
    }
}
```

### **Update Task**

```http
PUT /tasks/{id}
```

**Headers:**

```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**

```json
{
    "title": "مهمة محدثة",
    "description": "وصف محدث للمهمة",
    "project_id": 1,
    "assigned_user_id": 5,
    "priority": "Medium",
    "status": "In Progress",
    "due_date": "2024-07-15"
}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم تحديث المهمة بنجاح",
    "data": {...}
}
```

### **Delete Task**

```http
DELETE /tasks/{id}
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم حذف المهمة بنجاح"
}
```

### **Upload Attachment**

```http
POST /tasks/{id}/attachments
```

**Headers:**

```
Authorization: Bearer {token}
```

**Request Body:**

```
Content-Type: multipart/form-data
file: [file upload]
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم رفع المرفق بنجاح",
    "data": {
        "id": 1,
        "task_id": 1,
        "file_name": "document.pdf",
        "file_path": "task-attachments/document.pdf",
        "file_size": 1024000
    }
}
```

### **Download Attachment**

```http
GET /tasks/{task_id}/attachments/{attachment_id}/download
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```
File download
```

---

## 📊 **Statistics**

### **Get General Statistics**

```http
GET /stats
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم جلب الإحصائيات بنجاح",
    "data": {
        "projects_by_status": {
            "Open": 2,
            "In Progress": 1,
            "Completed": 1
        },
        "completed_tasks_per_user": [
            {
                "name": "أميرة محمد",
                "completed_tasks": 2
            }
        ],
        "most_active_users": [
            {
                "name": "كريم أحمد",
                "total_tasks": 3,
                "completed_tasks": 1
            }
        ],
        "overdue_tasks_count": 6,
        "tasks_by_priority": {
            "High": 9,
            "Medium": 1
        },
        "projects_approval_status": {
            "approved": 3,
            "pending": 1
        },
        "summary": {
            "total_projects": 4,
            "total_tasks": 10,
            "total_users": 18
        }
    }
}
```

### **Get User Statistics**

```http
GET /stats/user
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم جلب إحصائيات المستخدم بنجاح",
    "data": {
        "user_id": 1,
        "total_tasks": 5,
        "completed_tasks": 3,
        "pending_tasks": 2,
        "overdue_tasks": 1,
        "managed_projects": 2,
        "project_completion_rate": 75.5
    }
}
```

---

## 🔔 **Notifications**

### **Get All Notifications**

```http
GET /notifications
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم جلب الإشعارات بنجاح",
    "data": {
        "data": [
            {
                "id": "uuid",
                "type": "App\\Notifications\\TaskNotification",
                "data": {
                    "message": "تم تكليفك بمهمة جديدة",
                    "task_id": 1,
                    "task_title": "تصميم واجهة المستخدم",
                    "project_name": "تطوير موقع الشركة"
                },
                "read_at": null,
                "created_at": "2024-01-01T00:00:00.000000Z"
            }
        ],
        "pagination": {...}
    }
}
```

### **Get Unread Count**

```http
GET /notifications/unread-count
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم جلب عدد الإشعارات غير المقروءة بنجاح",
    "data": {
        "unread_count": 5
    }
}
```

### **Mark as Read**

```http
PATCH /notifications/{id}/read
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم تحديد الإشعار كمقروء بنجاح"
}
```

### **Mark All as Read**

```http
PATCH /notifications/mark-all-read
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم تحديد جميع الإشعارات كمقروءة بنجاح"
}
```

### **Delete Notification**

```http
DELETE /notifications/{id}
```

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "status": 200,
    "msg": "تم حذف الإشعار بنجاح"
}
```

---

## 🔧 **Helper Functions**

### **Response Format**

جميع الاستجابات تتبع نفس التنسيق:

```json
{
    "status": 200,
    "msg": "رسالة النجاح",
    "data": {...}
}
```

### **Error Response Format**

```json
{
    "status": 422,
    "msg": "فشل في التحقق من البيانات",
    "data": {
        "field_name": ["رسالة الخطأ"]
    }
}
```

### **Pagination Format**

```json
{
    "data": [...],
    "pagination": {
        "current_page": 1,
        "per_page": 15,
        "total": 100,
        "last_page": 7,
        "from": 1,
        "to": 15,
        "has_more_pages": true,
        "links": {
            "first": "http://example.com/api/endpoint?page=1",
            "last": "http://example.com/api/endpoint?page=7",
            "prev": null,
            "next": "http://example.com/api/endpoint?page=2"
        }
    }
}
```

---

## 🛡️ **Authentication & Authorization**

### **Required Headers**

```
Authorization: Bearer {token}
Content-Type: application/json
```

### **User Roles**

-   **Admin**: جميع الصلاحيات
-   **Project Manager**: إدارة المشاريع الخاصة به
-   **Developer**: عرض وتحديث المهام المخصصة له
-   **Designer**: عرض وتحديث المهام المخصصة له
-   **QA Tester**: عرض وتحديث المهام المخصصة له

### **Permission Matrix**

| Resource       | Admin | Project Manager   | Developer     | Designer      | QA Tester     |
| -------------- | ----- | ----------------- | ------------- | ------------- | ------------- |
| Create Project | ✅    | ✅                | ❌            | ❌            | ❌            |
| Update Project | ✅    | ✅ (own)          | ❌            | ❌            | ❌            |
| Delete Project | ✅    | ✅ (own)          | ❌            | ❌            | ❌            |
| Create Task    | ✅    | ✅ (own projects) | ❌            | ❌            | ❌            |
| Update Task    | ✅    | ✅ (own projects) | ✅ (assigned) | ✅ (assigned) | ✅ (assigned) |
| Delete Task    | ✅    | ✅ (own projects) | ❌            | ❌            | ❌            |
| View Stats     | ✅    | ✅ (own projects) | ❌            | ❌            | ❌            |

---

## 📝 **Error Codes**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 201  | Created               |
| 400  | Bad Request           |
| 401  | Unauthorized          |
| 403  | Forbidden             |
| 404  | Not Found             |
| 422  | Validation Error      |
| 500  | Internal Server Error |

---

## 🚀 **Getting Started**

### **1. Install Dependencies**

```bash
composer install
```

### **2. Setup Environment**

```bash
cp .env.example .env
php artisan key:generate
```

### **3. Configure Database**

```bash
php artisan migrate
php artisan db:seed --class=SimpleDataSeeder
```

### **4. Start Server**

```bash
php artisan serve
```

### **5. Test API**

```bash
php test_api.php
```

---

## 📚 **Additional Resources**

-   **README.md**: الدليل الشامل للمشروع
-   **QUICK_START.md**: دليل البدء السريع
-   **Project_Management_API.postman_collection.json**: Postman Collection
-   **test_api.php**: ملف اختبار الـ API

---

## 🤝 **Support**

للمساعدة والدعم:

-   📧 Email: support@company.com
-   📱 Phone: +1234567890
-   💬 Chat: Slack #project-management

---

**تم تطوير هذا الـ API باستخدام Laravel 10+ مع أفضل الممارسات وأحدث التقنيات** 🚀
