<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\HelperFunc;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks
     */
    public function index(Request $request): JsonResponse
    {
        $user  = $request->user();
        $query = Task::with(['project', 'assignedUser', 'attachments']);

        // Filter based on user role
        if ($user->isAdmin()) {
            // Admins can see all tasks
        } elseif ($user->isProjectManager()) {
            // Project managers can see tasks in their projects
            $query->whereHas('project', function ($q) use ($user) {
                $q->where('project_manager_id', $user->id);
            });
        } else {
            // Developers can only see their assigned tasks
            $query->where('assigned_user_id', $user->id);
        }

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('overdue')) {
            if ($request->boolean('overdue')) {
                $query->where('due_date', '<', now())
                    ->where('status', '!=', 'Done');
            }
        }

        // Get pagination parameters using HelperFunc
        $paginationParams = HelperFunc::getPaginationParams($request, 15);
        $tasks            = $query->paginate($paginationParams['per_page']);

        // Use HelperFunc pagination response
        return HelperFunc::paginateResponse($tasks, 'تم جلب المهام بنجاح');
    }

    /**
     * Store a newly created task
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());

        // Send notification to assigned user
        $assignedUser = $task->assignedUser;
        if ($assignedUser) {
            $assignedUser->notify(new TaskAssignedNotification($task));
        }

        return HelperFunc::sendResponse(201, 'تم إنشاء المهمة بنجاح', $task->load(['project', 'assignedUser', 'attachments']));
    }

    /**
     * Display the specified task
     */
    public function show(Request $request, Task $task): JsonResponse
    {
        $user = $request->user();

        // Check if user has access to this task
        if (! $user->isAdmin() &&
            $task->assigned_user_id !== $user->id &&
            $task->project->project_manager_id !== $user->id) {
            return HelperFunc::sendResponse(403, 'غير مصرح. يمكنك فقط عرض المهام المكلفة لك أو المهام في مشاريعك.');
        }

        return HelperFunc::sendResponse(200, 'تم جلب المهمة بنجاح', $task->load(['project', 'assignedUser', 'attachments.uploadedBy']));
    }

    /**
     * Update the specified task
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $user = $request->user();

        // Check permissions
        if (! $user->isAdmin() &&
            $task->assigned_user_id !== $user->id &&
            $task->project->project_manager_id !== $user->id) {
            return HelperFunc::sendResponse(403, 'غير مصرح. يمكنك فقط تحديث المهام المكلفة لك أو المهام في مشاريعك.');
        }

        // If assigned user changed, send notification
        $oldAssignedUserId = $task->assigned_user_id;
        $task->update($request->validated());

        if ($request->has('assigned_user_id') && $request->assigned_user_id !== $oldAssignedUserId) {
            $newAssignedUser = $task->assignedUser;
            if ($newAssignedUser) {
                $newAssignedUser->notify(new TaskAssignedNotification($task));
            }
        }

        return HelperFunc::sendResponse(200, 'تم تحديث المهمة بنجاح', $task->load(['project', 'assignedUser', 'attachments']));
    }

    /**
     * Remove the specified task
     */
    public function destroy(Request $request, Task $task): JsonResponse
    {
        $user = $request->user();

        // Check permissions
        if (! $user->isAdmin() && $task->project->project_manager_id !== $user->id) {
            return HelperFunc::sendResponse(403, 'غير مصرح. فقط المديرين ومديري المشاريع يمكنهم حذف المهام.');
        }

        // Delete attachments
        foreach ($task->attachments as $attachment) {
            HelperFunc::deleteFile($attachment->file_path);
        }

        $task->delete();

        return HelperFunc::sendResponse(200, 'تم حذف المهمة بنجاح');
    }

    /**
     * Upload attachment to task
     */
    public function uploadAttachment(Request $request, Task $task): JsonResponse
    {
        $user = $request->user();

        // Check if user has access to this task
        if (! $user->isAdmin() &&
            $task->assigned_user_id !== $user->id &&
            $task->project->project_manager_id !== $user->id) {
            return HelperFunc::sendResponse(403, 'غير مصرح. يمكنك فقط رفع المرفقات للمهام المكلفة لك أو المهام في مشاريعك.');
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return HelperFunc::sendResponse(422, 'فشل في التحقق من البيانات', $validator->errors());
        }

        $file = $request->file('file');

        // Validate file exists and is accessible
        if (! $file || ! $file->isValid()) {
            return HelperFunc::sendResponse(422, 'الملف غير صالح أو غير موجود');
        }

        try {
            $path = HelperFunc::uploadFile('task-attachments', $file);

            // Get file size safely
            $fileSize = 0;
            try {
                $fileSize = $file->getSize();
            } catch (\Exception $e) {
                // If we can't get size, try to get it from the uploaded file
                if (file_exists($path)) {
                    $fileSize = filesize($path);
                }
            }

            $attachment = $task->attachments()->create([
                'file_name'   => $file->getClientOriginalName(),
                'file_path'   => $path,
                'file_size'   => $fileSize,
                'mime_type'   => $file->getMimeType(),
                'uploaded_by' => $user->id,
            ]);

        } catch (\Exception $e) {
            return HelperFunc::sendResponse(500, 'فشل في رفع الملف: ' . $e->getMessage());
        }

        return HelperFunc::sendResponse(201, 'تم رفع المرفق بنجاح', $attachment->load('uploadedBy'));
    }

    /**
     * Download task attachment
     */
    public function downloadAttachment(Request $request, Task $task, $attachmentId)
    {
        $user       = $request->user();
        $attachment = $task->attachments()->findOrFail($attachmentId);

        // Check if user has access to this task
        if (! $user->isAdmin() &&
            $task->assigned_user_id !== $user->id &&
            $task->project->project_manager_id !== $user->id) {
            return HelperFunc::sendResponse(403, 'غير مصرح. يمكنك فقط تحميل المرفقات للمهام المكلفة لك أو المهام في مشاريعك.');
        }

        if (! file_exists($attachment->file_path)) {
            return HelperFunc::sendResponse(404, 'الملف غير موجود');
        }

        return response()->download(
            $attachment->file_path,
            $attachment->file_name
        );
    }
}
