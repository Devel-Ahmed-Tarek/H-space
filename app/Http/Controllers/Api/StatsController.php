<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\HelperFunc;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    /**
     * Get project management statistics
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Check if user has permission to view stats
        if (! $user->isAdmin() && ! $user->isProjectManager()) {
            return HelperFunc::sendResponse(403, 'غير مصرح. فقط المديرين ومديري المشاريع يمكنهم عرض الإحصائيات.');
        }

        // Projects by status
        $projectsByStatus = Project::select('status', DB::raw('count(*) as count'))
            ->when(! $user->isAdmin(), function ($query) use ($user) {
                $query->where('project_manager_id', $user->id);
            })
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Completed tasks per user
        $completedTasksPerUser = Task::select(
            'users.name',
            DB::raw('count(*) as completed_tasks')
        )
            ->join('users', 'tasks.assigned_user_id', '=', 'users.id')
            ->where('tasks.status', 'Done')
            ->when(! $user->isAdmin(), function ($query) use ($user) {
                $query->whereHas('project', function ($q) use ($user) {
                    $q->where('project_manager_id', $user->id);
                });
            })
            ->groupBy('users.id', 'users.name')
            ->orderBy('completed_tasks', 'desc')
            ->limit(10)
            ->get();

        // Most active users (based on task updates)
        $mostActiveUsers = User::select(
            'users.name',
            DB::raw('count(tasks.id) as total_tasks'),
            DB::raw('count(case when tasks.status = "Done" then 1 end) as completed_tasks')
        )
            ->leftJoin('tasks', 'users.id', '=', 'tasks.assigned_user_id')
            ->when(! $user->isAdmin(), function ($query) use ($user) {
                $query->whereHas('tasks.project', function ($q) use ($user) {
                    $q->where('project_manager_id', $user->id);
                });
            })
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_tasks', 'desc')
            ->limit(10)
            ->get();

        // Overdue tasks count
        $overdueTasksCount = Task::where('due_date', '<', now())
            ->where('status', '!=', 'Done')
            ->when(! $user->isAdmin(), function ($query) use ($user) {
                $query->whereHas('project', function ($q) use ($user) {
                    $q->where('project_manager_id', $user->id);
                });
            })
            ->count();

        // Tasks by priority
        $tasksByPriority = Task::select('priority', DB::raw('count(*) as count'))
            ->when(! $user->isAdmin(), function ($query) use ($user) {
                $query->whereHas('project', function ($q) use ($user) {
                    $q->where('project_manager_id', $user->id);
                });
            })
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority');

        // Projects approval status
        $projectsApprovalStatus = Project::select(
            DB::raw('count(case when is_approved = 1 then 1 end) as approved'),
            DB::raw('count(case when is_approved = 0 then 1 end) as pending')
        )
            ->when(! $user->isAdmin(), function ($query) use ($user) {
                $query->where('project_manager_id', $user->id);
            })
            ->first();

        return HelperFunc::sendResponse(200, 'تم جلب الإحصائيات بنجاح', [
            'projects_by_status'       => $projectsByStatus,
            'completed_tasks_per_user' => $completedTasksPerUser,
            'most_active_users'        => $mostActiveUsers,
            'overdue_tasks_count'      => $overdueTasksCount,
            'tasks_by_priority'        => $tasksByPriority,
            'projects_approval_status' => $projectsApprovalStatus,
            'summary'                  => [
                'total_projects' => Project::when(! $user->isAdmin(), function ($query) use ($user) {
                    $query->where('project_manager_id', $user->id);
                })->count(),
                'total_tasks'    => Task::when(! $user->isAdmin(), function ($query) use ($user) {
                    $query->whereHas('project', function ($q) use ($user) {
                        $q->where('project_manager_id', $user->id);
                    });
                })->count(),
                'total_users'    => User::count(),
            ],
        ]);
    }

    /**
     * Get user-specific statistics
     */
    public function userStats(Request $request): JsonResponse
    {
        $user = $request->user();

        $userStats = [
            'assigned_tasks'     => $user->assignedTasks()->count(),
            'completed_tasks'    => $user->assignedTasks()->where('status', 'Done')->count(),
            'overdue_tasks'      => $user->assignedTasks()
                ->where('due_date', '<', now())
                ->where('status', '!=', 'Done')
                ->count(),
            'managed_projects'   => $user->managedProjects()->count(),
            'active_projects'    => $user->managedProjects()->where('status', 'In Progress')->count(),
            'completed_projects' => $user->managedProjects()->where('status', 'Completed')->count(),
        ];

        // Calculate completion rate
        $userStats['completion_rate'] = $userStats['assigned_tasks'] > 0
        ? round(($userStats['completed_tasks'] / $userStats['assigned_tasks']) * 100, 2)
        : 0;

        return HelperFunc::sendResponse(200, 'تم جلب إحصائيات المستخدم بنجاح', $userStats);
    }
}
