<?php
namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Task $task
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
        return (new MailMessage)
            ->subject('Task Overdue: ' . $this->task->title)
            ->line('A task assigned to you is overdue.')
            ->line('Task: ' . $this->task->title)
            ->line('Project: ' . $this->task->project->name)
            ->line('Due Date: ' . $this->task->due_date->format('M d, Y'))
            ->line('Current Status: ' . $this->task->status)
            ->action('Update Task', url('/tasks/' . $this->task->id))
            ->line('Please update the task status as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id'      => $this->task->id,
            'task_title'   => $this->task->title,
            'project_name' => $this->task->project->name,
            'due_date'     => $this->task->due_date->format('Y-m-d'),
            'status'       => $this->task->status,
            'message'      => 'Task "' . $this->task->title . '" is overdue',
        ];
    }
}
