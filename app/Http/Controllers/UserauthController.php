<?php

namespace App\Http\Controllers;

use App\Models\userauth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserauthController extends Controller
{
  
  public function deleteAccount(Request $request)
{
    try {
        // التحقق من أن المستخدم مصادق عليه باستخدام JWT
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مصادق عليه'], 401);
        }

        // البحث عن حساب المستخدم في جدول UserAuth باستخدام رقم الهاتف
        $userauth = UserAuth::where('phone', $user->phone_number)->first();

        if (!$userauth) {
            return response()->json(['message' => 'المستخدم غير موجود في قاعدة البيانات'], 400);
        }

        // حذف بيانات المستخدم من جدول UserAuth
        $userauth->delete();

        // حذف بيانات المستخدم من جدول users (إن وجد)
        $user->delete();

        return response()->json([
            'message' => 'تم حذف الحساب بنجاح',
        ], 200);

    } catch (\Exception $e) {
        return response()->json(['message' => 'حدث خطأ: ' . $e->getMessage()], 500);
    }
}

  
  public function logout(Request $request)
{
    try {
        // التأكد من وجود التوكن
        if (!$token = JWTAuth::getToken()) {
            return response()->json(['message' => 'Token not provided'], 400);
        }

        // إبطال التوكن
        JWTAuth::invalidate($token);

        return response()->json(['message' => 'User logged out successfully'], 200);
    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        return response()->json(['message' => 'Failed to logout, please try again'], 500);
    }
}




    public function signup(Request $request)
    {
        // تأكد أن المستخدم مسجل دخول باستخدام التوكن
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please log in first.'], 401);
        }

        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
'phone' => 'required|string|unique:userauths,phone',
            'email' => 'nullable|email|unique:userauths,email,' . $user->id,
            'gender' => 'required|in:male,female',
            'province' => 'required|string',
            'city' => 'required|string',
            'area' => 'required|string',
        ]);

       if ($validator->fails()) {
    $message = '';
    foreach ($validator->errors()->messages() as $field => $errors) {
        foreach ($errors as $error) {
            $message .= ucfirst($field) . ': ' . $error . "\n";
        }
    }
    return response()->json(['message' => trim($message)], 422);
}


        $user = userauth::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'gender' => $request->gender,
                    'province' => $request->province,
                    'city' => $request->city,
                    'area' => $request->area,
                ]);

                return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
            }


    /**
     * Display a listing of the resource.
     */
  public function index(Request $request)
{
    try {
        // جلب المستخدم المصادق عليه من التوكن
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized. Please log in first.'
            ], 401);
        }

        // جلب بيانات `UserAuth` بناءً على `phone`
        $userData = UserAuth::where('phone', $user->phone_number)->first();

        if (!$userData) {
            return response()->json([
                'message' => 'User not found for this token.'
            ], 404);
        }

        return response()->json([
            'message' => 'User retrieved successfully.',
            'user' => [
                'id' => $userData->id,
                'first_name' => $userData->first_name ?? 'N/A',
                'last_name' => $userData->last_name ?? 'N/A',
                'email' => $userData->email ?? 'N/A',
                'phone' => $userData->phone,
                'gender' => $userData->gender ?? 'N/A',
                'province' => $userData->province ?? 'N/A',
                'city' => $userData->city ?? 'N/A',
                'area' => $userData->area ?? 'N/A',
                'created_at' => $userData->created_at,
                'updated_at' => $userData->updated_at,
            ]
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'حدث خطأ: ' . $e->getMessage()
        ], 500);
    }
}


    /**
     * Show the form for creating a new resource.
     */


    /**
     * Display the specified resource.
     */
   

    /**
     * Update the specified resource in storage.
     */
    public function updateuser(Request $request)
{
    try {
        // تحقق من أن المستخدم مسجل دخول باستخدام التوكن
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please log in first.'], 401);
        }

        // العثور على بيانات المستخدم في جدول `userauths`
        $userauth = UserAuth::where('phone', $user->phone_number)->first();
        if (!$userauth) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|unique:userauths,phone,' . $userauth->id,
            'email' => 'nullable|email|unique:userauths,email,' . $userauth->id,
            'gender' => 'nullable|in:male,female',
            'province' => 'nullable|string',
            'city' => 'nullable|string',
            'area' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // تحديث بيانات المستخدم إذا تم إرسالها
        $userauth->update([
            'first_name' => $request->input('first_name', $userauth->first_name),
            'last_name' => $request->input('last_name', $userauth->last_name),
            'phone' => $request->input('phone', $userauth->phone),
            'email' => $request->input('email', $userauth->email),
            'gender' => $request->input('gender', $userauth->gender),
            'province' => $request->input('province', $userauth->province),
            'city' => $request->input('city', $userauth->city),
            'area' => $request->input('area', $userauth->area),
        ]);

        return response()->json(['message' => 'User updated successfully', 'user' => $userauth], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'حدث خطأ: ' . $e->getMessage()], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(userauth $userauth)
    {
        //
    }
}
