<?php

namespace App\Http\Controllers\Apis;

use App\Models\User;
use App\Models\userauth;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    // دالة تسجيل المستخدم وإرسال OTP ثابت
  public function register(Request $request)
  {
      // التحقق من صحة البيانات المدخلة
      $validator = Validator::make($request->all(), [
          'phone_number' => 'required',
      ]);

      if ($validator->fails()) {
          return response()->json(['error' => $validator->errors()], 400);
      }

      // البحث عن المستخدم بناءً على رقم الهاتف
      $user = User::where('phone_number', $request->phone_number)->first();

      if ($user) {
          // إذا كان المستخدم موجودًا، يتم إرسال OTP مرة أخرى
          $otp_code = '1234'; // كود ثابت للتجربة
          $user->update(['otp_code' => $otp_code]);

          return response()->json([
              'phone_number' => $request->phone_number,
              'message' => 'User already registered. OTP sent again.',
              'otp_code' => $otp_code // عرض الـ OTP فقط لأغراض الاختبار
          ], 200);
      }



      // تعيين OTP ثابت (للاختبار)
      $otp_code = '1234';

      // إنشاء المستخدم الجديد
      $newUser = User::create([
          'phone_number' => $request->phone_number,
          'otp_code' => $otp_code,
          'is_verified' => false,
      ]);

   

      return response()->json([
          'phone_number' => $request->phone_number,
          'message' => 'User registered successfully. OTP sent.',
          'otp_code' => $otp_code // عرض الـ OTP فقط لأغراض الاختبار
      ], 200);
  }



      public function verifyOtp(Request $request)
      {
          // التحقق من صحة البيانات المدخلة
          $validator = Validator::make($request->all(), [
              'phone_number' => 'required',
              'otp_code' => 'required',
          ]);

          if ($validator->fails()) {
              return response()->json(['error' => $validator->errors()], 400);
          }

          // العثور على المستخدم باستخدام رقم الهاتف
          $user = User::where('phone_number', $request->phone_number)->first();

          if (!$user) {
              return response()->json(['error' => 'User not found.'], 404);
          }

          // التحقق من OTP
          if ($user->otp_code == $request->otp_code) {
              // إذا تطابق، قم بتحديث حالة التحقق
              $user->is_verified = true;
              $user->save();



              // توليد JWT Token
              $token = JWTAuth::fromUser($user);

              return response()->json([
                  'message' => 'OTP verified successfully.',
                  'token' => $token, // إرجاع الـ JWT Token
              ], 200);
          }

          return response()->json(['error' => 'Invalid OTP.'], 400);
      }
  
  
  
   public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'message' => 'Logged out successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to log out.'
            ], 500);
        }
    } 
  
   public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'error' => 'User not found.'
            ], 404);
        }

        // حذف الحساب
        $user->delete();

        // إبطال الجلسة أو التوكن بعد الحذف
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Account deleted successfully.'
        ], 200);
    }
	
	
	public function login(Request $request)
{
    // التحقق من صحة البيانات المدخلة
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    // محاولة التوثيق باستخدام البريد الإلكتروني وكلمة المرور
    $credentials = $request->only('email', 'password');

    if (!$token = JWTAuth::attempt($credentials)) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    return response()->json(compact('token'));
}

	
	
  }
