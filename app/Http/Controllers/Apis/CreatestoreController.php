<?php

namespace App\Http\Controllers\Apis;

use App\Models\Order;
use App\Models\section;
use App\Models\addproduct;

use App\Models\createstore;
use Illuminate\Http\Request;
use App\Http\Trait\GeneralTrait;
use App\Http\Controllers\Controller;
use App\Models\providerauth;

class CreatestoreController extends Controller
{
    use GeneralTrait;


    public function __construct()
    {
        $this->middleware('auth:api'); // التأكد من أن التوكن صالح
    }
 public function index()
{

   $provider = providerauth::where('phone_number', auth()->user()->phone_number)->first();

        if (!$provider) {
            return response()->json([
                'status' => false,
                'message' => 'المستخدم غير مصادق عليه',
            ], 401);
        }
   
    $createstores =  createstore::where('providerauth_id', $provider->id)
            ->with(['section', 'rates', 'storetype'])
            ->get()->map(function ($store) {
        return [
            'id' => $store->id,
            'name' => $store->name,
                      //'store_type' => $store->store_type,
                'store_type_id' => $store->store_type_id,
            'store_type_name_ar' => optional($store->storetype)->name_ar, // إصلاح الخطأ
            'store_type_name_en' => optional($store->storetype)->name_en, // إصلاح الخطأ
            'sections' => $store->section->map(function ($section) { // جلب الأقسام كمصفوفة
                return [
                    'section_id' => $section->id,
                    'section_name_en' => $section->name_en,
                    'section_name_ar' => $section->name_ar,
                ];
            }),
            'location' => $store->location,
              'image_url' => $store->image ? asset($store->image) : null,
            'tax_record_url' => $store->tax_record ? asset($store->tax_record) : null,
'average_rate' => $store->rates->avg('rate') ?? 0, // حساب متوسط التقييم
                         'is_hidden' => $store->is_hidden, // تضمين قيمة is_hidden كما هي

        ];
    });

    return response()->json([
        'status' => true,
        'message' => 'قائمة المتاجر',
        'data' => $createstores
    ]);
}



    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */
 public function store(Request $request)
{
 $userauth = providerauth::where('phone_number', auth()->user()->phone_number)->first();

    // التحقق من أن المستخدم موجود
    if (!$userauth) {
        return response()->json(['message' => 'User not found in userauths.'], 404);
    }    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'store_type_id' => 'nullable|exists:storetypes,id',
        'location' => 'required|string|max:255',
        'tax_record' => 'required|file|',
        'image' => 'nullable|file|',
        'section_id' => 'nullable|array',
        'section_id.*' => 'exists:sections,id'
            //  'is_hidden' => 'nullable|boolean', // إضافة `is_hidden`

    ]);

    $imagePath = null;
    $taxRecordPath = null;

    if ($request->hasFile('image')) {
        $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
        $request->file('image')->move(public_path('store_images'), $imageName);
        $imagePath = 'store_images/' . $imageName; 
    }

    if ($request->hasFile('tax_record')) {
        $taxRecordName = time() . '_' . $request->file('tax_record')->getClientOriginalName();
        $request->file('tax_record')->move(public_path('tax_records'), $taxRecordName);
        $taxRecordPath = 'tax_records/' . $taxRecordName;
    }

    $store = createstore::create([
        'name' => $request->name,
        'store_type_id' => $request->store_type_id,
        'location' => $request->location,
        'tax_record' => $taxRecordPath,
        'image' => $imagePath,
          'is_hidden' => $request->is_hidden ?? 0, // قيمة افتراضية 0 إذا لم يتم إرسالها
        'providerauth_id' => $userauth->id, // ✅ تسجيل id من userauths بدلاً من users

    ]);

    if ($request->has('section_id')) {
        $store->section()->attach($request->section_id);
    }

    return response()->json([
        'status' => true,
        'message' => 'تم إضافة المتجر بنجاح',
        'data' => [
            'id' => $store->id,
            'name' => $store->name,
            'store_type_id' => $store->store_type_id,
            'store_type_name_ar' => optional($store->storetype)->name_ar,
            'store_type_name_en' => optional($store->storetype)->name_en,
            'sections' => $store->section->map(function ($section) {
                return [
                    'section_id' => $section->id,
                    'section_name_en' => $section->name_en,
                    'section_name_ar' => $section->name_ar,
                ];
            }),
            'location' => $store->location,
            'image_url' => $store->image ? asset($store->image) : null,
            'tax_record_url' => $store->tax_record ? asset($store->tax_record) : null,
    'is_hidden' => $store->is_hidden, // إرجاع is_hidden

        ]
    ]);
}
    /**
     * Display the specified resource.
     */
   public function show($id)
{
    $createstore = createstore::with('section')->find($id);

    if (!$createstore) {
        return response()->json([
            'status' => false,
            'message' => 'المتجر غير موجود'
        ], 404);
    }

    $storeDetails = [
        'id' => $createstore->id,
        'name' => $createstore->name,
              'store_type' => $createstore->store_type,

        'sections' => $createstore->section->map(function ($section) {
            return [
                'section_id' => $section->id,
                'section_name_en' => $section->name_en,
                'section_name_ar' => $section->name_ar,
            ];
        }),
        'location' => $createstore->location,
        'image_url' => $createstore->image ? asset($createstore->image) : null,
        'tax_record_url' => $createstore->tax_record ? asset($createstore->tax_record) : null,
    ];

    return response()->json([
        'status' => true,
        'message' => 'تفاصيل المتجر',
        'data' => $storeDetails
    ]);
}

