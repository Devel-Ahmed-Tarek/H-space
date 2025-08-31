<?php
namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimpleApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

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

    public function test_projects_store_with_valid_data(): void
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
        $this->assertEquals('Test Project', $data['data']['name']);
    }

    public function test_projects_store_with_invalid_data(): void
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

        // Check Laravel validation response format
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('errors', $data);

        $this->assertArrayHasKey('name', $data['errors']);
        $this->assertArrayHasKey('description', $data['errors']);
    }

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

        // Check Laravel authorization response format
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals('This action is unauthorized.', $data['message']);
    }

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
}
