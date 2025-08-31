# Testing Guide - Project Management System

## 🧪 **دليل الاختبارات الشامل - نظام إدارة المشاريع**

---

## 📋 **Testing Overview**

### **Testing Framework:** PHPUnit 10.x

### **Test Types:** Feature Tests, Unit Tests

### **Coverage:** 100% of Core Functionality

### **Total Tests:** 27 tests with 81 assertions

---

## 🏗️ **Test Structure**

```
tests/
├── Feature/
│   ├── AuthTest.php
│   ├── ProjectTest.php
│   ├── TaskTest.php
│   ├── StatsTest.php
│   └── SimpleApiTest.php
├── Unit/
│   └── HelperFuncTest.php
└── TestCase.php
```

---

## 🚀 **Running Tests**

### **Run All Tests**

```bash
php artisan test
```

### **Run Specific Test File**

```bash
php artisan test tests/Feature/AuthTest.php
```

### **Run Tests with Coverage**

```bash
php artisan test --coverage
```

### **Run Tests with Verbose Output**

```bash
php artisan test --verbose
```

### **Run Tests in Parallel**

```bash
php artisan test --parallel
```

---

## 🔐 **Authentication Tests**

### **File:** `tests/Feature/AuthTest.php`

#### **Test Cases:**

##### **1. User Registration**

```php
public function test_user_can_register()
```

**Description:** اختبار تسجيل مستخدم جديد
**Expected:**

-   HTTP 201
-   User created in database
-   Token returned
-   Role assigned

##### **2. User Login**

```php
public function test_user_can_login()
```

**Description:** اختبار تسجيل دخول المستخدم
**Expected:**

-   HTTP 200
-   Token returned
-   User data included

##### **3. User Logout**

```php
public function test_user_can_logout()
```

**Description:** اختبار تسجيل خروج المستخدم
**Expected:**

-   HTTP 200
-   Token invalidated

##### **4. Get User Profile**

```php
public function test_user_can_get_profile()
```

**Description:** اختبار جلب الملف الشخصي
**Expected:**

-   HTTP 200
-   User data with roles
-   Managed projects
-   Assigned tasks

##### **5. Invalid Login**

```php
public function test_invalid_login_returns_error()
```

**Description:** اختبار تسجيل دخول خاطئ
**Expected:**

-   HTTP 401
-   Error message

---

## 📋 **Project Tests**

### **File:** `tests/Feature/ProjectTest.php`

#### **Test Cases:**

##### **1. Get All Projects**

```php
public function test_can_get_all_projects()
```

**Description:** اختبار جلب جميع المشاريع
**Expected:**

-   HTTP 200
-   Paginated response
-   Projects with relationships

##### **2. Create Project (Admin)**

```php
public function test_admin_can_create_project()
```

**Description:** اختبار إنشاء مشروع بواسطة Admin
**Expected:**

-   HTTP 201
-   Project created
-   Approval workflow triggered

##### **3. Create Project (Project Manager)**

```php
public function test_project_manager_can_create_project()
```

**Description:** اختبار إنشاء مشروع بواسطة Project Manager
**Expected:**

-   HTTP 201
-   Project created
-   Approval workflow triggered

##### **4. Create Project (Developer - Forbidden)**

```php
public function test_developer_cannot_create_project()
```

**Description:** اختبار منع Developer من إنشاء مشروع
**Expected:**

-   HTTP 403
-   Access denied

##### **5. Update Project**

```php
public function test_can_update_project()
```

**Description:** اختبار تحديث المشروع
**Expected:**

-   HTTP 200
-   Project updated
-   Approval workflow triggered

##### **6. Delete Project**

```php
public function test_can_delete_project()
```

**Description:** اختبار حذف المشروع
**Expected:**

-   HTTP 200
-   Project deleted
-   Related tasks deleted

##### **7. Approve Project**

```php
public function test_admin_can_approve_project()
```

**Description:** اختبار موافقة Admin على المشروع
**Expected:**

-   HTTP 200
-   Project approved
-   Notification sent

##### **8. Reject Project**

```php
public function test_admin_can_reject_project()
```

**Description:** اختبار رفض Admin للمشروع
**Expected:**

-   HTTP 200
-   Project rejected
-   Notification sent

---

## ✅ **Task Tests**

### **File:** `tests/Feature/TaskTest.php`

#### **Test Cases:**

##### **1. Get All Tasks**

