<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\HelperFunc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get user notifications
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get pagination parameters using HelperFunc
        $paginationParams = HelperFunc::getPaginationParams($request, 15);

        $notifications = $user->notifications()
            ->when($request->has('unread'), function ($query) use ($request) {
                if ($request->boolean('unread')) {
                    $query->whereNull('read_at');
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate($paginationParams['per_page']);

        // Use HelperFunc pagination response
        return HelperFunc::paginateResponse($notifications, 'تم جلب الإشعارات بنجاح');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $user         = $request->user();
        $notification = $user->notifications()->findOrFail($id);

        $notification->markAsRead();

        return HelperFunc::sendResponse(200, 'تم تحديد الإشعار كمقروء');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();

        return HelperFunc::sendResponse(200, 'تم تحديد جميع الإشعارات كمقروءة');
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user         = $request->user();
        $notification = $user->notifications()->findOrFail($id);

        $notification->delete();

        return HelperFunc::sendResponse(200, 'تم حذف الإشعار');
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $user  = $request->user();
        $count = $user->unreadNotifications()->count();

        return HelperFunc::sendResponse(200, 'تم جلب عدد الإشعارات غير المقروءة', [
            'unread_count' => $count,
        ]);
    }
}
