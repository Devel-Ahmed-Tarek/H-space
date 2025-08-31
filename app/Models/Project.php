<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'project_manager_id',
        'status',
        'start_date',
        'end_date',
        'is_approved',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'is_approved' => 'boolean',
    ];

    /**
     * Project status constants
     */
    const STATUS_OPEN        = 'Open';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_COMPLETED   = 'Completed';

    /**
     * Get the project manager for this project
     */
    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    /**
     * Get the tasks for this project
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the approvals for this project
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(ProjectApproval::class);
    }

    /**
     * Check if project is approved
     */
    public function isApproved(): bool
    {
        return $this->is_approved;
    }

    /**
     * Get project status options
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_OPEN,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
        ];
    }
}
