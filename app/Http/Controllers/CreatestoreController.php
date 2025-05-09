<?php

namespace App\Http\Controllers;

use App\Models\createstore;
use App\Models\section;
use Illuminate\Http\Request;

class CreatestoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $createstores=createstore::with('section')->paginate(5);
        $section=section::all();
        return view('pages.createstores.index',compact('createstores','section'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $sections=section::all();

    return view('pages.createstores.create',compact('sections'));
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // التحقق من البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'tax_record' => 'required|file|mimes:pdf,jpeg,png',
            'image' => 'nullable|file|mimes:jpeg,png,jpg',
            'section_id' => 'required|exists:sections,id',

        ]);

        // حفظ الصورة (إذا كانت موجودة)
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('store_images', 'public');
        }

        // حفظ وثيقة الضريبة (إذا كانت موجودة)
        if ($request->hasFile('tax_record')) {
            $taxRecordPath = $request->file('tax_record')->store('tax_records', 'public');
        }

        // حفظ البيانات في قاعدة البيانات
        $store = new createstore();
        $store->name = $request->name;
        $store->location = $request->location;
        $store->tax_record = $taxRecordPath ?? null;
        $store->image = $imagePath ?? null;
        $store->section_id = $request->section_id;
        $store->save();

        return redirect()->route('createstores.index')->with('success', 'تم إضافة الملعب بنجاح');
    }
    /**
     * Display the specified resource.
     */
    public function show(createstore $createstore)
    {
        //
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
        // التحقق من البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'tax_record' => 'nullable|file|mimes:pdf,jpeg,png',
            'image' => 'nullable|file|mimes:jpeg,png,jpg',
            'section_id' => 'required|exists:sections,id',
        ]);

        // العثور على الملعب بناءً على المعرف
        $store = createstore::findOrFail($id);

        // حفظ الصورة (إذا كانت موجودة)
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($store->image && file_exists(storage_path('app/public/' . $store->image))) {
                unlink(storage_path('app/public/' . $store->image));
            }
            $imagePath = $request->file('image')->store('store_images', 'public');
            $store->image = $imagePath;
        }

        // حفظ وثيقة الضريبة (إذا كانت موجودة)
        if ($request->hasFile('tax_record')) {
            // حذف الوثيقة القديمة إذا كانت موجودة
            if ($store->tax_record && file_exists(storage_path('app/public/' . $store->tax_record))) {
                unlink(storage_path('app/public/' . $store->tax_record));
            }
            $taxRecordPath = $request->file('tax_record')->store('tax_records', 'public');
            $store->tax_record = $taxRecordPath;
        }

        // تحديث بيانات الملعب
        $store->name = $request->name;
        $store->location = $request->location;
        $store->section_id = $request->section_id;


        // حفظ التحديثات
        $store->save();

        return redirect()->route('createstores.index')->with('success', 'تم تحديث الملعب بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $createstore = createstore::findOrFail($id);

            // حذف ملف الضريبة إذا كان موجودًا
            if ($createstore->tax_record && file_exists(public_path('tax_records/' . $createstore->tax_record))) {
                unlink(public_path('tax_records/' . $createstore->tax_record));
            }

            // حذف صورة الملعب إذا كانت موجودة
            if ($createstore->image && file_exists(public_path('createstore/' . $createstore->image))) {
                unlink(public_path('createstore/' . $createstore->image));
            }

            // حذف بيانات الملعب من قاعدة البيانات
            $createstore->delete();

            return redirect()->route('createstores.index')->with('success', 'تم حذف الملعب بنجاح');
        } catch (\Throwable $th) {
            return redirect()->route('createstores.index')->with('error', 'لم يتم حذف الملعب بنجاح');
        }
    }
}
