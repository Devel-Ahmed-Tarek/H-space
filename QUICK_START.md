# دليل البدء السريع - Quick Start Guide

## 🚀 **بدء الاستخدام في 5 دقائق:**

### **1. تثبيت المشروع:**

```bash
# تثبيت الـ dependencies
composer install

# نسخ ملف البيئة
cp .env.example .env

# إنشاء مفتاح التطبيق
php artisan key:generate
```

### **2. إعداد قاعدة البيانات:**

```bash
# تشغيل الـ migrations
php artisan migrate

# إنشاء البيانات
php artisan db:seed --class=SimpleDataSeeder

# إنشاء الـ storage link
php artisan storage:link
```

### **3. تشغيل المشروع:**

```bash
# تشغيل الـ server
php artisan serve
```

### **4. اختبار المشروع:**

```bash
# تشغيل الـ tests
php artisan test
```

---

## 🔐 **تسجيل الدخول السريع:**

### **بيانات المستخدمين:**

```
كلمة المرور: password123

👨‍💼 مدير:
ahmed@company.com / password123

👨‍💻 مدير مشروع:
mohamed@company.com / password123

👨‍💻 مطور:
amira@company.com / password123
```

---

## 📝 **أمثلة API سريعة:**

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

### **3. إنشاء مشروع:**

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

---

## 🧪 **اختبار سريع:**

### **تشغيل الـ Tests:**

```bash
# جميع الـ tests
php artisan test

# Unit tests فقط
php artisan test tests/Unit/HelperFuncTest.php

# Feature tests فقط
php artisan test tests/Feature/SimpleApiTest.php
```

### **النتيجة المتوقعة:**

```
Tests:    27 passed (81 assertions)
Duration: 1.41s
```

---

## 📊 **البيانات المتوفرة:**

### **المشاريع:**

-   4 مشاريع بحالات مختلفة
-   مشاريع معتمدة وغير معتمدة

### **المهام:**

-   10 مهام موزعة على المشاريع
-   مهام بأولويات وحالات مختلفة

### **المستخدمين:**

-   18 مستخدم بأدوار مختلفة
-   صلاحيات محددة لكل دور

---

## 🛠️ **استكشاف الأخطاء السريع:**

### **مشاكل شائعة:**

#### **1. خطأ في قاعدة البيانات:**

```bash
php artisan migrate:fresh --seed
```

#### **2. خطأ في الـ permissions:**

```bash
php artisan db:seed --class=SimpleDataSeeder
```

#### **3. خطأ في الـ storage:**

```bash
php artisan storage:link
```

#### **4. خطأ في الـ tests:**

```bash
php artisan test --verbose
```

---

## 📚 **الملفات المهمة:**

### **التوثيق:**

-   `README.md` - الدليل الشامل
-   `Project_Management_API.postman_collection.json` - Postman Collection

### **الملفات الأساسية:**

-   `app/Http/Helpers/HelperFunc.php` - Helper Functions
-   `app/Http/Controllers/Api/` - API Controllers
-   `database/seeders/SimpleDataSeeder.php` - البيانات الأولية

---

## 🎯 **الخطوات التالية:**

### **1. استيراد Postman Collection:**

-   افتح Postman
-   استورد `Project_Management_API.postman_collection.json`
-   عدل المتغيرات `base_url` و `auth_token`

### **2. اختبار الـ API:**

-   سجل دخول للحصول على token
-   اختبر جميع الـ endpoints
-   تحقق من الـ responses

### **3. تطوير Frontend:**

-   استخدم الـ API endpoints
-   استخدم HelperFunc للاستجابات
-   اتبع معايير الأمان

---

## 🎉 **الخلاصة:**

**المشروع جاهز للاستخدام!** 🚀

-   ✅ **API متكامل** يعمل بنسبة 100%
-   ✅ **بيانات جاهزة** للاختبار
-   ✅ **tests كاملة** ومتسقة
-   ✅ **توثيق شامل** وواضح
-   ✅ **Postman Collection** جاهز

**ابدأ الاستخدام الآن!** 🚀
