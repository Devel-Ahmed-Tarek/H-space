<?php
namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // Project Request Tests
    public function test_store_project_request_validation_passes(): void
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
        $this->assertEquals('تم إنشاء المشروع بنجاح', $response->json('msg'));
    }

    public function test_store_project_request_validation_fails_without_name(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $projectManager = User::factory()->create();
        $projectManager->assignRole('Project Manager');

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/projects', [
            'description'        => 'Test project description',
            'project_manager_id' => $projectManager->id,
            'start_date'         => '2024-01-01',
            'end_date'           => '2024-12-31',
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('name', $response->json('data'));
    }

    public function test_store_project_request_validation_fails_with_invalid_dates(): void
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
            'start_date'         => '2024-12-31',
            'end_date'           => '2024-01-01', // End date before start date
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('end_date', $response->json('data'));
    }

    public function test_store_project_request_authorization_fails_for_developer(): void
    {
        $developer = User::factory()->create();
        $developer->assignRole('Developer');

        $projectManager = User::factory()->create();
        $projectManager->assignRole('Project Manager');

        $token = $developer->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/projects', [
            'name'               => 'Test Project',
            'description'        => 'Test project description',
            'project_manager_id' => $projectManager->id,
            'start_date'         => '2024-01-01',
            'end_date'           => '2024-12-31',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('غير مصرح. فقط المديرين ومديري المشاريع يمكنهم إنشاء المشاريع.', $response->json('msg'));
    }

    // Task Request Tests
    public function test_store_task_request_validation_passes(): void
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
        $this->assertEquals('تم إنشاء المهمة بنجاح', $response->json('msg'));
    }

    public function test_store_task_request_validation_fails_with_past_due_date(): void
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
            'due_date'         => '2020-01-01', // Past date
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('due_date', $response->json('data'));
    }

    // Auth Request Tests
    public function test_login_request_validation_passes(): void
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
        $this->assertEquals('تم تسجيل الدخول بنجاح', $response->json('msg'));
    }

    public function test_login_request_validation_fails_with_invalid_email(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email'    => 'invalid-email',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('email', $response->json('data'));
    }

    public function test_register_request_validation_passes(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'Developer',
        ]);

        $response->assertStatus(201);
        $this->assertEquals('تم تسجيل المستخدم بنجاح', $response->json('msg'));
    }

    public function test_register_request_validation_fails_with_weak_password(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => '123',
            'password_confirmation' => '123',
            'role'                  => 'Developer',
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('password', $response->json('data'));
    }

    public function test_register_request_validation_fails_with_unmatched_password_confirmation(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'differentpassword',
            'role'                  => 'Developer',
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('password', $response->json('data'));
    }

    public function test_register_request_validation_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'Developer',
        ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('email', $response->json('data'));
    }

    // HelperFunc Response Format Tests
    public function test_helper_func_response_format(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $projectManager = User::factory()->create();
        $projectManager->assignRole('Project Manager');

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/projects');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('msg', $data);
        $this->assertArrayHasKey('data', $data);

        $this->assertEquals(200, $data['status']);
        // Check if message is either success or no data found
        $this->assertContains($data['msg'], ['تم جلب المشاريع بنجاح', 'No data found']);
        $this->assertArrayHasKey('data', $data['data']);
        $this->assertArrayHasKey('pagination', $data['data']);
    }

    public function test_pagination_response_format(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $projectManager = User::factory()->create();
        $projectManager->assignRole('Project Manager');

        // Create some projects
        Project::factory()->count(5)->create([
            'project_manager_id' => $projectManager->id,
        ]);

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/projects?per_page=3&page=1');

        $response->assertStatus(200);

        $data       = $response->json();
        $pagination = $data['data']['pagination'];

        $this->assertEquals(1, $pagination['current_page']);
        $this->assertEquals(3, $pagination['per_page']);
        $this->assertEquals(5, $pagination['total']);
        $this->assertArrayHasKey('links', $pagination);
        $this->assertArrayHasKey('first', $pagination['links']);
        $this->assertArrayHasKey('last', $pagination['links']);
    }
}
