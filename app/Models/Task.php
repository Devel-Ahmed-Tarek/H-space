<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'project_id',
        'assigned_user_id',
        'status',
        'due_date',
        'priority',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'date',
    ];

    /**
     * Task status constants
     */
    const STATUS_TODO        = 'To Do';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_DONE        = 'Done';

    /**
     * Task priority constants
     */
    const PRIORITY_LOW    = 'Low';
    const PRIORITY_MEDIUM = 'Medium';
    const PRIORITY_HIGH   = 'High';
    const PRIORITY_URGENT = 'Urgent';

    /**
     * Get the project this task belongs to
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user assigned to this task
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    /**
     * Get the attachments for this task
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class);
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== self::STATUS_DONE;
    }

    /**
     * Check if task is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_DONE;
    }

    /**
     * Get task status options
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_TODO,
            self::STATUS_IN_PROGRESS,
            self::STATUS_DONE,
        ];
    }

    /**
     * Get task priority options
     */
    public static function getPriorityOptions(): array
    {
        return [
            self::PRIORITY_LOW,
            self::PRIORITY_MEDIUM,
            self::PRIORITY_HIGH,
            self::PRIORITY_URGENT,
        ];
    }
}
