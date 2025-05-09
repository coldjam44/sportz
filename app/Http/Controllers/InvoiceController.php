<?php

namespace App\Http\Controllers;

use App\Models\cart;
use App\Models\Order;

use App\Models\invoice;
use Illuminate\Http\Request;
   use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{

 public function createInvoice(Request $request)
{
    // تحقق من صحة المدخلات
    $request->validate([
        'cart_id' => 'required|exists:carts,id',
        'address' => 'required|string',
        'notes' => 'nullable|string',
    ]);

    // جلب السلة بناءً على ID السلة مع المنتجات
    $cart = Cart::with('product')->find($request->cart_id);

    if (!$cart) {
        return response()->json(['error' => 'Cart not found'], 404);
    }

    // التأكد من أن السلة تحتوي على منتجات
    if (!$cart->product) {
        return response()->json(['error' => 'No product found in cart'], 400);
    }

    // حساب المجموع الإجمالي للسلة
    $totalAmount = $cart->product->price * $cart->quantity; // إذا كان لديك حقل quantity في Cart

    // إنشاء الفاتورة
    $invoice = Invoice::create([
        'cart_id' => $request->cart_id,
        'address' => $request->address,
        'notes' => $request->notes,
        'total_amount' => $totalAmount,
    ]);

    return response()->json([
        'message' => 'Invoice created successfully',
        'invoice' => $invoice
    ]);
}

public function show($id)
{
    // جلب الفاتورة بناءً على ID مع السلة والمنتجات
    $invoice = Invoice::with('cart.product')->find($id); // جلب المنتجات المرتبطة بالسلة

    if (!$invoice) {
        return response()->json(['error' => 'Invoice not found'], 404);
    }

    // تحويل الفاتورة إلى Collection
    $invoice = collect([$invoice]);

    // تحويل الصور من JSON إلى روابط كاملة
    $invoice = $invoice->map(function ($item) {
        // تحقق من وجود المنتج
        if ($item->cart && $item->cart->product) {
            // إذا كان الحقل image عبارة عن JSON من صور
            $images = json_decode($item->cart->product->image);

            // تحقق إذا كانت الصور عبارة عن مصفوفة
            if (is_array($images)) {
                $imageLinks = array_map(function ($image) {
                    // تحويل كل صورة إلى رابط كامل
                    return url('addproducts/' . ltrim($image, '/'));
                }, $images);
            } else {
                // إذا كانت صورة واحدة فقط
                $imageLinks = [url('addproducts/' . ltrim($images, '/'))];
            }

            // إضافة الروابط المحولة إلى الصورة
            $item->cart->product->image = $imageLinks;
        }

        return $item;
    });

    // عرض الفاتورة مع تفاصيلها
    return response()->json([
        'invoice' => $invoice
    ]);
}










}
