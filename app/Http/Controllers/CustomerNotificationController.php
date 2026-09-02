<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Customer;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerNotificationController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function index(Request $request): View
    {
        $customer = $this->customer();

        $categoryFilter = match ($request->string('filter')->toString()) {
            'orders' => 'order_update',
            'promotions' => 'promotion',
            'account' => 'account',
            default => null,
        };

        $groups = $this->notifications->groupedFor($customer, $categoryFilter);
        $totalCount = $groups->flatten(1)->count();
        $unreadCount = $this->notifications->unreadCountFor($customer);

        return view('account-notifications', [
            'customer' => $customer,
            'groups' => $groups,
            'unreadCount' => $unreadCount,
            'totalCount' => $totalCount,
            'activeFilter' => $request->string('filter')->toString() ?: 'all',
        ]);
    }

    public function markRead(AppNotification $notification): JsonResponse
    {
        $customer = $this->customer();
        $this->notifications->markAsRead($notification, $customer);

        return response()->json([
            'message' => 'Notification marked as read.',
            'unread_count' => $this->notifications->unreadCountFor($customer),
        ]);
    }

    public function markAllRead(): JsonResponse|RedirectResponse
    {
        $customer = $this->customer();
        $count = $this->notifications->markAllAsRead($customer);

        if (request()->wantsJson()) {
            return response()->json([
                'message' => $count > 0 ? 'All notifications marked as read.' : 'No unread notifications.',
                'unread_count' => 0,
            ]);
        }

        return redirect()
            ->route('account.notifications')
            ->with('success', $count > 0 ? 'All notifications marked as read.' : 'No unread notifications.');
    }

    private function customer(): Customer
    {
        /** @var Customer $customer */
        $customer = auth('customer')->user();

        return $customer;
    }
}
