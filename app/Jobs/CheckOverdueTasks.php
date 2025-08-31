<?php
namespace App\Jobs;

use App\Models\Task;
use App\Notifications\TaskOverdueNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckOverdueTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all overdue tasks
        $overdueTasks = Task::where('due_date', '<', now())
            ->where('status', '!=', 'Done')
            ->with(['assignedUser', 'project'])
            ->get();

        foreach ($overdueTasks as $task) {
            if ($task->assignedUser) {
                $task->assignedUser->notify(new TaskOverdueNotification($task));
            }
        }
    }
}