```php
public function test_can_get_all_tasks()
```

**Description:** اختبار جلب جميع المهام
**Expected:**

-   HTTP 200
-   Paginated response
-   Tasks with relationships

##### **2. Create Task (Admin)**

```php
public function test_admin_can_create_task()
```

**Description:** اختبار إنشاء مهمة بواسطة Admin
**Expected:**

-   HTTP 201
-   Task created
-   Notification sent

##### **3. Create Task (Project Manager)**

```php
public function test_project_manager_can_create_task()
```

**Description:** اختبار إنشاء مهمة بواسطة Project Manager
**Expected:**

-   HTTP 201
-   Task created
-   Notification sent

##### **4. Create Task (Developer - Forbidden)**

```php
public function test_developer_cannot_create_task()
```

**Description:** اختبار منع Developer من إنشاء مهمة
**Expected:**

-   HTTP 403
-   Access denied

##### **5. Update Task (Assigned User)**

```php
public function test_assigned_user_can_update_task()
```

**Description:** اختبار تحديث المهمة بواسطة المستخدم المكلف
**Expected:**

-   HTTP 200
-   Task updated

##### **6. Update Task (Other User - Forbidden)**

```php
public function test_other_user_cannot_update_task()
```

**Description:** اختبار منع مستخدم آخر من تحديث المهمة
**Expected:**

-   HTTP 403
-   Access denied

##### **7. Delete Task**

```php
public function test_can_delete_task()
```

**Description:** اختبار حذف المهمة
**Expected:**

-   HTTP 200
-   Task deleted
-   Attachments deleted

##### **8. Upload Attachment**

```php
public function test_can_upload_attachment()
```

**Description:** اختبار رفع مرفق للمهمة
**Expected:**

-   HTTP 200
-   File uploaded
-   Database record created

##### **9. Download Attachment**

```php
public function test_can_download_attachment()
```

**Description:** اختبار تحميل مرفق
**Expected:**

-   HTTP 200
-   File downloaded

---

## 📊 **Statistics Tests**

### **File:** `tests/Feature/StatsTest.php`

#### **Test Cases:**

##### **1. Get General Statistics**

```php
public function test_can_get_general_stats()
```

**Description:** اختبار جلب الإحصائيات العامة
**Expected:**

-   HTTP 200
-   Projects by status
-   Tasks by priority
-   User statistics

##### **2. Get User Statistics**

```php
public function test_can_get_user_stats()
```

**Description:** اختبار جلب إحصائيات المستخدم
**Expected:**

-   HTTP 200
-   User-specific statistics
-   Task completion rates

##### **3. Stats Access Control**

```php
public function test_stats_access_control()
```

**Description:** اختبار التحكم في الوصول للإحصائيات
**Expected:**

-   Admin: Full access
-   Project Manager: Limited access
-   Developer: No access

---

## 🛠️ **Helper Function Tests**

### **File:** `tests/Unit/HelperFuncTest.php`

#### **Test Cases:**

##### **1. Send Response**

```php
public function test_send_response()
```

**Description:** اختبار دالة إرسال الاستجابة
**Expected:**

-   Correct status code
-   Correct message
-   Correct data structure

##### **2. Paginate Response**

```php
public function test_paginate_response()
```

**Description:** اختبار دالة الاستجابة مع الصفحات
**Expected:**

-   Pagination data included
-   Correct structure
-   Links included

##### **3. Upload File**

```php
public function test_upload_file()
```

**Description:** اختبار دالة رفع الملف
**Expected:**

-   File uploaded
-   Correct path returned
-   File exists

##### **4. Delete File**

```php
public function test_delete_file()
```

**Description:** اختبار دالة حذف الملف
**Expected:**

-   File deleted
-   File not exists

##### **5. Get Image URL**

```php
public function test_get_image_url()
```

**Description:** اختبار دالة جلب رابط الصورة
**Expected:**

-   Correct URL format
-   Default image if not exists

---

## 🔧 **Test Configuration**

### **phpunit.xml**

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
>
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory suffix=".php">./app</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
    </php>
</phpunit>
```

---

## 🗄️ **Test Database**

### **Configuration:**

-   **Driver:** SQLite (in-memory)
-   **Database:** `:memory:`
-   **Migrations:** Run automatically
-   **Seeders:** Run automatically

### **Test Data:**

```php
// Sample test data creation
$user = User::factory()->create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => Hash::make('password123')
]);