public function getOrderCountByStore($storeId)
{
    // استعلام للحصول على عدد الطلبات التي تحتوي على منتجات مرتبطة بالـ store_id المحدد
    $orderCount = Order::whereHas('orderItems.product', function ($query) use ($storeId) {
        $query->where('createstore_id', $storeId); // تأكد من أن المنتج مرتبط بالـ store_id
    })->count();

    return response()->json(['order_count' => $orderCount]);
}



    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $sections=section::all();

        $store = createstore::findOrFail($id);
        return view('pages.createstores.edit', compact('store','sections'));
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, $id)
{
  $provider = providerauth::where('phone_number', auth()->user()->phone_number)->first();
        $store = createstore::where('id', $id)->where('providerauth_id', $provider->id)->firstOrFail();
  
    $validated = $request->validate([
        'name' => 'sometimes|string|max:255',
        'store_type_id' => 'sometimes|exists:storetypes,id',
        'location' => 'sometimes|string|max:255',
        'tax_record' => 'nullable|file|',
        'image' => 'nullable|file|',
        'section_id' => 'sometimes|array',
        'section_id.*' => 'exists:sections,id',
    'is_hidden' => 'sometimes|boolean', // إضافة is_hidden

    ]);

    $data = $request->only(['name', 'location', 'store_type_id', 'is_hidden']);

    if ($request->hasFile('image')) {
        if ($store->image && file_exists(public_path($store->image))) {
            unlink(public_path($store->image));
        }

        $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
        $request->file('image')->move(public_path('store_images'), $imageName);
        $data['image'] = 'store_images/' . $imageName;
    }

    if ($request->hasFile('tax_record')) {
        if ($store->tax_record && file_exists(public_path($store->tax_record))) {
            unlink(public_path($store->tax_record));
        }

        $taxRecordName = time() . '_' . $request->file('tax_record')->getClientOriginalName();
        $request->file('tax_record')->move(public_path('tax_records'), $taxRecordName);
        $data['tax_record'] = 'tax_records/' . $taxRecordName;
    }

    $store->update($data);

    if ($request->has('section_id')) {
        $store->section()->sync($request->section_id);
    }

    return response()->json([
        'status' => true,
        'message' => 'تم تحديث المتجر بنجاح',
        'data' => [
            'id' => $store->id,
            'name' => $store->name,
            'store_type_id' => $store->store_type_id,
            'store_type_name_ar' => optional($store->storetype)->name_ar,
            'store_type_name_en' => optional($store->storetype)->name_en,
            'sections' => $store->section->map(function ($section) {
                return [
                    'section_id' => $section->id,
                    'section_name_en' => $section->name_en,
                    'section_name_ar' => $section->name_ar,
                ];
            }),
            'location' => $store->location,
            'image_url' => $store->image ? asset($store->image) : null,
            'tax_record_url' => $store->tax_record ? asset($store->tax_record) : null,
    'is_hidden' => $store->is_hidden, // إرجاع is_hidden

        ]
    ], 200);
}

    /**
     * Remove the specified resource from storage.
     */
   public function destroy($id)
{
             $provider = providerauth::where('phone_number', auth()->user()->phone_number)->first();

    $createstore = createstore::where('id', $id)->where('providerauth_id', $provider->id)->firstOrFail();

    // حذف ملف الضريبة إذا كان موجودًا
    if ($createstore->tax_record && file_exists(public_path($createstore->tax_record))) {
        unlink(public_path($createstore->tax_record));
    }

    // حذف صورة المتجر إذا كانت موجودة
    if ($createstore->image && file_exists(public_path($createstore->image))) {
        unlink(public_path($createstore->image));
    }

    // حذف بيانات المتجر من قاعدة البيانات
    $createstore->delete();

    return response()->json([
        'status' => true,
        'message' => 'تم حذف المتجر بنجاح'
    ]);
}
  
  
  
