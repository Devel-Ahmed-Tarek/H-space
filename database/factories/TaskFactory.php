<?php
namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'            => $this->faker->sentence(4),
            'description'      => $this->faker->paragraph(2),
            'project_id'       => Project::factory(),
            'assigned_user_id' => User::factory(),
            'status'           => $this->faker->randomElement(['To Do', 'In Progress', 'Done']),
            'priority'         => $this->faker->randomElement(['Low', 'Medium', 'High', 'Urgent']),
            'due_date'         => $this->faker->dateTimeBetween('now', '+2 months'),
        ];
    }

    /**
     * Indicate that the task is to do.
     */
    public function todo(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'To Do',
        ]);
    }

    /**
     * Indicate that the task is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'In Progress',
        ]);
    }

    /**
     * Indicate that the task is done.
     */
    public function done(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Done',
        ]);
    }

    /**
     * Indicate that the task is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn(array $attributes) => [
            'due_date' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
            'status'   => $this->faker->randomElement(['To Do', 'In Progress']),
        ]);
    }

    /**
     * Indicate that the task is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn(array $attributes) => [
            'priority' => 'High',
        ]);
    }

    /**
     * Indicate that the task is urgent.
     */
    public function urgent(): static
    {
        return $this->state(fn(array $attributes) => [
            'priority' => 'Urgent',
        ]);
    }
}
