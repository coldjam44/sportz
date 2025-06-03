<?php

namespace App\Http\Controllers\Apis;

use App\Models\OrderItem;
use App\Models\section;
use App\Models\providerauth;
use App\Models\createstore;

use App\Models\addproduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AddproductController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api'); // التأكد من أن التوكن صالح
    }
    public function index(Request $request)
    {
        $provider = Providerauth::where('phone_number', auth()->user()->phone_number)->first();

        if (!$provider) {
            return response()->json([
                'status' => false,
                'message' => 'المستخدم غير مصرح له بالوصول إلى المنتجات',
            ], 403);
        }

        $sectionId = $request->input('section_id');
        $storeTypeId = $request->input('store_type_id');

        $query = addproduct::select([
            'addproducts.id',
            'addproducts.is_hidden',
            'addproducts.name_en',
            'addproducts.name_ar',
            'addproducts.description_en',
            'addproducts.description_ar',
            'addproducts.price',
            'addproducts.discount',
            'addproducts.section_id',
            'addproducts.start_time',
            'addproducts.end_time',
            'addproducts.image',
            'sections.name_en as section_name_en',
            'sections.name_ar as section_name_ar'
        ])
            ->leftJoin('sections', 'sections.id', '=', 'addproducts.section_id')
            ->where('addproducts.providerauth_id', $provider->id) // ✅ جلب المنتجات الخاصة بالمزوّد فقط
            ->withCount('orderItems');

        if ($sectionId) {
            $query->where('addproducts.section_id', $sectionId);
        }

        if ($storeTypeId) {
            $query->where('store_type_id', $storeTypeId);
        }

        $products = $query->paginate(10);

        $products->getCollection()->transform(function ($product) {
            $product->image = $this->getImageLinks($product->image);
            $product->orders_count = $product->order_items_count;
            unset($product->order_items_count);
            return $product;
        });

        return response()->json([
            'status' => true,
            'message' => __('messages.product_list'),
            'data' => [
                'current_page' => $products->currentPage(),
                'products' => $products->items(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }


    // تحويل أسماء الصور إلى روابط
    private function getImageLinks($imageJson)
    {
        $images = json_decode($imageJson, true);
        $imageLinks = [];

        foreach ($images as $image) {
            $imageLinks[] = asset('addproducts/' . $image); // إنشاء الرابط الصحيح
        }

        return $imageLinks;
    }

    // عرض منتج مفرد
    // عرض منتج مفرد مع عدد الطلبات
    public function show($id)
    {
        // جلب المنتج مع بيانات القسم
        $product = AddProduct::select([
            'addproducts.id',
            'addproducts.name_en',
            'addproducts.name_ar',
            'addproducts.description_en',
            'addproducts.description_ar',
            'addproducts.price',
            'addproducts.discount',
            'addproducts.section_id',
            'addproducts.start_time',
            'addproducts.end_time',
            'addproducts.image',
            'sections.name_en as section_name_en',
            'sections.name_ar as section_name_ar'
        ])
            ->leftJoin('sections', 'sections.id', '=', 'addproducts.section_id')
            ->where('addproducts.id', $id)
            ->first();

        // التحقق من وجود المنتج
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'المنتج غير موجود'
            ], 404);
        }

        // تحويل أسماء الصور إلى روابط
        $product->image = $this->getImageLinks($product->image);

        // حساب عدد الطلبات التي تحتوي على المنتج
        $orderCount = OrderItem::where('product_id', $id)->count();

        return response()->json([
            'status' => true,
            'message' => 'تفاصيل المنتج',
            'data' => [
                'id' => $product->id,
                'name_en' => $product->name_en,
                'name_ar' => $product->name_ar,
                'description_en' => $product->description_en,
                'description_ar' => $product->description_ar,
                'price' => $product->price,
                'discount' => $product->discount,
                'section_id' => $product->section_id,
                'section_name_en' => $product->section_name_en,
                'section_name_ar' => $product->section_name_ar,
                'start_time' => $product->start_time,
                'end_time' => $product->end_time,
                'image' => $product->image,

            ],
            'order_count' => $orderCount
        ]);
    }



    // تخزين منتج جديد
    public function store(Request $request)
    {
        $provider = Providerauth::where('phone_number', auth()->user()->phone_number)->first();

        if (!$provider) {
            return response()->json([
                'status' => false,
                'message' => 'المستخدم غير مصرح له بالوصول إلى المنتجات',
            ], 403);
        }

        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'image' => 'required|array',
            'price' => 'required|numeric',
            'discount' => 'nullable|string',
            'section_id' => 'nullable|exists:sections,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'is_hidden' => 'nullable|boolean'
        ]);

        try {
            $addproducts = [];
            foreach ($request->file('image') as $file) {
                $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('addproducts'), $imageName);
                $addproducts[] = $imageName;
            }

            $product = addproduct::create([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'description_ar' => $request->description_ar,
                'description_en' => $request->description_en,
                'image' => json_encode($addproducts),
                'price' => $request->price,
                'discount' => $request->discount,
                'section_id' => $request->section_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'is_hidden' => $request->is_hidden,
                'providerauth_id' => $provider->id // ✅ ربط المنتج بالمزوّد الحالي
            ]);

            $product->load('section')->loadCount('orderItems');
            $product->image = $this->getImageLinks($product->image);

            return response()->json([
                'status' => true,
                'message' => 'تم إضافة المنتج بنجاح',
                'data' => [
                    'id' => $product->id,
                    'name_ar' => $product->name_ar,
                    'name_en' => $product->name_en,
                    'description_ar' => $product->description_ar,
                    'description_en' => $product->description_en,
                    'image' => $product->image,
                    'price' => $product->price,
                    'discount' => $product->discount,
                    'section_id' => $product->section_id,
                    'start_time' => $product->start_time,
                    'end_time' => $product->end_time,
                    'is_hidden' => $product->is_hidden,
                    'section_name_en' => $product->section?->name_en,
                    'section_name_ar' => $product->section?->name_ar,
                    'orders_count' => $product->order_items_count,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء إضافة المنتج',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function mostOrderedProducts(Request $request)
    {
        // التحقق من أن المستخدم مسجّل كمزوّد
        $provider = Providerauth::where('phone_number', auth()->user()->phone_number)->first();
        if (!$provider) {
            return response()->json([
                'status' => false,
                'message' => 'المستخدم غير مصرح له بالوصول إلى البيانات',
            ], 403);
        }

        // التحقق من وجود store_id في الطلب
        $storeId = $request->input('store_id');
        if (!$storeId) {
            return response()->json([
                'status' => false,
                'message' => 'يجب تحديد معرف المتجر store_id',
            ], 400);
        }

        // جلب المتجر والتحقق من ملكيته
        $store = CreateStore::where('id', $storeId)
            ->where('providerauth_id', $provider->id)
            ->with('section') // جلب الأقسام المرتبطة بهذا المتجر
            ->first();

        if (!$store) {
            return response()->json([
                'status' => false,
                'message' => 'المتجر غير موجود أو لا ينتمي لهذا المزود',
            ], 404);
        }

        // **1️⃣ تحديد الأقسام المرتبطة بالمتجر**
        $sectionIds = $store->section->pluck('id'); // استخراج جميع section_id المرتبطة بالمتجر

        if ($sectionIds->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'لا توجد أقسام مرتبطة بهذا المتجر.',
                'data' => []
            ]);
        }

        // **2️⃣ جلب المنتجات الأكثر طلبًا داخل هذه الأقسام**
        $products = AddProduct::select([
            'addproducts.id',
            'addproducts.name_en',
            'addproducts.name_ar',
            'addproducts.description_en',
            'addproducts.description_ar',
            'addproducts.price',
            'addproducts.discount',
            'addproducts.section_id',
            'addproducts.start_time',
            'addproducts.end_time',
            'addproducts.image',
            'sections.name_en as section_name_en',
            'sections.name_ar as section_name_ar'
        ])
            ->leftJoin('sections', 'sections.id', '=', 'addproducts.section_id')
            ->whereIn('addproducts.section_id', $sectionIds) // ✅ جلب المنتجات المرتبطة بالأقسام المحددة
            ->withCount('orderItems') // ✅ حساب عدد الطلبات لكل منتج
            ->orderByDesc('order_items_count') // ✅ ترتيب المنتجات بناءً على عدد الطلبات
            ->paginate(10); // ✅ دعم التصفح (pagination)

        // **3️⃣ تعديل البيانات قبل الإرسال**
        $products->getCollection()->transform(function ($product) {
            $product->image = $this->getImageLinks($product->image);
            // حساب عدد الطلبات الكلي
            $ordersCount = $product->order_items_count;
            // جلب عدد الطلبات الملغية
            $canceledCount = \App\Models\OrderItem::where('product_id', $product->id)
                ->whereHas('order', function ($q) {
                    $q->where('status', 'canceled');
                })->count();
            // طرح الملغية من الكلي
            $product->orders_count = $ordersCount - $canceledCount;
            $product->canceled_subtracted = $canceledCount;
            unset($product->order_items_count);
            return $product;
        });

        return response()->json([
            'status' => true,
            'message' => 'أكثر المنتجات طلبًا في المتجر ' . $store->name,
            'data' => [
                'current_page' => $products->currentPage(),
                'products' => $products->items(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    // تحديث المنتج
    public function update(Request $request, $id)
    {
        $provider = Providerauth::where('phone_number', auth()->user()->phone_number)->first();

        $product = AddProduct::where('id', $id)
            ->where('providerauth_id', $provider->id) // ✅ السماح فقط بتحديث منتجات المزوّد
            ->first();

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'المنتج غير موجود أو ليس لديك الصلاحية لتعديله',
            ], 403);
        }

        $validated = $request->validate([
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'price' => 'nullable|numeric',
            'discount' => 'nullable|string',
            'section_id' => 'nullable|exists:sections,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'is_hidden' => 'nullable|boolean'
        ]);

        $addproducts = json_decode($product->image, true) ?? [];

        if ($request->hasFile('image')) {
            foreach ($addproducts as $image) {
                $imagePath = public_path('addproducts/' . $image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $addproducts = [];

            foreach ($request->file('image') as $file) {
                $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('addproducts'), $imageName);
                $addproducts[] = $imageName;
            }
        }

        $start_time = $request->has('start_time') && $request->start_time !== "null" ? $request->start_time : null;
        $end_time = $request->has('end_time') && $request->end_time !== "null" ? $request->end_time : null;

        $product->update([
            'name_ar' => $request->filled('name_ar') ? $request->name_ar : $product->name_ar,
            'name_en' => $request->filled('name_en') ? $request->name_en : $product->name_en,
            'description_ar' => $request->filled('description_ar') ? $request->description_ar : $product->description_ar,
            'description_en' => $request->filled('description_en') ? $request->description_en : $product->description_en,
            'image' => json_encode($addproducts),
            'price' => $request->filled('price') ? $request->price : $product->price,
            'discount' => isset($request->discount) ? $request->discount : $product->discount,
            'section_id' => $request->filled('section_id') ? $request->section_id : $product->section_id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'is_hidden' => $request->filled('is_hidden') ? $request->is_hidden : $product->is_hidden,
        ]);

        $product->loadCount('orderItems');
        $product->image = $this->getImageLinks($product->image);

        $sectionNameAr = null;
        $sectionNameEn = null;
        if ($product->section_id) {
            $section = Section::find($product->section_id);
            $sectionNameAr = $section ? $section->name_ar : null;
            $sectionNameEn = $section ? $section->name_en : null;
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث المنتج بنجاح',
            'data' => [
                'id' => $product->id,
                'name_ar' => $product->name_ar,
                'name_en' => $product->name_en,
                'description_ar' => $product->description_ar,
                'description_en' => $product->description_en,
                'price' => $product->price,
                'discount' => $product->discount,
                'section_id' => $product->section_id,
                'section_name_ar' => $sectionNameAr,
                'section_name_en' => $sectionNameEn,
                'start_time' => $product->start_time,
                'end_time' => $product->end_time,
                'image' => $product->image,
                'is_hidden' => $product->is_hidden,
                'orders_count' => $product->order_items_count,
            ]
        ]);
    }

    // حذف المنتج
    public function destroy($id)
    {
        $provider = Providerauth::where('phone_number', auth()->user()->phone_number)->first();

        $product = AddProduct::where('id', $id)
            ->where('providerauth_id', $provider->id) // ✅ التأكد من أن المنتج يخص المزوّد الحالي
            ->first();

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'المنتج غير موجود أو ليس لديك الصلاحية لحذفه',
            ], 403);
        }

        $images = json_decode($product->image);
        if (is_array($images)) {
            foreach ($images as $image) {
                $imagePath = public_path('addproducts/' . $image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف المنتج بنجاح',
        ]);
    }
}