public function getAllStoresForUser(Request $request)
{
    $user = auth()->user();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'يجب تسجيل الدخول أولاً',
        ], 401);
    }

    $perPage = $request->get('per_page', 10);
    $minRate = $request->input('min_rate');
    $maxRate = $request->input('max_rate');
    $search = $request->input('search');

    $storesQuery = createstore::with(['section.addProducts', 'rates', 'storetype'])
        ->where('is_hidden', 0)
        ->when($request->store_type_id, function ($query) use ($request) {
            $query->where('store_type_id', $request->store_type_id);
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('location', 'LIKE', "%$search%")
                  ->orWhereHas('storetype', function ($q2) use ($search) {
                      $q2->where('name_ar', 'LIKE', "%$search%")
                         ->orWhere('name_en', 'LIKE', "%$search%");
                  });
            });
        });

    // لو المستخدم دخل min_rate و max_rate نفلتر على أساسهم
    if ($minRate !== null && $maxRate !== null) {
        $storesQuery->whereIn('id', function ($query) use ($minRate, $maxRate) {
            $query->select('store_id')
                ->from('providerrates')
                ->groupBy('store_id')
                ->havingRaw('AVG(rate) BETWEEN ? AND ?', [$minRate, $maxRate]);
        });
    }
    
    $storesPaginator = $storesQuery->paginate($perPage);

    $stores = $storesPaginator->getCollection()->map(function ($store) {
        return [
            'id' => $store->id,
            'name' => $store->name,
            'store_type_id' => $store->store_type_id,
            'store_type_name_ar' => optional($store->storetype)->name_ar,
            'store_type_name_en' => optional($store->storetype)->name_en,
            'location' => $store->location,
            'image_url' => $store->image ? asset($store->image) : null,
            'tax_record_url' => $store->tax_record ? asset($store->tax_record) : null,
            'average_rate' => round($store->rates->avg('rate'), 2) ?? 0,
            'ratings_count' => $store->rates->count(),
            'is_hidden' => $store->is_hidden,
            'sections' => $store->section->map(function ($section) {
                return [
                    'section_id' => $section->id,
                    'section_name_en' => $section->name_en,
                    'section_name_ar' => $section->name_ar,
                ];
            }),
        ];
    });

    return response()->json([
        'status' => true,
        'message' => 'قائمة جميع المتاجر المتاحة',
        'data' => $stores,
        'total_stores' => $storesPaginator->total(),
        'total_pages' => $storesPaginator->lastPage(),
        'current_page' => $storesPaginator->currentPage(),
    ]);
}


  
public function getStoreSectionsAndProducts(Request $request)
{
    $user = auth()->user();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'يجب تسجيل الدخول أولاً',
        ], 401);
    }

    $storeId = $request->get('store_id');
    $storeTypeId = $request->get('store_type_id');
    $perPage = $request->get('per_page', 10);
    $sectionId = $request->get('section_id');
    $search = $request->get('search');

    // الحصول على userauth
    $userauth = \App\Models\userauth::where('phone', $user->phone_number)->first();
    $cartProductIds = [];
    $favouriteProductIds = [];
    // $cartItems = [];
	$cartItems = collect(); // ✅ كده تقدر تستخدم has() بعدين بدون مشاكل

    if ($userauth) {
        $cartItems = \App\Models\cart::where('userauth_id', $userauth->id)
            ->get()
            ->keyBy('product_id');

        $favouriteProductIds = \App\Models\Favourite::where('userauth_id', $userauth->id)
            ->pluck('product_id')
            ->toArray();
    }

    // لو مش مدخل store_id رجع كل المنتجات من المتاجر اللي مش مخفية وممكن تحدد store_type_id
    if (!$storeId) {
        $query = \App\Models\addproduct::with(['section.stores' => function ($q) {
            $q->where('is_hidden', 0);
        }, 'section']);

        if ($storeTypeId) {
            $query->whereHas('section.stores', function ($q) use ($storeTypeId) {
                $q->where('store_type_id', $storeTypeId);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'LIKE', "%$search%")
                  ->orWhere('name_en', 'LIKE', "%$search%")
                  ->orWhere('description_ar', 'LIKE', "%$search%")
                  ->orWhere('description_en', 'LIKE', "%$search%");
            });
        }

        $products = $query->paginate($perPage);
    } else {
        if (!is_numeric($storeId)) {
            return response()->json([
                'status' => false,
                'message' => 'يجب إرسال store_id صالح',
            ], 400);
        }

        $store = createstore::with('section')->find($storeId);

        if (!$store || $store->is_hidden) {
            return response()->json([
                'status' => false,
                'message' => 'المتجر غير موجود أو مخفي',
            ], 404);
        }

        if ($storeTypeId && $store->store_type_id != $storeTypeId) {
            return response()->json([
                'status' => false,
                'message' => 'هذا المتجر لا يتبع نوع المتجر المحدد',
            ], 404);
        }

        if ($sectionId) {
            $section = $store->section()->where('sections.id', $sectionId)->first();

            if (!$section) {
                return response()->json([
                    'status' => false,
                    'message' => 'هذا القسم غير تابع لهذا المتجر',
                ], 404);
            }

            $products = $section->addProducts()
                ->with('section')
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('name_ar', 'LIKE', "%$search%")
                              ->orWhere('name_en', 'LIKE', "%$search%")
                              ->orWhere('description_ar', 'LIKE', "%$search%")
                              ->orWhere('description_en', 'LIKE', "%$search%");
                    });
                })
                ->paginate($perPage);
        } else {
            $sectionIds = $store->section->pluck('id');

            $products = \App\Models\addproduct::whereIn('section_id', $sectionIds)
                ->with('section')
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('name_ar', 'LIKE', "%$search%")
                              ->orWhere('name_en', 'LIKE', "%$search%")
                              ->orWhere('description_ar', 'LIKE', "%$search%")
                              ->orWhere('description_en', 'LIKE', "%$search%");
                    });
                })
                ->paginate($perPage);
        }
    }

    // تجهيز البيانات للإرجاع
