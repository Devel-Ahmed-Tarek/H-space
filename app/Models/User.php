<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Get the projects managed by this user
     */
    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'project_manager_id');
    }

    /**
     * Get the tasks assigned to this user
     */
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_user_id');
    }

    /**
     * Get the project approvals by this user
     */
    public function projectApprovals()
    {
        return $this->hasMany(ProjectApproval::class, 'approver_id');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    /**
     * Check if user is project manager
     */
    public function isProjectManager(): bool
    {
        return $this->hasRole('Project Manager');
    }

    /**
     * Check if user is developer
     */
    public function isDeveloper(): bool
    {
        return $this->hasRole('Developer');
    }
}
