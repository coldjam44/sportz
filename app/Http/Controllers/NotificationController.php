<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\providerauth;

class NotificationController extends Controller
{
    public function testLogin(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['message' => 'غير مسجل دخول. الرجاء تسجيل الدخول أولاً.'], 401);
            }

            // تأكد من اسم الحقل الصحيح هنا
            $provider = providerauth::where('phone_number', $user->phone_number)->first();

            if ($provider) {
                return response()->json([
                    'message' => 'مسجل دخول كبروفايدر',
                    'user_type' => 'provider',
                    'provider' => $provider,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'مسجل دخول كمستخدم عادي',
                    'user_type' => 'user',
                    'user' => $user,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'غير مسجل دخول. الرجاء تسجيل الدخول أولاً.',
                'error' => $e->getMessage(),
            ], 401);
        }
    }
}
