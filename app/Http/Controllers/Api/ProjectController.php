<?php
namespace App\Http\Controllers\Api;

use App\Events\ProjectCreated;
use App\Http\Controllers\Controller;
use App\Http\Helpers\HelperFunc;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Jobs\SendProjectApprovalRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects
     */
    public function index(Request $request): JsonResponse
    {
        $user  = $request->user();
        $query = Project::with(['projectManager', 'tasks']);

        // Filter based on user role
        if ($user->isAdmin()) {
            // Admins can see all projects
        } elseif ($user->isProjectManager()) {
            // Project managers can see their own projects and projects they manage
            $query->where(function ($q) use ($user) {
                $q->where('project_manager_id', $user->id)
                    ->orWhereHas('tasks', function ($taskQuery) use ($user) {
                        $taskQuery->where('assigned_user_id', $user->id);
                    });
            });
        } else {
            // Developers can only see projects they have tasks in
            $query->whereHas('tasks', function ($q) use ($user) {
                $q->where('assigned_user_id', $user->id);
            });
        }

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('is_approved')) {
            $query->where('is_approved', $request->boolean('is_approved'));
        }

        // Get pagination parameters using HelperFunc
        $paginationParams = HelperFunc::getPaginationParams($request, 15);
        $projects         = $query->paginate($paginationParams['per_page']);

        // Use HelperFunc pagination response
        return HelperFunc::paginateResponse($projects, 'تم جلب المشاريع بنجاح');
    }

    /**
     * Store a newly created project
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());

        // Dispatch approval request job
        SendProjectApprovalRequest::dispatch($project);

        // Fire event
        event(new ProjectCreated($project));

        return HelperFunc::sendResponse(201, 'تم إنشاء المشروع بنجاح', $project->load(['projectManager', 'tasks']));
    }

    /**
     * Display the specified project
     */
    public function show(Request $request, Project $project): JsonResponse
    {
        $user = $request->user();

        // Check if user has access to this project
        if (! $user->isAdmin() &&
            $project->project_manager_id !== $user->id &&
            ! $project->tasks()->where('assigned_user_id', $user->id)->exists()) {
            return HelperFunc::sendResponse(403, 'غير مصرح. يمكنك فقط عرض المشاريع التي تديرها أو المهام المكلفة لك فيها.');
        }

        return HelperFunc::sendResponse(200, 'تم جلب المشروع بنجاح', $project->load(['projectManager', 'tasks.assignedUser', 'approvals.approver']));
    }

    /**
     * Update the specified project
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $user = $request->user();

        // Check permissions
        if (! $user->isAdmin() && $project->project_manager_id !== $user->id) {
            return HelperFunc::sendResponse(403, 'غير مصرح. فقط المديرين ومديري المشاريع يمكنهم تحديث المشاريع.');
        }

        $project->update($request->validated());

        return HelperFunc::sendResponse(200, 'تم تحديث المشروع بنجاح', $project->load(['projectManager', 'tasks']));
    }

    /**
     * Remove the specified project
     */
    public function destroy(Request $request, Project $project): JsonResponse
    {
        $user = $request->user();

        // Check permissions
        if (! $user->isAdmin() && $project->project_manager_id !== $user->id) {
            return HelperFunc::sendResponse(403, 'غير مصرح. فقط المديرين ومديري المشاريع يمكنهم حذف المشاريع.');
        }

        $project->delete();

        return HelperFunc::sendResponse(200, 'تم حذف المشروع بنجاح');
    }

    /**
     * Approve or reject a project
     */
    public function approve(Request $request, Project $project): JsonResponse
    {
        $user = $request->user();

        if (! $user->isAdmin()) {
            return HelperFunc::sendResponse(403, 'غير مصرح. فقط المديرين يمكنهم اعتماد المشاريع.');
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'status'   => 'required|in:approved,rejected',
            'comments' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return HelperFunc::sendResponse(422, 'فشل في التحقق من البيانات', $validator->errors());
        }

        // Create approval record
        $project->approvals()->create([
            'approver_id' => $user->id,
            'status'      => ucfirst($request->status),
            'comments'    => $request->comments,
            'approved_at' => now(),
        ]);

        // Update project approval status
        $project->update([
            'is_approved' => $request->status === 'approved',
        ]);

        // Notify project manager
        if ($project->projectManager) {
            $project->projectManager->notify(new \App\Notifications\ProjectApprovalNotification(
                $project,
                $request->status,
                $request->comments
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Project ' . $request->status . ' successfully',
            'data'    => $project->load(['projectManager', 'approvals.approver']),
        ]);
    }
}
