<?php

namespace App\Http\Controllers\API;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('sanctum')->user(); 
        $notifications = $user->notifications()->paginate(15);
        $result = [
                'notifications' => $notifications,
                'unread_count' => $user->unreadNotifications()->count()
            ];
        return ApiResponseClass::sendResponse($result, 'Notifications retrieved successfully.');
    }

    public function markAsRead(Request $request, $id)
    {
        $user = auth('sanctum')->user(); 
        $notification = $user->unreadNotifications()->where('id', $id)->first();
        if (!$notification) {
            return ApiResponseClass::sendError('Notification not found or already read.');
        }
        $notification->markAsRead();
        return ApiResponseClass::sendResponse([], 'Notification marked as read successfully.');
    }

    public function markAllAsRead(Request $request)
    {
        $user = auth('sanctum')->user(); 
        $user->unreadNotifications->markAsRead();
        return ApiResponseClass::sendResponse([], 'All notifications marked as read successfully.');
    }
}