<?php

namespace App\Http\Controllers;

use App\Models\section;
use App\Models\addproduct;
use Illuminate\Http\Request;

class AddproductController extends Controller
{
    public function index()
    {
        $products = AddProduct::with('section')->get();
        return view('pages.addproducts.index', compact('products'));
    }

    // عرض صفحة إنشاء منتج جديد
    public function create()
    {
        $sections = section::all();
        return view('pages.addproducts.create', compact('sections'));
    }

    // تخزين منتج جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'image' => 'required|array',
            'price' => 'required|numeric',
            'discount' => 'nullable|string',
            'section_id' => 'nullable|exists:sections,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        ]);

        if ($request->hasFile('image')) {
            $addproducts = [];
            foreach ($request->file('image') as $file) {
                $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('addproducts'), $imageName);
                $addproducts[] = $imageName;  // حفظ اسم الصورة في المصفوفة
            }
        } else {
            // في حالة عدم وجود صور
            $addproducts = [];
        }


        AddProduct::create([
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
        ]);

        return redirect()->route('addproducts.index')->with('success', 'تم إضافة المنتج بنجاح');
    }

    // عرض صفحة التعديل
    public function edit($id)
    {
        $product = AddProduct::findOrFail($id);
        $sections = section::all();
        return view('pages.addproducts.edit', compact('product', 'sections'));
    }

    // تحديث المنتج
    public function update(Request $request, $id)
    {
        $product = AddProduct::findOrFail($id);

        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            //'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'price' => 'required|numeric',
            'discount' => 'nullable|string',
            'section_id' => 'nullable|exists:sections,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ]);

        $addproducts = [];
        if ($request->hasFile('image')) {
            $existingImages = json_decode($product->image, true);
            foreach ($existingImages as $image) {
                $imagePath = public_path('addproducts/' . $image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            foreach ($request->file('image') as $file) {
                $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('addproducts'), $imageName);
                $addproducts[] = $imageName;
            }
        } else {
            $addproducts = json_decode($product->image, true);
        }

        $product->update([
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
        ]);
        return redirect()->route('addproducts.index')->with('success', 'تم تحديث المنتج بنجاح');
    }

    // حذف المنتج
    public function destroy($id)
    {
        $product = AddProduct::findOrFail($id);

        $images = json_decode($product->image);

        if (is_array($images)) {
            foreach ($images as $image) {
                $imagePath = public_path('addproducts/' . $image);
                if (file_exists($imagePath)) {
                    unlink($imagePath); // Delete the image file
                }
            }
        }

        $product->delete();

        return redirect()->route('addproducts.index')->with('success', 'تم حذف المنتج بنجاح');
    }
}
