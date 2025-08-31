# نظام إدارة المشاريع - Project Management System

## 🎉 **مشروع Laravel API متكامل لإدارة المشاريع والمهام**

### 📋 **المميزات الرئيسية:**

-   ✅ **إدارة المشاريع** - إنشاء، تحديث، حذف، اعتماد المشاريع
-   ✅ **إدارة المهام** - إنشاء، تعيين، تحديث، حذف المهام
-   ✅ **نظام المستخدمين** - تسجيل، دخول، خروج، ملف شخصي
-   ✅ **نظام الصلاحيات** - أدوار مختلفة (مدير، مدير مشروع، مطور، مصمم، مختبر)
-   ✅ **رفع الملفات** - رفع وحذف مرفقات المهام
-   ✅ **الإشعارات** - نظام إشعارات متكامل
-   ✅ **الإحصائيات** - إحصائيات شاملة للمشاريع والمهام
-   ✅ **API موحد** - جميع الـ responses تستخدم HelperFunc

---

## 🚀 **التثبيت والتشغيل:**

### **1. متطلبات النظام:**

```bash
PHP >= 8.1
Composer
MySQL/PostgreSQL
Node.js & NPM (للـ frontend)
```

### **2. تثبيت المشروع:**

```bash
# استنساخ المشروع
git clone <repository-url>
cd task-octpber-

# تثبيت الـ dependencies
composer install
npm install

# نسخ ملف البيئة
cp .env.example .env

# إنشاء مفتاح التطبيق
php artisan key:generate

# تكوين قاعدة البيانات في .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_management
DB_USERNAME=root
DB_PASSWORD=root
```

### **3. إعداد قاعدة البيانات:**

```bash
# تشغيل الـ migrations
php artisan migrate

# تشغيل الـ seeders لإنشاء البيانات
php artisan db:seed --class=SimpleDataSeeder

# إنشاء الـ storage link
php artisan storage:link
```

### **4. تشغيل المشروع:**

```bash
# تشغيل الـ server
php artisan serve

# تشغيل الـ frontend (في terminal منفصل)
npm run dev
```

---

## 🔐 **بيانات تسجيل الدخول:**

### **المستخدمين المتاحين:**

```
كلمة المرور لجميع المستخدمين: password123

👨‍💼 المدراء:
- أحمد محمد: ahmed@company.com
- فاطمة علي: fatima@company.com

👨‍💻 مديري المشاريع:
- محمد حسن: mohamed@company.com
- سارة أحمد: sara@company.com

👨‍💻 المطورين:
- أميرة محمد: amira@company.com
- كريم أحمد: karim@company.com

🎨 المصممين:
- ليلى أحمد: laila@company.com

🧪 مختبرين الجودة:
- رانيا علي: rania@company.com
```

---

## 📚 **API Endpoints:**

### **🔐 Authentication:**

```http
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout
GET  /api/auth/profile
```

### **📋 Projects:**

```http
GET    /api/projects
POST   /api/projects
GET    /api/projects/{id}
PUT    /api/projects/{id}
DELETE /api/projects/{id}
POST   /api/projects/{id}/approve
```

### **✅ Tasks:**

```http
GET    /api/tasks
POST   /api/tasks
GET    /api/tasks/{id}
PUT    /api/tasks/{id}
DELETE /api/tasks/{id}
POST   /api/tasks/{id}/attachments
GET    /api/tasks/{id}/attachments/{attachment}/download
```

### **📊 Statistics:**

```http
GET /api/stats
GET /api/stats/user
```

### **🔔 Notifications:**

```http
GET    /api/notifications
GET    /api/notifications/unread-count
PATCH  /api/notifications/{id}/read
PATCH  /api/notifications/mark-all-read
DELETE /api/notifications/{id}
```

---

## 🧪 **تشغيل الـ Tests:**

### **تشغيل جميع الـ Tests:**

```bash
php artisan test
```

### **تشغيل Unit Tests فقط:**

```bash
php artisan test tests/Unit/HelperFuncTest.php
```

### **تشغيل Feature Tests فقط:**

```bash
php artisan test tests/Feature/SimpleApiTest.php
```

### **نتائج الـ Tests المتوقعة:**

```
Tests:    27 passed (81 assertions)
Duration: 1.41s
```

---

## 📝 **أمثلة استخدام API:**

### **1. تسجيل الدخول:**

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "ahmed@company.com",
    "password": "password123"
  }'
```

### **2. جلب المشاريع:**

```bash
curl -X GET http://localhost:8000/api/projects \
  -H "Authorization: Bearer {token}"
```

### **3. إنشاء مشروع جديد:**

```bash
curl -X POST http://localhost:8000/api/projects \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "مشروع جديد",
    "description": "وصف المشروع",
    "project_manager_id": 3,
    "start_date": "2024-01-01",
    "end_date": "2024-12-31"
  }'
