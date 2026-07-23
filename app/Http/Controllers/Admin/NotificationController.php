<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function index(Request $request): View
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('notifications.view'), 403);

        $user = Auth::guard('admin')->user();

        return view('admin.notifications.index', [
            'title' => 'Notifications',
            'breadcrumbs' => [
                ['label' => 'System'],
                ['label' => 'Notifications'],
            ],
            'notifications' => $this->notifications->paginateFor($user, [
                'unread' => $request->boolean('unread') ?: null,
                'category' => $request->string('category')->toString() ?: null,
            ]),
            'unreadCount' => $this->notifications->unreadCountFor($user),
            'filters' => $request->only(['unread', 'category']),
        ]);
    }

    public function markRead(AppNotification $notification): JsonResponse|RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('notifications.view'), 403);

        $user = Auth::guard('admin')->user();
        $this->notifications->markAsRead($notification, $user);

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Notification marked as read.',
                'unread_count' => $this->notifications->unreadCountFor($user),
            ]);
        }

        return back();
    }

    public function markAllRead(): JsonResponse|RedirectResponse
    {
        abort_unless(Auth::guard('admin')->user()?->hasPermission('notifications.view'), 403);

        $user = Auth::guard('admin')->user();
        $count = $this->notifications->markAllAsRead($user);

        if (request()->wantsJson()) {
            return response()->json([
                'message' => $count > 0 ? 'All notifications marked as read.' : 'No unread notifications.',
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', $count > 0 ? 'All notifications marked as read.' : 'No unread notifications.');
    }
}
