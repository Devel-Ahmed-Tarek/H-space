<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebugTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_debug_validation_response(): void
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

        // Debug: Print the actual response structure
        echo "Response Status: " . $response->getStatusCode() . "\n";
        echo "Response Data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";

        // Check if it has the expected structure
        $this->assertIsArray($data);

        // Check if it has any of the expected keys
        if (isset($data['status'])) {
            $this->assertEquals(422, $data['status']);
        }

        if (isset($data['message'])) {
            $this->assertIsString($data['message']);
        }

        if (isset($data['errors'])) {
            $this->assertIsArray($data['errors']);
        }
    }

    public function test_debug_unauthorized_response(): void
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

        // Debug: Print the actual response structure
        echo "Response Status: " . $response->getStatusCode() . "\n";
        echo "Response Data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";

        // Check if it has the expected structure
        $this->assertIsArray($data);

        // Check if it has any of the expected keys
        if (isset($data['status'])) {
            $this->assertEquals(403, $data['status']);
        }

        if (isset($data['message'])) {
            $this->assertIsString($data['message']);
        }
    }
}