$transformed = $products->getCollection()->map(function ($product) use ($cartItems, $favouriteProductIds) {
    $store = optional($product->section)->stores->first();

    // حساب التقييمات
    $storeRatings = $store ? $store->rates : collect();
    $totalRatings = $storeRatings->sum('rate');
    $ratingsCount = $storeRatings->count();
    $averageRate = $ratingsCount > 0 ? round($totalRatings / $ratingsCount, 1) : 0;

    // فحص صلاحية الخصم
    $now = now(); // Carbon instance
    $startDate = $product->start_time ? \Carbon\Carbon::parse($product->start_time) : null;
    $endDate = $product->end_time ? \Carbon\Carbon::parse($product->end_time) : null;

    $validDiscount = null;
    if ($product->discount && $startDate && $endDate) {
        if ($now->between($startDate, $endDate)) {
            $validDiscount = $product->discount; // الخصم ساري
        }
    }

    return [
        'id' => $product->id,
        'name_ar' => $product->name_ar,
        'name_en' => $product->name_en,
        'description_ar' => $product->description_ar,
        'description_en' => $product->description_en,
        'price' => $product->price,
        'discount' => $validDiscount, // فقط إذا كان ساريًا
        'final_price' => $validDiscount ? $product->price - $validDiscount : $product->price,
        'image_urls' => collect(json_decode($product->image, true))->map(fn($img) => asset('addproducts/' . $img))->toArray(),
        'section_name_ar' => optional($product->section)->name_ar,
        'section_name_en' => optional($product->section)->name_en,
        'in_cart' => $cartItems->has($product->id),
        'quantity' => $cartItems->has($product->id) ? $cartItems[$product->id]->quantity : 0,
        'in_favourite' => in_array($product->id, $favouriteProductIds),
        'store' => $store ? [
            'name' => $store->name,
            'location' => $store->location,
            'image' => asset($store->image),
            'average_rate' => $averageRate,
            'ratings_count' => $ratingsCount,
        ] : null,
    ];
});

    return response()->json([
        'status' => true,
        'message' => 'قائمة المنتجات',
        'data' => $transformed,
        'pagination' => [
            'total_products' => $products->total(),
            'per_page' => $products->perPage(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
        ]
    ]);
}


