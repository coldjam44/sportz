<?php

namespace App\Http\Controllers\Apis;

use App\Models\Order;
use App\Models\userauth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\section;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderController extends Controller
{
    public function index(Request $request)
    {

        // تحقق من وجود status
        if (!$request->has('status')) {
            return response()->json([
                'status' => false,
                'message' => 'حقل status مطلوب'
            ], 400); // 400 = Bad Request
        }



        $createstoreId = $request->query('createstore_id');
        $status = $request->query('status');
        $perPage = $request->query('per_page', 10);
        $page = $request->query('page', 1);

        // جلب الطلبات مع الفاتورة والعلاقات الأخرى
        $query = Order::with(['orderItems.product.section', 'user', 'createstore', 'invoice'])
            ->when($createstoreId, fn($q) => $q->where('createstore_id', $createstoreId))
            ->when($status, function ($q) use ($status) {
                if ($status === 'completed') {
                    $q->whereIn('status', ['completed', 'canceled']);
                } else {
                    $q->where('status', $status);
                }
            });



        // حساب الإحصائيات
        $newOrder = Order::where('status', 'pending')
            ->when($createstoreId, fn($q) => $q->where('createstore_id', $createstoreId))
            ->count();

        $completedOrder = Order::whereIn('status', ['completed', 'canceled'])
            ->when($createstoreId, fn($q) => $q->where('createstore_id', $createstoreId))
            ->count();

        $currentOrder = Order::where('status', 'current')
            ->when($createstoreId, fn($q) => $q->where('createstore_id', $createstoreId))
            ->count();

        // إذا كان per_page = 0، نعيد فقط الإحصائيات
        if ($perPage == 0) {
            return response()->json([
                'new_orders' => $newOrder,
                'completed_orders' => $completedOrder,
                'current_orders' => $currentOrder,
            ]);
        }

        // جلب الطلبات مع الترقيم
        $orders = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'new_orders' => $newOrder,
            'completed_orders' => $completedOrder,
            'current_orders' => $currentOrder,
            'orders' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'status' => $order->status,
                    'current_status' => $order->current_status,

                    'total_price' => $order->total_price,
                    'created_at' => ($order->created_at), // تحويل التاريخ
                    'address' => $order->invoice->address ?? 'لم يتم تحديد عنوان',
                    'notes' => $order->invoice->notes ?? 'لا يوجد ملاحظات',
                    'user' => $order->user ? [
                        'id' => $order->user->id,
                        'full_name' => trim($order->user->first_name . ' ' . $order->user->last_name),
                        'phone' => $order->user->phone,
                        'location' => trim($order->user->area . ', ' . $order->user->city),
                    ] : null,
                    'store' => $order->createstore ? [
                        'id' => $order->createstore->id,
                        'name' => $order->createstore->name,
                        'store_type_name_ar' => optional($order->createstore->storetype)->name_ar,
                        'store_type_name_en' => optional($order->createstore->storetype)->name_en,
                    ] : null,

                    'order_items' => $order->orderItems->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'section_id' => optional($item->product)->section_id,
                            'section_name' => optional($item->product->section)->name_ar,
                        ];
                    }),

                ];
            }),
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $orders->lastPage(),
            ],
        ]);
    }


    public function userOrders(Request $request)
    {
        try {
            // التحقق من التوكن
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // البحث عن userauth المرتبط برقم الهاتف
            $userauth = Userauth::where('phone', $user->phone_number)->first();

            if (!$userauth) {
                return response()->json(['error' => 'User authentication not found'], 404);
            }

            $status = $request->query('status');
            $query = Order::with('orderItems.product', 'invoice')->where('userauth_id', $userauth->id);

            if ($status) {
                if ($status === 'completed') {
                    $query->whereIn('status', ['completed', 'canceled']);
                } elseif ($status === 'current') {
                    $query->whereIn('status', ['current', 'pending']);
                }
            }

            $perPage = 10;
            $ordersPaginator = $query->paginate($perPage);
            $orders = $ordersPaginator->getCollection();

            $orders->map(function ($order) {
                $order->orderItems->map(function ($item) {
                    $product = $item->product;
                    if ($product && $product->image) {
                        // تأكد إن الصورة تكون array من روابط صالحة
                        if (is_string($product->image)) {
                            $decoded = json_decode($product->image, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $product->image = array_map(function ($img) {
                                    return filter_var($img, FILTER_VALIDATE_URL) ? $img : url('addproducts/' . ltrim($img, '/'));
                                }, $decoded);
                            } else {
                                $product->image = [filter_var($product->image, FILTER_VALIDATE_URL)
                                    ? $product->image
                                    : url('addproducts/' . ltrim($product->image, '/'))];
                            }
                        } elseif (is_array($product->image)) {
                            $product->image = array_map(function ($img) {
                                return filter_var($img, FILTER_VALIDATE_URL) ? $img : url('addproducts/' . ltrim($img, '/'));
                            }, $product->image);
                        }
                    } else {
                        $product->image = [];
                    }
                    $product->section_name_ar = $product->section ? $product->section->name_ar : null;
                    $product->section_name_en = $product->section ? $product->section->name_en : null;

                    // نخفي بيانات الـ section نفسها لو مش محتاجينها
                    unset($product->section);
                    return $item;
                });
                return $order;
            });

            return response()->json([
                'status' => true,
                'message' => 'قائمة الطلبات الخاصة بالمستخدم',
                'data' => $orders->map(fn($order) => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'current_status' => $order->status === 'current' ? $order->current_status : null,
                    'total_price' => $order->total_price,
                    'address' => $order->invoice->address ?? 'لم يتم تحديد عنوان',
                    'notes' => $order->invoice->notes ?? 'لا يوجد ملاحظات',
                    'order_items' => $order->orderItems,
                ]),
                'total_orders' => $ordersPaginator->total(),
                'total_pages' => $ordersPaginator->lastPage(),
                'current_page' => $ordersPaginator->currentPage(),
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Token is invalid or expired'], 400);
        }
    }




    // عرض طلب معين باستخدام id
    public function show($orderId)
    {
        // البحث عن الطلب باستخدام المعرف وتحميل العلاقات المطلوبة
        $order = Order::with(['orderItems.product.section', 'user'])->find($orderId);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404); // إذا لم يتم العثور على الطلب
        }

        // تحويل الصور إلى روابط
        $order->orderItems->map(function ($item) {
            if ($item->product->image) {
                // تأكد من أن الصورة عبارة عن JSON أو سلسلة صور
                $images = json_decode($item->product->image);
                if (is_array($images)) {
                    $imageLinks = array_map(fn($image) => url('addproducts/' . $image), $images);
                    $item->product->image = $imageLinks;
                } else {
                    // إذا كانت الصورة واحدة وليست JSON
                    $item->product->image = url('addproducts/' . $item->product->image);
                }
            }

            return $item;
        });

        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'current_status' => $order->status === 'current' ? $order->current_status : null,
            'total_price' => $order->total_price,
            'user' => $order->user ? [
                'id' => $order->user->id,
                'full_name' => trim($order->user->first_name . ' ' . $order->user->last_name), // ✅ دمج الاسم الأول والأخير
                'phone' => $order->user->phone,
                'location' => trim($order->user->area . ', ' . $order->user->city), // ✅ دمج المنطقة والمدينة
            ] : null,
            'order_items' => $order->orderItems->map(fn($item) => [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'section_id' => $item->product->section_id ?? null,
                'section_name' => $item->product->section->name_ar ?? null,
                'images' => $item->product->image // ✅ صور المنتجات بعد تحويلها لرابط
            ])
        ]);
    }

    public function updateUserOrder(Request $request, $orderId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $userauth = Userauth::where('phone', $user->phone_number)->first();
            $order = Order::where('id', $orderId)->where('userauth_id', $userauth->id)->first();

            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            // لو اليوزر طلب إلغاء الأوردر
            if ($request->has('cancel') && $request->cancel == true) {
                if (in_array($order->status, ['completed', 'canceled'])) {
                    return response()->json(['error' => 'لا يمكن إلغاء طلب مكتمل أو ملغي بالفعل'], 400);
                }
                $order->status = 'canceled';
                $order->current_status = 'canceled';
                $order->save();

                return response()->json([
                    'status' => true,
                    'message' => 'تم إلغاء الطلب بنجاح',
                    'order' => $order,
                ]);
            }

            // تحديث الحالة لو طلب تحديثها
            if ($request->has('status')) {
                $order->status = $request->status;
            }
            if ($request->has('current_status')) {
                $order->current_status = $request->current_status;
            }

            // تحديث العناصر داخل الطلب لو تم إرسال order_items
            if ($request->has('order_items')) {
                foreach ($request->order_items as $itemUpdate) {
                    if (isset($itemUpdate['id']) && isset($itemUpdate['quantity'])) {
                        $orderItem = $order->orderItems()->where('id', $itemUpdate['id'])->first();
                        if ($orderItem) {
                            $orderItem->quantity = $itemUpdate['quantity'];
                            $orderItem->save();
                        }
                    }
                }
            }

            // إعادة حساب السعر الكلي للطلب بعد التحديث
            $newTotalPrice = $order->orderItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });
            $order->total_price = $newTotalPrice;
            $order->save();

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث الطلب بنجاح',
                'order' => $order->load('orderItems.product'),
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Token is invalid or expired'], 400);
        }
    }

    public function deleteUserOrder(Request $request, $orderId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $userauth = Userauth::where('phone', $user->phone_number)->first();
            if (!$userauth) {
                return response()->json(['error' => 'User authentication not found'], 404);
            }

            $order = Order::where('id', $orderId)
                ->where('userauth_id', $userauth->id)
                ->first();

            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            $order->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم حذف الطلب بنجاح'
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Token is invalid or expired'], 400);
        }
    }
}
