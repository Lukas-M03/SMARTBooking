<?php

namespace App\Http\Controllers;

use App\Models\Notification as NotificationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = NotificationModel::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($request->boolean('unread')) {
            $query->where('is_read', false);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $query->get()->values(),
            ]);
        }

        $notifications = $query->paginate(7);

        return view('notifications.index', ['notifications' => $notifications]);
    }

    public function show(Request $request, NotificationModel $notification)
    {
        $user = Auth::user();

        $ownsNotification = (int) $notification->user_id === (int) $user->id;
        $relatedToBooking = $notification->booking
            && ((int) $notification->booking->student_id === (int) $user->id
                || (int) $notification->booking->adviser_id === (int) $user->id);

        abort_unless($ownsNotification || $relatedToBooking, 404);

        if ($request->expectsJson()) {
            return response()->json(['data' => $notification]);
        }

        return view('notifications.index', ['notifications' => collect([$notification])]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = NotificationModel::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->markAsRead();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Notification marked as read.']);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(Request $request)
    {
        NotificationModel::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'All notifications marked as read.']);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Request $request, $id)
    {
        $notification = NotificationModel::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $notification->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Notification deleted successfully.']);
        }

        return back()->with('success', 'Notification deleted successfully.');
    }

    public function deleteAll()
    {
        NotificationModel::where('user_id', Auth::id())->delete();
        return back()->with('success', 'All notifications deleted successfully.');
    }
}