public function getStoreDetails(Request $request, $store_id)
{
    // التحقق من أن المستخدم مسجل الدخول
    $user = auth()->user();
    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'يجب تسجيل الدخول أولاً',
        ], 401);
    }

    // الحصول على `section_id` من الطلب إذا كان موجودًا
    $section_id = $request->input('section_id');

    // البحث عن المتجر مع تحميل العلاقات المطلوبة
    try {
        $store = createstore::with([
            'storetype', 
            'rates', 
            'section.addProducts'
        ])
        ->where('id', $store_id)
        ->where('is_hidden', 0)
        ->firstOrFail();
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'status' => false,
            'message' => 'المتجر غير موجود أو مخفي',
        ], 404);
    }

    // تصفية الأقسام بناءً على `section_id` إذا تم تمريره
    $sections = $store->section;
    if ($section_id) {
        $sections = $sections->where('id', $section_id);
    }

    return response()->json([
        'status' => true,
        'message' => 'تفاصيل المتجر',
        'data' => [
            'id' => $store->id,
            'name' => $store->name,
            'store_type_id' => $store->store_type_id,
            'store_type_name_ar' => optional($store->storetype)->name_ar,
            'store_type_name_en' => optional($store->storetype)->name_en,
            'location' => $store->location,
            'image_url' => $store->image ? asset($store->image) : null,
            'tax_record_url' => $store->tax_record ? asset($store->tax_record) : null,
            'average_rate' => $store->rates->avg('rate') ?? 0,

            // الأقسام مع المنتجات الخاصة بها بعد الفلترة
            'sections' => $sections->map(function ($section) {
                return [
                    'section_id' => $section->id,
                    'section_name_en' => $section->name_en,
                    'section_name_ar' => $section->name_ar,
                    'products' => $section->addProducts ? $section->addProducts->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name_ar' => $product->name_ar,
                            'name_en' => $product->name_en,
                            'description_ar' => $product->description_ar,
                            'description_en' => $product->description_en,
                            'price' => $product->price,
                            'image_urls' => collect(json_decode($product->image, true))->map(function ($img) {
                                return asset($img);
                            })->toArray(),
                        ];
                    }) : [], // إذا لم تكن هناك منتجات، أعد مصفوفة فارغة
                ];
            }),
        ]
    ]);
}
  
  
public function getProductDetails(Request $request, $product_id)
{
    // التحقق من أن المستخدم مسجل الدخول عبر التوكن
    $user = auth()->user();
    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'يجب تسجيل الدخول أولاً',
        ], 401);
    }

    // البحث عن المنتج والتحقق من ارتباطه بمتجر
    $product = addproduct::with(['store' => function ($query) {
        $query->with('rates'); // تحميل التقييمات مع المتجر
    }])->where('id', $product_id)->first();

    if (!$product) {
        return response()->json([
            'status' => false,
            'message' => 'المنتج غير موجود',
        ], 404);
    }

    // جلب المتجر باستخدام createstore_id
    $store = createstore::where('id', $product->createstore_id)->with('rates')->first();

    if (!$store) {
        return response()->json([
            'status' => false,
            'message' => 'المتجر المرتبط بالمنتج غير موجود',
        ], 404);
    }

    return response()->json([
        'status' => true,
        'message' => 'تفاصيل المنتج',
        'data' => [
            'store_id' => $store->id,
            'store_name' => $store->name,
            'store_image_url' => $store->image ? asset($store->image) : null,
            'store_average_rate' => $store->rates->avg('rate') ?? 0,
            'product_id' => $product->id,
            'product_name_ar' => $product->name_ar,
            'product_name_en' => $product->name_en,
            'product_description_ar' => $product->description_ar,
            'product_description_en' => $product->description_en,
            'product_price' => $product->price,
            'product_image_urls' => collect(json_decode($product->image, true))->map(function ($img) {
                return asset($img);
            })->toArray(),
        ]
    ]);
}
  
  public function toggleFavourite(Request $request)
{
    $user = auth()->user();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'يجب تسجيل الدخول أولاً',
        ], 401);
    }

    $productId = $request->get('product_id');

    if (!$productId || !is_numeric($productId)) {
        return response()->json([
            'status' => false,
            'message' => 'يجب إرسال product_id صالح',
        ], 400);
    }

    $userauth = \App\Models\userauth::where('phone', $user->phone_number)->first();

    if (!$userauth) {
        return response()->json([
            'status' => false,
            'message' => 'المستخدم غير موجود',
        ], 404);
    }

    $favourite = \App\Models\Favourite::where('userauth_id', $userauth->id)
                    ->where('product_id', $productId)
                    ->first();

    if ($favourite) {
        $favourite->delete();
        return response()->json([
            'status' => true,
            'message' => 'تم إزالة المنتج من المفضلة',
            'favourited' => false,
        ]);
    } else {
        \App\Models\Favourite::create([
            'userauth_id' => $userauth->id,
            'product_id' => $productId,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم إضافة المنتج إلى المفضلة',
            'favourited' => true,
        ]);
    }
}
public function getFavourites(Request $request)
{
    $user = auth()->user();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'يجب تسجيل الدخول أولاً',
        ], 401);
    }

    $userauth = \App\Models\userauth::where('phone', $user->phone_number)->first();

    if (!$userauth) {
        return response()->json([
            'status' => false,
            'message' => 'المستخدم غير موجود',
        ], 404);
    }

    $perPage = $request->get('per_page', 10);

    // المفضلات
    $favourites = \App\Models\Favourite::where('userauth_id', $userauth->id)
        ->with('product.section')
        ->paginate($perPage);

    // المنتجات داخل السلة
    $cartItems = \App\Models\cart::where('userauth_id', $userauth->id)
        ->get()
        ->keyBy('product_id');

    // IDs المنتجات المفضلة
    $favouriteProductIds = \App\Models\Favourite::where('userauth_id', $userauth->id)
        ->pluck('product_id')
        ->toArray();

    $transformed = $favourites->getCollection()->map(function ($fav) use ($cartItems, $favouriteProductIds) {
        $product = $fav->product;

        return [
            'id' => $product->id,
            'name_ar' => $product->name_ar,
            'name_en' => $product->name_en,
            'description_ar' => $product->description_ar,
            'description_en' => $product->description_en,
            'price' => $product->price,
            'discount' => $product->discount,
            'image_urls' => collect(json_decode($product->image, true))->map(fn($img) => asset($img))->toArray(),
            'section_name_ar' => optional($product->section)->name_ar,
            'section_name_en' => optional($product->section)->name_en,
            'in_cart' => $cartItems->has($product->id),
            'quantity' => $cartItems->has($product->id) ? $cartItems[$product->id]->quantity : 0,
            'in_favourite' => in_array($product->id, $favouriteProductIds),
        ];
    });

    // استبدال البيانات الأصلية بالبيانات المعدلة
    $favourites->setCollection($transformed);

    return response()->json([
        'status' => true,
        'message' => 'قائمة المنتجات المفضلة',
        'data' => $favourites->items(),
        'pagination' => [
            'total' => $favourites->total(),
            'per_page' => $favourites->perPage(),
            'current_page' => $favourites->currentPage(),
            'last_page' => $favourites->lastPage(),
        ]
    ]);
}


}