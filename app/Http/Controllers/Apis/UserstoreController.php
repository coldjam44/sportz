<?php

namespace App\Http\Controllers\Apis;

use App\Models\Order;
use App\Models\userstore;
use App\Models\createstore;
use Illuminate\Http\Request;
use App\Http\Trait\GeneralTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class UserstoreController extends Controller
{
    use GeneralTrait;

    public function __construct()
    {
        $this->middleware('auth:api'); // التأكد من أن التوكن صالح
    }
public function index()
{

    $ratesSummary = userstore::select( \DB::raw('COUNT(rate) as total_rates'))

    ->get();
    // جلب جميع التسهيلات
    $userstores = userstore::all();

    // تحويل الصورة إلى رابط كامل
    $userstores = $userstores->map(function ($userstore) {
        // إذا كانت صورة موجودة، يتم تحويلها إلى رابط كامل
        if ($userstore->image) {
            $userstore->image = url('userstore/' . $userstore->image);
        }
        return $userstore;
    });

    // إرجاع البيانات مع الروابط
    return $this->returnData('userstore', $userstores);
}


    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            //'image' => 'required',
            'section_id' => 'required|exists:sections,id',
            'rate' => 'required',

        ],[
            'name_en.required' => 'English Name is required',
            'name_ar.required' => 'Arabic Name is required',
            //'image.required' => 'Image is required',
            'section_id.required' => 'Section ID is required',
            'rate.required' => 'The rate field is required.',

        ]);
        try{
        $userstore=new userstore();
        $userstore->name_en=$request->name_en;
        $userstore->name_ar=$request->name_ar;
        $userstore->section_id=$request->section_id;
        $userstore->rate=$request->rate;
        $image = $request->image;
        $imagename = time() . '.' . $image->getClientOriginalExtension();
        $request->image->move(public_path('userstore'), $imagename);
        $userstore->image=$imagename;
        $userstore->save();
        return $this->returnData('userstore',$userstore);
        }catch(\Exception $e){
            return $this->returnError('E001','error');
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'section_id' => 'required|exists:sections,id',
            'rate' => 'required',

            //'image' => 'nullable|image', // الصورة اختيارية
        ],[
            'name_en.required' => 'English Name is required',
            'name_ar.required' => 'Arabic Name is required',
            'section_id.required' => 'Section ID is required',
            'rate.required' => 'The rate field is required.',

        ]);

        try {
            $userstore = userstore::findOrFail($request->id);
            // تحديث الاسم باللغة الإنجليزية والعربية
            $userstore->name_en = $request->name_en;
            $userstore->name_ar = $request->name_ar;
            $userstore->section_id = $request->section_id;
            $userstore->rate = $request->rate;

            // إذا تم رفع صورة جديدة
            if ($request->hasFile('image')) {
                // إذا كانت هناك صورة جديدة، نحذف القديمة أولاً
                $oldImage = public_path('userstore/' . $userstore->image);
                if (File::exists($oldImage)) {
                    File::delete($oldImage);
                }

                // رفع الصورة الجديدة
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('userstore'), $imageName);

                // تحديث اسم الصورة في قاعدة البيانات
                $userstore->image = $imageName;
            }

            // حفظ التغييرات في قاعدة البيانات
            $userstore->save();

            // إعادة البيانات بعد التحديث
            return $this->returnData('userstore', $userstore);

        } catch (\Exception $e) {
            return $this->returnError('E001', 'Error: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            // جلب بيانات المتجر بناءً على المعرف
            $userstore = userstore::findOrFail($id);

            // حساب عدد التقييمات الإجمالية لهذا المتجر فقط
            $totalRates = userstore::where('id', $id)->count('rate');

            // حساب عدد الطلبات التي تم تقديمها لهذا المتجر
            $totalOrders = $userstore->orders()->count();  // هنا افترضنا أن علاقة الـ userstore و orders هي علاقة one-to-many

            // تحويل الصورة إلى رابط كامل
            if ($userstore->image) {
                $userstore->image = url('userstore/' . $userstore->image);
            }

            // إرجاع البيانات مع عدد التقييمات وعدد الطلبات
            return response()->json([
                'status' => true,
                'message' => 'تفاصيل المتجر',
                'total_rates' => $totalRates, // عدد التقييمات لهذا المتجر
                'total_orders' => $totalOrders, // عدد الطلبات لهذا المتجر
                'data' => $userstore
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }


   







    public function destroy($id)
    {
        $userstore=userstore::find($id);
        $userstore->delete();
        if(!$userstore){
            return $this->returnError('E001','data not found');
        }else{
            return $this->returnSuccessMessage('data deleted');
        }    }
}

