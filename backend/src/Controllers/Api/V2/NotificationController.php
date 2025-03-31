<?php

namespace App\Controllers\Api\V2;

use App\Core\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function __construct()
    {
        // Ensure user is authenticated for all notification endpoints
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $notifications = Notification::where('user_id', $user->id)
            ->with(['notifiable'])
            ->latest()
            ->paginate(20);

        return $this->json([
            'success' => true,
            'notifications' => $this->formatNotifications($notifications->items()),
            'pagination' => [
                'total' => $notifications->total(),
                'per_page' => $notifications->perPage(),
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage()
            ],
            'unread_count' => Notification::where('user_id', $user->id)
                ->where('read', false)
                ->count()
        ]);
    }

    public function markAsRead($id)
    {
        $user = auth()->user();
        $notification = Notification::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return $this->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->update(['read' => true]);

        return $this->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    public function markAllAsRead()
    {
        $user = auth()->user();
        Notification::where('user_id', $user->id)
            ->where('read', false)
            ->update(['read' => true]);

        return $this->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    private function formatNotifications($notifications)
    {
        return array_map(function($notification) {
            $formatted = [
                'id' => $notification->id,
                'type' => $notification->type,
                'message' => $notification->message,
                'read' => (bool) $notification->read,
                'created_at' => $notification->created_at,
                'notifiable_type' => $notification->notifiable_type,
                'notifiable_id' => $notification->notifiable_id
            ];

            // Add notifiable data if available
            if ($notification->notifiable) {
                $formatted['notifiable'] = $this->formatNotifiable($notification->notifiable);
            }

            return $formatted;
        }, $notifications);
    }

    private function formatNotifiable($notifiable)
    {
        // Format based on notifiable type
        switch (get_class($notifiable)) {
            case 'App\Models\Mod':
                return [
                    'id' => $notifiable->id,
                    'name' => $notifiable->name,
                    'type' => 'mod'
                ];
            case 'App\Models\Comment':
                return [
                    'id' => $notifiable->id,
                    'content' => $notifiable->content,
                    'type' => 'comment'
                ];
            default:
                return null;
        }
    }
}