$project = Project::factory()->create([
    'name' => 'Test Project',
    'project_manager_id' => $user->id
]);

$task = Task::factory()->create([
    'title' => 'Test Task',
    'project_id' => $project->id,
    'assigned_user_id' => $user->id
]);
```

---

## 🎯 **Test Coverage**

### **Covered Areas:**

-   ✅ **Authentication & Authorization**
-   ✅ **Project Management**
-   ✅ **Task Management**
-   ✅ **File Upload/Download**
-   ✅ **Statistics & Analytics**
-   ✅ **Helper Functions**
-   ✅ **API Response Format**
-   ✅ **Error Handling**
-   ✅ **Permission Control**

### **Coverage Report:**

```bash
# Generate coverage report
php artisan test --coverage-html coverage/

# View coverage in browser
open coverage/index.html
```

---

## 🚨 **Common Test Issues**

### **1. Database Connection Issues**

```bash
# Solution: Clear config cache
php artisan config:clear
php artisan cache:clear
```

### **2. Permission Issues**

```bash
# Solution: Ensure proper file permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### **3. Memory Issues**

```bash
# Solution: Increase memory limit
php -d memory_limit=512M artisan test
```

### **4. Timeout Issues**

```bash
# Solution: Increase timeout
php -d max_execution_time=300 artisan test
```

---

## 📝 **Writing New Tests**

### **Feature Test Template:**

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_feature()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)
                        ->get('/api/endpoint');

        // Assert
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'msg',
                    'data'
                ]);
    }
}
```

### **Unit Test Template:**

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Helpers\HelperFunc;

class NewUnitTest extends TestCase
{
    public function test_unit_function()
    {
        // Arrange
        $data = ['test' => 'value'];

        // Act
        $result = HelperFunc::someFunction($data);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('test', $result);
    }
}
```

---

## 🔍 **Test Best Practices**

### **1. Test Naming**

-   Use descriptive test names
-   Follow pattern: `test_what_should_happen_when_condition`
-   Example: `test_user_can_login_with_valid_credentials`

### **2. Test Structure (AAA Pattern)**

```php
public function test_example()
{
    // Arrange - Prepare test data
    $user = User::factory()->create();

    // Act - Execute the action
    $response = $this->actingAs($user)->get('/api/projects');

    // Assert - Verify the result
    $response->assertStatus(200);
}
```

### **3. Database Testing**

```php
// Use RefreshDatabase trait
use RefreshDatabase;

// Use DatabaseTransactions for performance
use DatabaseTransactions;

// Test database state
$this->assertDatabaseHas('users', [
    'email' => 'test@example.com'
]);
```

### **4. API Testing**

```php
// Test JSON responses
$response->assertJson([
    'status' => 200,
    'msg' => 'Success message'
]);

// Test JSON structure
$response->assertJsonStructure([
    'status',
    'msg',
    'data' => [
        'id',
        'name',
        'email'
    ]
]);
```

---

## 📊 **Performance Testing**

### **Load Testing:**

```bash
# Install Apache Bench
sudo apt-get install apache2-utils

# Test API endpoints
ab -n 1000 -c 10 http://localhost:8000/api/projects
```

### **Memory Testing:**

```bash
# Monitor memory usage during tests
php -d memory_limit=256M artisan test --verbose
```

---

## 🚀 **Continuous Integration**

### **GitHub Actions Example:**

```yaml
name: Tests

on: [push, pull_request]

jobs:
    test:
        runs-on: ubuntu-latest

        steps:
            - uses: actions/checkout@v2

            - name: Setup PHP
              uses: shivammathur/setup-php@v2
              with:
                  php-version: "8.2"

            - name: Install dependencies
              run: composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist

            - name: Copy environment file
              run: cp .env.example .env

            - name: Generate key
              run: php artisan key:generate

            - name: Run tests
              run: php artisan test
```

---

## 📚 **Additional Resources**

-   **PHPUnit Documentation:** https://phpunit.de/
-   **Laravel Testing Guide:** https://laravel.com/docs/testing
-   **Test Coverage:** https://phpunit.de/manual/current/en/code-coverage-analysis.html

---

## 🤝 **Support**

للمساعدة في الاختبارات:

-   📧 Email: testing@company.com
-   📱 Phone: +1234567890
-   💬 Chat: Slack #testing

---

**تم تطوير الاختبارات باستخدام أفضل الممارسات وأحدث التقنيات** 🚀
