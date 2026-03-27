<?php

namespace App\Http\Controllers;

use App\Models\Notification as NotificationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = NotificationModel::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = NotificationModel::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        NotificationModel::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy($id)
    {
        $notification = NotificationModel::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $notification->delete();
        return back()->with('success', 'Notification deleted successfully.');
    }

    public function deleteAll()
    {
        NotificationModel::where('user_id', Auth::id())->delete();
        return back()->with('success', 'All notifications deleted successfully.');
    }
}
