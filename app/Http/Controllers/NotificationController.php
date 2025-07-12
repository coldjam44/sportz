<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // جلب كل الإشعارات للمستخدم الحالي
    public function index()
    {
        $user = Auth::user();

        // جلب الإشعارات الخاصة بالمستخدم
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // تحديث حالة كل الإشعارات إلى 'read' لو كانت 'new'
        Notification::where('user_id', $user->id)
            ->where('status', 'new')
            ->update(['status' => 'read']);

        return response()->json($notifications);
    }


    // إنشاء إشعار جديد
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'title' => 'required|string|max:255',
            'message_ar' => 'required|string',
            'message_en' => 'nullable|string',
            'status' => 'in:new,read', // اختياري، القيمة الافتراضية new
            'type' => 'nullable|string|max:255',
        ]);

        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'message_ar' => $request->message_ar,
            'message_en' => $request->message_en,
            'status' => $request->status ?? 'new',
            'type' => $request->type,
        ]);

        return response()->json([
            'message' => 'Notification created successfully.',
            'notification' => $notification,
        ], 201);
    }

    // عرض إشعار معين
    public function show($id)
    {
        $user = Auth::user();

        $notification = Notification::where('id', $id)->where('user_id', $user->id)->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found.'], 404);
        }

        return response()->json($notification);
    }

    // تحديث حالة الإشعار (مثلاً تغييره من 'new' إلى 'read')
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $notification = Notification::where('id', $id)->where('user_id', $user->id)->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found.'], 404);
        }

        $request->validate([
            'status' => 'required|in:new,read',
        ]);

        $notification->status = $request->status;
        $notification->save();

        return response()->json([
            'message' => 'Notification updated successfully.',
            'notification' => $notification,
        ]);
    }

    // حذف إشعار
    public function destroy($id)
    {
        $user = Auth::user();

        $notification = Notification::where('id', $id)->where('user_id', $user->id)->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found.'], 404);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification deleted successfully.']);
    }
}