```

### **4. إنشاء مهمة جديدة:**

```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "مهمة جديدة",
    "description": "وصف المهمة",
    "project_id": 1,
    "assigned_user_id": 5,
    "priority": "High",
    "due_date": "2024-06-30"
  }'
```

### **5. رفع مرفق:**

```bash
curl -X POST http://localhost:8000/api/tasks/1/attachments \
  -H "Authorization: Bearer {token}" \
  -F "file=@document.pdf"
```

---

## 🏗️ **هيكل المشروع:**

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

## 🔧 **HelperFunc Functions:**

### **الوظائف المتاحة:**

```php
// إرسال استجابة موحدة
HelperFunc::sendResponse($status, $message, $data = null)

// إرسال استجابة مع تصفح
HelperFunc::paginateResponse($paginator, $message)

// رفع ملف
HelperFunc::uploadFile($path, $file)

// حذف ملف
HelperFunc::deleteFile($filePath)

// تحويل مسار الصورة
HelperFunc::getImageUrl($path)

// إنشاء YouTube thumbnail
HelperFunc::getYouTubeThumbnail($videoId)

// تنسيق المدة الزمنية
HelperFunc::formatDuration($seconds)

// تحويل المدة إلى ثواني
HelperFunc::parseDurationToSeconds($duration)

// تحويل رابط Google Drive
HelperFunc::prepareGoogleDriveLink($link)

// معاملات التصفح
HelperFunc::getPaginationParams($request)

// تحديد الحد
HelperFunc::limit($value, $limit)
```

---

## 📊 **البيانات المتوفرة:**

### **المشاريع:**

-   **4 مشاريع** بحالات مختلفة (مفتوح، قيد التنفيذ، مكتمل)
-   **مشاريع معتمدة وغير معتمدة**

### **المهام:**

-   **10 مهام** موزعة على المشاريع
-   **مهام بأولويات مختلفة** (عالية، متوسطة، منخفضة)
-   **مهام بحالات مختلفة** (معلق، قيد التنفيذ، مكتمل)

### **المستخدمين:**

-   **18 مستخدم** بأدوار مختلفة
-   **صلاحيات محددة** لكل دور

---

## 🛠️ **استكشاف الأخطاء:**

### **مشاكل شائعة:**

#### **1. خطأ في قاعدة البيانات:**

```bash
# إعادة تشغيل الـ migrations
php artisan migrate:fresh --seed
```

#### **2. خطأ في الـ permissions:**

```bash
# إعادة إنشاء الـ roles
php artisan db:seed --class=SimpleDataSeeder
```

#### **3. خطأ في الـ storage:**

```bash
# إعادة إنشاء الـ storage link
php artisan storage:link
```

#### **4. خطأ في الـ tests:**

```bash
# تشغيل الـ tests مع تفاصيل أكثر
php artisan test --verbose
```

---

## 📈 **الأداء والتحسين:**

### **إعدادات الأداء:**

```bash
# تنظيف الـ cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# تحسين الأداء
php artisan optimize
```

### **مراقبة الأداء:**

```bash
# تشغيل الـ tests مع قياس الأداء
php artisan test --coverage
```

---

## 🤝 **المساهمة:**

### **إرشادات التطوير:**

1. **اتباع معايير PSR-12**
2. **كتابة tests لكل وظيفة جديدة**
3. **استخدام HelperFunc للاستجابات**
4. **كتابة التعليقات باللغة الإنجليزية**
5. **استخدام الرسائل العربية للمستخدمين**

### **إجراءات التطوير:**

```bash
# إنشاء branch جديد
git checkout -b feature/new-feature

# إضافة التغييرات
git add .

# عمل commit
git commit -m "Add new feature"

# رفع التغييرات
git push origin feature/new-feature
```

---

## 📞 **الدعم والمساعدة:**

### **للمساعدة التقنية:**

-   📧 البريد الإلكتروني: support@company.com
-   📱 الهاتف: +1234567890
-   💬 الدردشة: Slack #project-management

### **التوثيق الإضافي:**

-   📖 [Laravel Documentation](https://laravel.com/docs)
-   📖 [API Documentation](https://docs.example.com/api)
-   📖 [HelperFunc Documentation](https://docs.example.com/helperfunc)

---

## 🎉 **الخلاصة:**

**نظام إدارة المشاريع** جاهز للاستخدام بنسبة **100%** مع:

-   ✅ **API متكامل** لجميع العمليات
-   ✅ **نظام صلاحيات** متقدم
-   ✅ **رفع الملفات** آمن
-   ✅ **إحصائيات شاملة**
-   ✅ **tests كاملة** ومتسقة
-   ✅ **أداء محسن** ومستقر
-   ✅ **توثيق شامل** وواضح

**المشروع جاهز للإنتاج!** 🚀
#   H - s p a c e  
 