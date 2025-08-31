<?php
namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Project $project,
        public string $status,
        public ?string $comments = null
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Project ' . ucfirst($this->status) . ': ' . $this->project->name);

        if ($this->status === 'approved') {
            $message->line('Your project has been approved!')
                ->line('Project: ' . $this->project->name)
                ->line('Status: Approved')
                ->action('View Project', url('/projects/' . $this->project->id));
        } else {
            $message->line('Your project has been rejected.')
                ->line('Project: ' . $this->project->name)
                ->line('Status: Rejected');

            if ($this->comments) {
                $message->line('Comments: ' . $this->comments);
            }
        }

        return $message->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'project_id'   => $this->project->id,
            'project_name' => $this->project->name,
            'status'       => $this->status,
            'comments'     => $this->comments,
            'message'      => 'Project "' . $this->project->name . '" has been ' . $this->status,
        ];
    }
}
