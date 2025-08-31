<?php
namespace App\Jobs;

use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectApprovalNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendProjectApprovalRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Project $project
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all admin users
        $admins = User::role('Admin')->get();

        // Send approval request to all admins
        foreach ($admins as $admin) {
            $admin->notify(new ProjectApprovalNotification(
                $this->project,
                'pending',
                'New project requires approval'
            ));
        }
    }
}
