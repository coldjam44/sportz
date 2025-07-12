<?php

namespace App\Http\Controllers;

use App\Models\providerauth;
use App\Models\User;
use App\Models\createstore;
use App\Models\CreateStadium;
use App\Models\addproduct;
use App\Models\section;

use Tymon\JWTAuth\Facades\JWTAuth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Notification; // تأكد أنك مستورد الموديل
class ProviderauthController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized. Please log in first.'
            ], 401);
        }

        // جلب بيانات مقدم الخدمة بناءً على رقم الهاتف الحالي
        $provider = Providerauth::where('phone_number', $user->phone_number)->first();

        if (!$provider) {
            return response()->json([
                'message' => 'Provider not found for this user.'
            ], 404);
        }

        return response()->json([
            'message' => 'Provider retrieved successfully.',
            'provider' => [
                'id' => $provider->id,
                'first_name' => $provider->first_name ?? 'N/A',
                'last_name' => $provider->last_name ?? 'N/A',
                'email' => $provider->email ?? 'N/A',
                'phone_number' => $provider->phone_number,
                'created_at' => $provider->created_at,
                'updated_at' => $provider->updated_at,
            ]
        ], 200);
    }





    public function signup(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please log in first.'], 401);
        }

        // التحقق من وجود البريد الإلكتروني أو رقم الهاتف مسبقًا
        //$existingProvider = Providerauth::where('email', $request->email)
        // ->orWhere('phone_number', $request->phone_number)
        //->first();

        // if ($existingProvider) {
        //   return response()->json(['message' => 'This email or phone number is already registered.'], 409);
        // }

        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|unique:providerauths,phone_number',
            'email' => 'nullable|email|unique:providerauths,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        // إنشاء الحساب
        $provider = Providerauth::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $user->phone_number, // استخدام رقم الهاتف من المستخدم المسجل
        ]);

        Notification::create([
            'user_id' => $provider->id, // أو حسب الحقل المناسب
            'title' => 'أهلاً بك!',
            'message_ar' => 'تم تسجيلك كبروفايدر بنجاح.',
            'message_en' => 'You have successfully signed up as a provider.',
            'status' => 'new',
            'type' => 'provider_signup',
        ]);

        return response()->json([
            'message' => 'Provider registered successfully',
            'provider' => $provider
        ], 201);
    }






    public function update(Request $request, $id)
    {
        $provider = Providerauth::find($id);

        if (!$provider) {
            return response()->json(['message' => 'Provider not found.'], 404);
        }

        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:providerauths,email,' . $id,
            'phone_number' => 'nullable|string|unique:providerauths,phone_number,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        // حفظ رقم الهاتف القديم للمقارنة
        $oldPhoneNumber = $provider->phone_number;

        // تحديث البيانات
        $provider->update([
            'first_name' => $request->first_name ?? $provider->first_name,
            'last_name' => $request->last_name ?? $provider->last_name,
            'email' => $request->email ?? $provider->email,
            'phone_number' => $request->phone_number ?? $provider->phone_number,
        ]);

        // إذا تم تغيير رقم الهاتف، نتحقق مما إذا كان الرقم الجديد مسجلًا، وإذا لم يكن، نقوم بإنشاء سجل جديد
        if ($request->phone_number && $request->phone_number !== $oldPhoneNumber) {
            $existingProvider = Providerauth::where('phone_number', $request->phone_number)->first();
            if (!$existingProvider) {
                $newProvider = Providerauth::create([
                    'first_name' => $provider->first_name,
                    'last_name' => $provider->last_name,
                    'email' => $provider->email,
                    'phone_number' => $request->phone_number,
                ]);
            }
        }

        Notification::create([
            'user_id' => $provider->id,
            'title' => 'تم تحديث بياناتك',
            'message_ar' => 'تم تعديل ملفك الشخصي بنجاح.',
            'message_en' => 'Your profile has been updated successfully.',
            'status' => 'new',
            'type' => 'provider_profile_update',
        ]);

        return response()->json([
            'message' => 'Provider updated successfully.',
            'provider' => $provider,
        ], 200);
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

    // حذف الحساب
    public function deleteAccount(Request $request)
    {
        $provider = Providerauth::where('phone_number', auth()->user()->phone_number)->first();

        if (!$provider) {
            return response()->json([
                'error' => 'User not found.'
            ], 404);
        }

        // حذف الملاعب التي أضافها
        $stadiums = CreateStadium::where('providerauth_id', $provider->id)->get();
        foreach ($stadiums as $stadium) {
            $imagePath = public_path('stadium_images/' . $stadium->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $stadium->delete();
        }

        // حذف الأقسام التي أضافها
        Section::where('providerauth_id', $provider->id)->delete();

        // حذف المتاجر التي أضافها
        $stores = CreateStore::where('providerauth_id', $provider->id)->get();
        foreach ($stores as $store) {
            $imagePath = public_path('store_images/' . $store->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $store->delete();
        }

        // حذف المنتجات التي أضافها
        $products = AddProduct::where('providerauth_id', $provider->id)->get();
        foreach ($products as $product) {
            $images = json_decode($product->image, true);
            if (is_array($images)) {
                foreach ($images as $image) {
                    $imagePath = public_path('addproducts/' . $image);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }
            $product->delete();
        }

        // حذف الحساب نفسه
        $provider->delete();

Notification::create([
    'user_id' => $provider->id,
    'title' => 'حذف الحساب',
    'message_ar' => 'تم حذف حسابك بنجاح. نأسف لرحيلك!',
    'message_en' => 'Your account has been deleted successfully. We’re sorry to see you go!',
    'status' => 'new',
    'type' => 'provider_account_delete',
]);


        // إبطال التوكن بعد حذف الحساب
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'تم حذف الحساب وجميع البيانات المرتبطة به بنجاح.'
        ], 200);
    }




    /**
     * Display a listing of the resource.
     */


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(providerauth $providerauth)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(providerauth $providerauth)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(providerauth $providerauth)
    {
        //
    }
}
