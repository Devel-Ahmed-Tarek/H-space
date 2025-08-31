<?php
namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run seeders
        $this->seed();
    }

    public function test_admin_can_create_project(): void
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

        $responseData = $response->json();
        $this->assertArrayHasKey('success', $responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('data', $responseData);

        $data = $responseData['data'];
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('description', $data);
        $this->assertArrayHasKey('project_manager_id', $data);
        $this->assertArrayHasKey('start_date', $data);
        $this->assertArrayHasKey('end_date', $data);
        $this->assertArrayHasKey('created_at', $data);
        $this->assertArrayHasKey('updated_at', $data);
        $this->assertArrayHasKey('project_manager', $data);
        $this->assertArrayHasKey('tasks', $data);

        $this->assertDatabaseHas('projects', [
            'name'        => 'Test Project',
            'description' => 'Test project description',
        ]);
    }

    public function test_developer_cannot_create_project(): void
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
    }

    public function test_admin_can_view_all_projects(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $projectManager = User::factory()->create();
        $projectManager->assignRole('Project Manager');

        Project::factory()->create([
            'project_manager_id' => $projectManager->id,
        ]);

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/projects');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'status',
                            'project_manager',
                        ],
                    ],
                ],
            ]);
    }

    public function test_project_manager_can_view_their_projects(): void
    {
        $projectManager = User::factory()->create();
        $projectManager->assignRole('Project Manager');

        Project::factory()->create([
            'project_manager_id' => $projectManager->id,
        ]);

        $token = $projectManager->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/projects');

        $response->assertStatus(200);

        $data = $response->json('data.data');
        $this->assertCount(1, $data);
    }

    public function test_admin_can_approve_project(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $projectManager = User::factory()->create();
        $projectManager->assignRole('Project Manager');

        $project = Project::factory()->create([
            'project_manager_id' => $projectManager->id,
            'is_approved'        => false,
        ]);

        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/projects/{$project->id}/approve", [
            'status'   => 'approved',
            'comments' => 'Project approved',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('projects', [
            'id'          => $project->id,
            'is_approved' => true,
        ]);
    }
}
