<?php
namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // Project API Tests
    public function test_projects_index_returns_helper_func_format(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $projectManager = User::factory()->create();
        $projectManager->assignRole('Project Manager');

        // Create some projects
        Project::factory()->count(3)->create([
            'project_manager_id' => $projectManager->id,
        ]);

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/projects');

        $response->assertStatus(200);

        $data = $response->json();

        // Check HelperFunc response format
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('msg', $data);
        $this->assertArrayHasKey('data', $data);

        $this->assertEquals(200, $data['status']);
        $this->assertEquals('تم جلب المشاريع بنجاح', $data['msg']);

        // Check pagination format
        $this->assertArrayHasKey('data', $data['data']);
        $this->assertArrayHasKey('pagination', $data['data']);

        $pagination = $data['data']['pagination'];
        $this->assertArrayHasKey('current_page', $pagination);
        $this->assertArrayHasKey('per_page', $pagination);
        $this->assertArrayHasKey('total', $pagination);
        $this->assertArrayHasKey('last_page', $pagination);
        $this->assertArrayHasKey('links', $pagination);
    }

    public function test_projects_store_returns_helper_func_format(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $projectManager = User::factory()->create();
        $projectManager->assignRole('Project Manager');

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/projects', [
            'name'               => 'Test Project',
            'description'        => 'Test project description',
            'project_manager_id' => $projectManager->id,
            'start_date'         => '2024-01-01',
            'end_date'           => '2024-12-31',
        ]);

        $response->assertStatus(201);

        $data = $response->json();

        // Check HelperFunc response format
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('msg', $data);
        $this->assertArrayHasKey('data', $data);

        $this->assertEquals(201, $data['status']);
        $this->assertEquals('تم إنشاء المشروع بنجاح', $data['msg']);

        // Check project data
        $this->assertArrayHasKey('id', $data['data']);
        $this->assertArrayHasKey('name', $data['data']);
        $this->assertArrayHasKey('project_manager', $data['data']);
        $this->assertArrayHasKey('tasks', $data['data']);
    }

    public function test_projects_update_returns_helper_func_format(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $projectManager = User::factory()->create();
        $projectManager->assignRole('Project Manager');

        $project = Project::factory()->create([
            'project_manager_id' => $projectManager->id,
        ]);

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/projects/{$project->id}", [
            'name'        => 'Updated Project Name',
            'description' => 'Updated project description',
        ]);

        $response->assertStatus(200);

        $data = $response->json();

        // Check HelperFunc response format
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('msg', $data);
        $this->assertArrayHasKey('data', $data);

        $this->assertEquals(200, $data['status']);
        $this->assertEquals('تم تحديث المشروع بنجاح', $data['msg']);

        // Check updated data
        $this->assertEquals('Updated Project Name', $data['data']['name']);
        $this->assertEquals('Updated project description', $data['data']['description']);
    }

    public function test_projects_destroy_returns_helper_func_format(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $projectManager = User::factory()->create();
        $projectManager->assignRole('Project Manager');

        $project = Project::factory()->create([
            'project_manager_id' => $projectManager->id,
        ]);

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(200);

        $data = $response->json();

        // Check HelperFunc response format
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('msg', $data);
        $this->assertArrayHasKey('data', $data);

        $this->assertEquals(200, $data['status']);
        $this->assertEquals('تم حذف المشروع بنجاح', $data['msg']);

        // Check project was deleted
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    // Task API Tests
    public function test_tasks_index_returns_helper_func_format(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $projectManager = User::factory()->create();
        $projectManager->assignRole('Project Manager');

        $developer = User::factory()->create();
        $developer->assignRole('Developer');

        $project = Project::factory()->create([
            'project_manager_id' => $projectManager->id,
        ]);

        // Create some tasks
        Task::factory()->count(3)->create([
            'project_id'       => $project->id,
            'assigned_user_id' => $developer->id,
        ]);

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/tasks');

        $response->assertStatus(200);

        $data = $response->json();

        // Check HelperFunc response format
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('msg', $data);
        $this->assertArrayHasKey('data', $data);

        $this->assertEquals(200, $data['status']);
        $this->assertEquals('تم جلب المهام بنجاح', $data['msg']);

        // Check pagination format
        $this->assertArrayHasKey('data', $data['data']);
        $this->assertArrayHasKey('pagination', $data['data']);
    }

    public function test_tasks_store_returns_helper_func_format(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $projectManager = User::factory()->create();
        $projectManager->assignRole('Project Manager');

        $developer = User::factory()->create();
        $developer->assignRole('Developer');

        $project = Project::factory()->create([
            'project_manager_id' => $projectManager->id,
        ]);

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/tasks', [
            'title'            => 'Test Task',
            'description'      => 'Test task description',
            'project_id'       => $project->id,
            'assigned_user_id' => $developer->id,
            'due_date'         => '2024-12-31',
        ]);

        $response->assertStatus(201);

        $data = $response->json();

        // Check HelperFunc response format
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('msg', $data);
        $this->assertArrayHasKey('data', $data);

        $this->assertEquals(201, $data['status']);
        $this->assertEquals('تم إنشاء المهمة بنجاح', $data['msg']);

        // Check task data
        $this->assertArrayHasKey('id', $data['data']);
        $this->assertArrayHasKey('title', $data['data']);
        $this->assertArrayHasKey('project', $data['data']);
        $this->assertArrayHasKey('assigned_user', $data['data']);
    }

    // Auth API Tests
    public function test_auth_login_returns_helper_func_format(): void
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);

        $data = $response->json();

        // Check HelperFunc response format
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('msg', $data);
        $this->assertArrayHasKey('data', $data);

        $this->assertEquals(200, $data['status']);
        $this->assertEquals('تم تسجيل الدخول بنجاح', $data['msg']);

        // Check auth data
        $this->assertArrayHasKey('user', $data['data']);
        $this->assertArrayHasKey('token', $data['data']);
        $this->assertArrayHasKey('token_type', $data['data']);
    }

    public function test_auth_register_returns_helper_func_format(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'Developer',
        ]);

        $response->assertStatus(201);

        $data = $response->json();

        // Check HelperFunc response format
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('msg', $data);
        $this->assertArrayHasKey('data', $data);

        $this->assertEquals(201, $data['status']);
        $this->assertEquals('تم تسجيل المستخدم بنجاح', $data['msg']);

        // Check auth data
        $this->assertArrayHasKey('user', $data['data']);
        $this->assertArrayHasKey('token', $data['data']);
        $this->assertArrayHasKey('token_type', $data['data']);
    }

    // Notification API Tests
    public function test_notifications_index_returns_helper_func_format(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/notifications');

        $response->assertStatus(200);

        $data = $response->json();

        // Check HelperFunc response format
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('msg', $data);
        $this->assertArrayHasKey('data', $data);

        $this->assertEquals(200, $data['status']);
        $this->assertEquals('تم جلب الإشعارات بنجاح', $data['msg']);

        // Check pagination format
        $this->assertArrayHasKey('data', $data['data']);
        $this->assertArrayHasKey('pagination', $data['data']);
    }

    public function test_notifications_mark_as_read_returns_helper_func_format(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Create a notification
        $notification = $user->notifications()->create([
            'id'   => 'test-notification-id',
            'type' => 'App\Notifications\TestNotification',
            'data' => ['message' => 'Test notification'],
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/notifications/{$notification->id}/mark-as-read");

        $response->assertStatus(200);

        $data = $response->json();

        // Check HelperFunc response format
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('msg', $data);
        $this->assertArrayHasKey('data', $data);

        $this->assertEquals(200, $data['status']);
        $this->assertEquals('تم تحديد الإشعار كمقروء', $data['msg']);
    }

    // Error Response Tests
    public function test_unauthorized_access_returns_helper_func_format(): void
    {
        $developer = User::factory()->create();
        $developer->assignRole('Developer');

        $token = $developer->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/projects', [
            'name'        => 'Test Project',
            'description' => 'Test description',
        ]);

        $response->assertStatus(403);

        $data = $response->json();

        // Check HelperFunc response format
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('msg', $data);
        $this->assertArrayHasKey('data', $data);

        $this->assertEquals(403, $data['status']);
        $this->assertEquals('غير مصرح. فقط المديرين ومديري المشاريع يمكنهم إنشاء المشاريع.', $data['msg']);
    }

    public function test_validation_error_returns_helper_func_format(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/projects', [
            // Missing required fields
        ]);

        $response->assertStatus(422);

        $data = $response->json();

        // Check HelperFunc response format
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('msg', $data);
        $this->assertArrayHasKey('data', $data);

        $this->assertEquals(422, $data['status']);
        $this->assertArrayHasKey('name', $data['data']);
        $this->assertArrayHasKey('description', $data['data']);
    }
}
