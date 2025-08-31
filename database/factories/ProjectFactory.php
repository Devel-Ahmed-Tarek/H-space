<?php
namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', '+1 month');
        $endDate   = $this->faker->dateTimeBetween($startDate, '+2 years');

        return [
            'name'               => $this->faker->sentence(3),
            'description'        => $this->faker->paragraph(3),
            'project_manager_id' => User::factory(),
            'status'             => $this->faker->randomElement(['Open', 'In Progress', 'Completed']),
            'start_date'         => $startDate,
            'end_date'           => $endDate,
            'is_approved'        => $this->faker->boolean(80), // 80% chance of being approved
        ];
    }

    /**
     * Indicate that the project is open.
     */
    public function open(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Open',
        ]);
    }

    /**
     * Indicate that the project is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'In Progress',
        ]);
    }

    /**
     * Indicate that the project is completed.
     */
    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Completed',
        ]);
    }

    /**
     * Indicate that the project is approved.
     */
    public function approved(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_approved' => true,
        ]);
    }

    /**
     * Indicate that the project is not approved.
     */
    public function notApproved(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_approved' => false,
        ]);
    }
}
