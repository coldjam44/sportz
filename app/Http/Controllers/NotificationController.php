<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class NotificationController extends Controller
{
    // جلب كل الإشعارات للمستخدم الحالي
    public function index(Request $request)
{
    $user = Auth::user();
    $userType = $this->getUserTypeFromToken(); // دالة تستخرج user_type من التوكن

    $perPage = $request->input('per_page', 10);

    $notifications = Notification::where('user_id', $user->id)
        ->where('account_type', $userType)
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);

    Notification::where('user_id', $user->id)
        ->where('account_type', $userType)
        ->where('status', 'new')
        ->update(['status' => 'read']);

    return response()->json($notifications);
}




    public function store(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'title' => 'required|string|max:255',
        'message_ar' => 'required|string',
        'message_en' => 'nullable|string',
        'status' => 'in:new,read',
        'type' => 'nullable|string|max:255',
    ]);

    $accountType = $this->getUserTypeFromToken();

    $notification = Notification::create([
        'user_id' => $user->id,
        'title' => $request->title,
        'message_ar' => $request->message_ar,
        'message_en' => $request->message_en,
        'status' => $request->status ?? 'new',
        'type' => $request->type,
        'account_type' => $accountType,
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

    public function addNotification(array $data)
    {
        $accountType = $this->getUserTypeFromToken();

        if ($accountType) {
            $data['account_type'] = $accountType;
        } else {
            unset($data['account_type']);
        }

        return Notification::create($data);
    }

    // لاحظ أن دالة getUserTypeFromToken لازم تُعدل لتُعيد النوع بدل JSON response
    protected function getUserTypeFromToken(): ?string
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token) return null;

            $payload = JWTAuth::getPayload($token);
            return $payload->get('user_type');
        } catch (\Exception $e) {
            return null;
        }
    }
}
