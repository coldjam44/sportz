<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // عرض كل الإشعارات
    public function index()
{
    // جلب المستخدم الحالي من التوكن JWT
    $user = auth()->user();

    if (!$user) {
        return response()->json(['message' => 'غير مصرح'], 401);
    }

    // جلب الإشعارات الخاصة بالمستخدم فقط
    $notifications = Notification::where('user_id', $user->id)
                                 ->orderBy('created_at', 'desc')
                                 ->get();

    return response()->json([
        'message' => 'تم جلب الإشعارات بنجاح',
        'notifications' => $notifications,
    ]);
}

   public function store(Request $request)
{
    // تحقق من صحة البيانات المطلوبة بدون user_id من العميل
    $data = $request->validate([
        'title' => 'required|string|max:255',
        'message_ar' => 'required|string',
        'message_en' => 'nullable|string',
        'status' => 'nullable|in:new,read',
    ]);

    // اربط الاشعار بالمستخدم المسجل دخولاً
    $data['user_id'] = auth()->id();

    // إنشاء الاشعار
    $notification = Notification::create($data);

    // إرجاع الرد مع حالة 201 (تم الإنشاء)
    return response()->json($notification, 201);
}


    // عرض إشعار محدد
    public function show(Notification $notification)
    {
        return response()->json($notification);
    }

    // تعديل إشعار (واجهة التعديل)
    public function edit(Notification $notification)
    {
        //
    }

    // تحديث إشعار
    public function update(Request $request, Notification $notification)
    {
        $data = $request->validate([
            'user_id' => 'sometimes|integer',
            'title' => 'sometimes|string|max:255',
            'message_ar' => 'sometimes|string',
            'message_en' => 'nullable|string',
            'status' => 'nullable|in:new,read',
        ]);

        $notification->update($data);

        return response()->json($notification);
    }

    // حذف إشعار
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return response()->json(null, 204);
    }
}
