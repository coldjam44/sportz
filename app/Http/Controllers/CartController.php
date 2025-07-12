<?php

namespace App\Http\Controllers;

use App\Models\cart;
use App\Models\Order;
use App\Models\createstore;
use App\Models\invoice;
use App\Models\Favourite;
use App\Models\StoreSection;

use App\Models\userauth;
use App\Models\OrderItem;
use App\Models\addproduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        try {
            // استخراج التوكن
            $user = JWTAuth::parseToken()->authenticate();

            // التأكد من وجود المستخدم
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            // التحقق من وجود المستخدم في جدول userauths باستخدام رقم الهاتف
            $userauth = Userauth::where('phone', $user->phone_number)->first();  // تأكد من استخدام `phone_number`

            if (!$userauth) {
                return response()->json(['error' => 'User authentication not found'], 404);
            }

            // البحث عن المنتج في السلة
            $cartItem = Cart::where('userauth_id', $userauth->id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($cartItem) {
                // تحديث الكمية
                $cartItem->update(['quantity' => $cartItem->quantity + $request->quantity]);

                // إشعار بتحديث المنتج في السلة
                Notification::create([
                    'user_id' => $userauth->id,
                    'title' => 'تم تحديث المنتج في السلة',
                    'message_ar' => "تم تحديث كمية المنتج رقم {$request->product_id} في السلة إلى {$cartItem->quantity}",
                    'message_en' => "Product ID {$request->product_id} quantity updated in cart to {$cartItem->quantity}",
                    'type' => 'cart_update',
                ]);
            } else {
                // إضافة منتج جديد إلى السلة
                $newCartItem = Cart::create([
                    'userauth_id' => $userauth->id,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity
                ]);

                // إشعار بإضافة منتج جديد للسلة
                Notification::create([
                    'user_id' => $userauth->id,
                    'title' => 'تم إضافة منتج جديد إلى السلة',
                    'message_ar' => "تم إضافة المنتج رقم {$request->product_id} إلى السلة بكمية {$request->quantity}",
                    'message_en' => "Product ID {$request->product_id} added to cart with quantity {$request->quantity}",
                    'type' => 'cart_add',
                ]);
            }


            return response()->json(['message' => 'Product added to cart successfully']);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Token is invalid or expired'], 400);
        }
    }




    public function getCart()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $userauth = Userauth::where('phone', $user->phone_number)->first();

            if (!$userauth) {
                return response()->json(['message' => 'User authentication not found'], 404);
            }

            $cartItems = Cart::where('userauth_id', $userauth->id)
                ->with(['product.section'])
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['message' => 'السلة فارغة'], 200);
            }

            $favouriteProductIds = Favourite::where('userauth_id', $userauth->id)
                ->pluck('product_id')
                ->toArray();

            $transformedCart = [];
            $total = 0;
            $now = \Carbon\Carbon::now();

            foreach ($cartItems as $item) {
                $product = $item->product;

                // تحويل السعر والخصم لأرقام للتأكد من الحسابات
                $price = floatval($product->price);
                $discount = floatval($product->discount);

                // معالجة فترة الخصم
                $startDiscount = $product->start_time ? \Carbon\Carbon::parse($product->start_time) : null;
                $endDiscount = $product->end_time ? \Carbon\Carbon::parse($product->end_time) : null;

                $discountDetails = "لا يوجد خصم";

                if ($startDiscount && $endDiscount) {
                    if ($now->lt($startDiscount)) {
                        // قبل بداية الخصم
                        $discount = null;
                        $product->start_time = null;
                        $product->end_time = null;
                        $discountDetails = "الخصم سيبدأ من " . $startDiscount->toDateString();
                    } elseif ($now->gt($endDiscount)) {
                        // بعد انتهاء الخصم
                        $discount = null;
                        $product->start_time = null;
                        $product->end_time = null;
                        $discountDetails = "انتهت فترة الخصم بتاريخ " . $endDiscount->toDateString();
                    } else {
                        // خلال فترة الخصم
                        $discountDetails = "الخصم ساري من " . $startDiscount->toDateString() . " إلى " . $endDiscount->toDateString();
                    }
                }

                // السعر بعد الخصم
                $discountedPrice = $discount ? $price - ($price * ($discount / 100)) : $price;

                // إجمالي لهذا المنتج
                $productTotal = $discountedPrice * $item->quantity;

                $total += $productTotal;

                // الصور
                $images = json_decode($product->image);
                $imageUrls = is_array($images) ? array_map(fn($img) => url('addproducts/' . ltrim($img, '/')), $images) : [];

                $transformedCart[] = [
                    'cart_id' => $item->id,
                    'id' => $product->id,
                    'name_ar' => $product->name_ar,
                    'name_en' => $product->name_en,
                    'description_ar' => $product->description_ar,
                    'description_en' => $product->description_en,
                    'price' => $price,
                    'discount' => $discount,
                    'start_time' => $product->start_time,
                    'end_time' => $product->end_time,
                    'discount_details' => $discountDetails,
                    'image_urls' => $imageUrls,
                    'section_name_ar' => optional($product->section)->name_ar,
                    'section_name_en' => optional($product->section)->name_en,
                    'section_id' => optional($product->section)->id,
                    'in_cart' => true,
                    'in_favourite' => in_array($product->id, $favouriteProductIds),
                    'quantity' => $item->quantity,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'محتويات السلة',
                'data' => $transformedCart,
                'total' => number_format($total, 2)
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Token is invalid or expired'], 400);
        }
    }


    // public function getCart()
    // {
    //     try {
    //         $user = JWTAuth::parseToken()->authenticate();

    //         if (!$user) {
    //             return response()->json(['message' => 'Unauthorized'], 401);
    //         }

    //         $userauth = Userauth::where('phone', $user->phone_number)->first();

    //         if (!$userauth) {
    //             return response()->json(['message' => 'User authentication not found'], 404);
    //         }

    //         $cartItems = Cart::where('userauth_id', $userauth->id)
    //             ->with(['product.section'])
    //             ->get();

    //         if ($cartItems->isEmpty()) {
    //             return response()->json(['message' => 'السلة فارغة'], 200);
    //         }

    //         $favouriteProductIds = Favourite::where('userauth_id', $userauth->id)
    //             ->pluck('product_id')
    //             ->toArray();

    //         $transformedCart = [];
    //         $total = 0;

    //         foreach ($cartItems as $item) {
    //             $product = $item->product;

    //             // تحويل السعر والخصم لأرقام للتأكد من الحسابات
    //             $price = floatval($product->price);
    //             $discount = floatval($product->discount);

    //             // السعر بعد الخصم
    //             $discountedPrice = $price - ($price * ($discount / 100));

    //             // إجمالي لهذا المنتج
    //             $productTotal = $discountedPrice * $item->quantity;

    //             $total += $productTotal;

    //             // الصور
    //             $images = json_decode($product->image);
    //             $imageUrls = array_map(fn($img) => url('addproducts/' . $img), $images);

    //             $transformedCart[] = [
    //                 'cart_id' => $item->id,
    //                 'id' => $product->id,
    //                 'name_ar' => $product->name_ar,
    //                 'name_en' => $product->name_en,
    //                 'description_ar' => $product->description_ar,
    //                 'description_en' => $product->description_en,
    //                 'price' => $price,
    //                 'discount' => $discount,
    //                 'image_urls' => $imageUrls,
    //                 'section_name_ar' => optional($product->section)->name_ar,
    //                 'section_name_en' => optional($product->section)->name_en,
    //                 'section_id' => optional($product->section)->id,  // Added section ID here
    //                 'in_cart' => true,
    //                 'in_favourite' => in_array($product->id, $favouriteProductIds),
    //                 'quantity' => $item->quantity,
    //             ];
    //         }

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'محتويات السلة',
    //             'data' => $transformedCart,
    //             'total' => number_format($total, 2)
    //         ]);
    //     } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
    //         return response()->json(['error' => 'Token is invalid or expired'], 400);
    //     }
    // }




    public function updateCart(Request $request)
    {
        try {
            // استخراج المستخدم من التوكن
            $user = JWTAuth::parseToken()->authenticate();

            // التأكد من وجود المستخدم
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // التحقق من صحة البيانات المدخلة
            $request->validate([
                'cart_id' => 'required|exists:carts,id',
                'quantity' => 'required|integer|min:1'
            ]);

            // التحقق من وجود المنتج في سلة المستخدم
            $userauth = Userauth::where('phone', $user->phone_number)->first();
            $cart = Cart::where('id', $request->cart_id)->where('userauth_id', $userauth->id)->first();

            if (!$cart) {
                return response()->json(['message' => 'هذا المنتج غير موجود في سلتك'], 403);
            }

            // تحديث الكمية
            $cart->update(['quantity' => $request->quantity]);
            // إنشاء إشعار للمستخدم بعد تحديث الكمية
            Notification::create([
                'user_id' => $userauth->id,
                'title' => 'تم تحديث كمية المنتج في السلة',
                'message_ar' => "تم تحديث كمية المنتج رقم {$cart->product_id} في سلتك إلى {$request->quantity}",
                'message_en' => "Product ID {$cart->product_id} quantity updated in your cart to {$request->quantity}",
                'type' => 'cart_update',
            ]);
            return response()->json(['message' => 'تم تحديث الكمية بنجاح', 'cart' => $cart]);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Token is invalid or expired'], 400);
        }
    }


    public function removeFromCart($cart_id)
    {
        try {
            // استخراج المستخدم من التوكن
            $user = JWTAuth::parseToken()->authenticate();

            // التأكد من وجود المستخدم
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // التحقق من وجود المنتج في سلة المستخدم
            $userauth = Userauth::where('phone', $user->phone_number)->first();
            $cart = Cart::where('id', $cart_id)->where('userauth_id', $userauth->id)->first();

            if (!$cart) {
                return response()->json(['message' => 'هذا المنتج غير موجود في سلتك'], 403);
            }
            // إنشاء إشعار للمستخدم بعد حذف المنتج
            Notification::create([
                'user_id' => $userauth->id,
                'title' => 'تمت إزالة منتج من السلة',
                'message_ar' => "تمت إزالة المنتج رقم {$cart->product_id} من سلتك",
                'message_en' => "Product ID {$cart->product_id} has been removed from your cart",
                'type' => 'cart_remove',
            ]);
            // حذف المنتج من السلة
            $cart->delete();

            return response()->json(['message' => 'تمت إزالة المنتج من السلة بنجاح']);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Token is invalid or expired'], 400);
        }
    }



    public function checkout(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $userauth = Userauth::where('phone', $user->phone_number)->first();
            if (!$userauth) {
                return response()->json(['message' => 'User authentication not found'], 404);
            }

            // جلب كل المنتجات في السلة مع العلاقات
            $cartItems = Cart::where('userauth_id', $userauth->id)->with('product.section.storeSection')->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['message' => 'السلة فارغة'], 400);
            }

            // تجميع المنتجات حسب createstore_id
            $groupedByStore = $cartItems->groupBy(function ($item) {
                return optional($item->product->section->storeSection)->store_id;
            });

            $orders = [];
            foreach ($groupedByStore as $createstore_id => $items) {
                $total = $items->sum(function ($item) {
                    return $item->product->price * $item->quantity;
                });

                // إنشاء الطلب
                $order = Order::create([
                    'userauth_id'    => $userauth->id,
                    'total_price'    => $total,
                    'status'         => 'pending',
                    'createstore_id' => $createstore_id,
                ]);

                // إضافة المنتجات إلى الطلب
                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $item->product_id,
                        'quantity'   => $item->quantity,
                        'price'      => $item->product->price,
                    ]);
                }

                // تحديث الفاتورة
                $cartIds = $cartItems->pluck('id');
                $invoice = Invoice::whereIn('cart_id', $cartIds)->first();
                if ($invoice) {
                    $invoice->update([
                        'order_id' => $order->id,
                        'cart_id' => null,
                    ]);
                }


                // حذف العناصر من السلة
                Cart::whereIn('id', $items->pluck('id'))->delete();

                // بعد إنشاء الطلب وإضافة العناصر
                Notification::create([
                    'user_id' => $userauth->id,
                    'title' => 'تم إنشاء طلب جديد',
                    'message_ar' => "تم إنشاء طلب جديد برقم {$order->id} بقيمة {$total} ريال.",
                    'message_en' => "A new order with ID {$order->id} has been created with total amount {$total}.",
                    'type' => 'order_created',
                ]);


                $orders[] = $order;
            }




            return response()->json([
                'message' => 'تم تأكيد الطلبات بنجاح',
                'orders'  => $orders
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Token is invalid or expired'], 400);
        } catch (\Exception $ex) {
            return response()->json(['error' => 'حدث خطأ داخلي', 'details' => $ex->getMessage()], 500);
        }
    }



    // public function checkout(Request $request)
    // {
    //     try {
    //         $user = JWTAuth::parseToken()->authenticate();

    //         if (!$user) {
    //             return response()->json(['message' => 'Unauthorized'], 401);
    //         }

    //         $userauth = Userauth::where('phone', $user->phone_number)->first();
    //         if (!$userauth) {
    //             return response()->json(['message' => 'User authentication not found'], 404);
    //         }

    //         $cartItems = Cart::where('userauth_id', $userauth->id)->with('product.section')->get();
    //         if ($cartItems->isEmpty()) {
    //             return response()->json(['message' => 'السلة فارغة'], 400);
    //         }

    //         $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

    //         // جلب الـ section_id من أول منتج
    //         $firstProduct = optional($cartItems->first())->product;
    //         $section_id = optional($firstProduct)->section_id;

    //         // Get store_id based on section_id from store_section table
    //         $storeSection = StoreSection::where('section_id', $section_id)->first();
    //         $createstore_id = $storeSection ? $storeSection->store_id : null;

    //         // لوق البيانات
    //         $debugData = [
    //             'first_product' => $firstProduct,
    //             'section_id' => $section_id,
    //             'store_section_found' => $storeSection ? true : false,
    //             'createstore_id' => $createstore_id,
    //             'cart_items_count' => $cartItems->count(),
    //             'cart_items' => $cartItems->map(function ($item) {
    //                 return [
    //                     'product_id' => $item->product_id,
    //                     'product_section_id' => optional($item->product)->section_id,
    //                     'product_name' => optional($item->product)->name_en,
    //                     'quantity' => $item->quantity,
    //                 ];
    //             }),
    //         ];

    //         // إنشاء الطلب
    //         $order = Order::create([
    //             'userauth_id' => $userauth->id,
    //             'total_price' => $total,
    //             'status' => 'pending',
    //             'createstore_id' => $createstore_id,
    //         ]);

    //         // إضافة المنتجات إلى الطلب
    //         foreach ($cartItems as $item) {
    //             OrderItem::create([
    //                 'order_id' => $order->id,
    //                 'product_id' => $item->product_id,
    //                 'quantity' => $item->quantity,
    //                 'price' => $item->product->price,
    //             ]);
    //         }

    //         // تحديث الفاتورة
    //         $cartIds = $cartItems->pluck('id');
    //         $invoice = Invoice::whereIn('cart_id', $cartIds)->first();
    //         if ($invoice) {
    //             $invoice->update([
    //                 'order_id' => $order->id,
    //                 'cart_id' => null,
    //             ]);
    //         }

    //         // حذف السلة
    //         Cart::where('userauth_id', $userauth->id)->delete();

    //         return response()->json([
    //             'message' => 'تم تأكيد الطلب بنجاح',
    //             'order' => $order,
    //             'debug' => $debugData
    //         ]);

    //     } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
    //         return response()->json(['error' => 'Token is invalid or expired'], 400);
    //     } catch (\Exception $ex) {
    //         return response()->json(['error' => 'حدث خطأ داخلي', 'details' => $ex->getMessage()], 500);
    //     }
    // }




    public function updateOrderStatus($orderId, Request $request)
    {
        try {
            if (!$request->has('status')) {
                return response()->json(['error' => 'Missing status parameter'], 400);
            }

            $order = Order::findOrFail($orderId);

            $validStatuses = ['pending', 'completed', 'canceled', 'current'];
            $status = $request->input('status');

            if (!in_array($status, $validStatuses)) {
                return response()->json(['error' => 'Invalid status value'], 400);
            }

            // تحديث حالة الطلب
            $order->status = $status;

            // ✅ تحديث الحالة الفرعية عند اختيار current
            if ($status === 'current') {
                $validCurrentStatuses = ['Approved', 'Shipping', 'Shipped'];
                if (!$request->has('current_status') || !in_array($request->input('current_status'), $validCurrentStatuses)) {
                    return response()->json(['error' => 'Invalid or missing current_status value'], 400);
                }
                $order->current_status = $request->input('current_status');
            } else {
                $order->current_status = null; // تصفير الحالة الفرعية إذا لم يكن current
            }

            $order->save();
            Notification::create([
                'user_id' => auth()->user()->id ?? null,
                'title' => 'تم تحديث حالة الطلب',
                'message_ar' => "تم تحديث حالة الطلب رقم {$order->id} إلى: {$order->status}",
                'message_en' => "Order #{$order->id} status updated to: {$order->status}",
                'type' => 'order_status_update',
            ]);
            return response()->json([
                'message' => 'تم تحديث حالة الطلب بنجاح',
                'order' => $order
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Order not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ'], 500);
        }
    }

    // public function updateOrderStatus($orderId, Request $request)
    // {
    //     try {
    //         // تحقق من وجود الـ status في الطلب
    //         if (!$request->has('status')) {
    //             return response()->json(['error' => 'Missing status parameter'], 400);
    //         }

    //         // جلب الطلب باستخدام المعرف
    //         $order = Order::findOrFail($orderId);

    //         // التحقق من صحة الحالة المرسلة
    //         $validStatuses = [
    //             'received' => 'تم استلام طلبك',
    //             'accepted' => 'تمت الموافقة',
    //             'rejected' => 'تم الرفض',
    //             'shipping' => 'جارٍ الشحن',
    //             'shipped' => 'تم الشحن',
    //             'completed' => 'تم الانتهاء'
    //         ];

    //         $status = $request->input('status');

    //         if (!array_key_exists($status, $validStatuses)) {
    //             return response()->json(['error' => 'Invalid status value'], 400);
    //         }

    //         // تحديث حالة الطلب
    //         $order->status = $status;
    //         $order->status_text = $validStatuses[$status]; // حفظ النصوص باللغة العربية
    //         $order->save();

    //         return response()->json([
    //             'message' => 'تم تحديث حالة الطلب بنجاح',
    //             'order' => [
    //                 'id' => $order->id,
    //                 'status' => $order->status,
    //                 'status_text' => $order->status_text
    //             ]
    //         ]);
    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         return response()->json(['error' => 'Order with id ' . $orderId . ' not found'], 404);
    //     } catch (\Exception $e) {
    //         Log::error('Error updating order status: ' . $e->getMessage());
    //         return response()->json(['error' => 'حدث خطأ أثناء تحديث الطلب'], 500);
    //     }
    // }






}
