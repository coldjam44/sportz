<?php

namespace App\Http\Controllers;

use App\Models\sportsuser;
use Illuminate\Http\Request;
use App\Models\createstadium;
use App\Models\avilableservice;

class CreatestadiumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $createstadiums=createstadium::with('avilableservice')->paginate(5);
        $sports=sportsuser::all();
        return view('pages.createstadiums.index',compact('createstadiums','sports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $sports=sportsuser::all();
    $amenities=avilableservice::all();

    return view('pages.createstadiums.create',compact('sports','amenities'));
}


    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     try {
    //         // التحقق من المدخلات
    //         $request->validate([
    //             'name' => 'required',
    //             'location' => 'required',
    //             'sportsuser_id' => 'required',
    //             'tax_record' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
    //             //'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //             'morning_start_time' => 'required|date_format:H:i', // ⏰ وقت بداية الفترة الصباحية
    //             'morning_end_time' => 'required|date_format:H:i|after:morning_start_time', // ⏰ وقت نهاية الفترة الصباحية
    //             'evening_start_time' => 'required|date_format:H:i', // 🌙 وقت بداية الفترة المسائية
    //             'evening_end_time' => 'required|date_format:H:i|after:evening_start_time', // 🌙 وقت نهاية الفترة المسائية
    //             'booking_price' => 'required|numeric|min:0', // 💰 سعر الحجز
    //             //'evening_extra_enabled' => 'boolean', // ✅ خيار تفعيل زيادة في الفترة المسائية
    //             'evening_extra_price_per_hour' => 'nullable|numeric|min:0', // 💰 سعر الزيادة في الفترة المسائية
    //             'team_members_count' => 'required|integer|min:1', // ⚽ عدد أعضاء الفريق
    //         ], [
    //             'name.required' => 'يرجى إدخال الاسم',
    //             'location.required' => 'يرجى إدخال الموقع',
    //             'sportsuser_id.required' => 'يرجى إدخال المستخدم',
    //             'tax_record.required' => 'يرجى رفع مستند الضريبة',
    //             'morning_start_time.required' => 'يرجى إدخال وقت بداية الفترة الصباحية',
    //             'morning_end_time.required' => 'يرجى إدخال وقت نهاية الفترة الصباحية',
    //             'morning_end_time.after' => 'يجب أن يكون وقت نهاية الفترة الصباحية بعد بدايتها',
    //             'evening_start_time.required' => 'يرجى إدخال وقت بداية الفترة المسائية',
    //             'evening_end_time.required' => 'يرجى إدخال وقت نهاية الفترة المسائية',
    //             'evening_end_time.after' => 'يجب أن يكون وقت نهاية الفترة المسائية بعد بدايتها',
    //             'booking_price.required' => 'يرجى إدخال سعر الحجز',
    //             'team_members_count.required' => 'يرجى إدخال عدد أعضاء الفريق',
    //         ]);

    //         // إنشاء سجل جديد
    //         $createstadium = new createstadium();
    //         $createstadium->name = $request->name;
    //         $createstadium->location = $request->location;
    //         $createstadium->sportsuser_id = $request->sportsuser_id;

    //         // رفع ملف الضريبة (صورة أو PDF)
    //         if ($request->hasFile('tax_record')) {
    //             $taxRecordFile = $request->file('tax_record');
    //             $taxRecordName = time() . '_tax.' . $taxRecordFile->getClientOriginalExtension();
    //             $taxRecordFile->move(public_path('tax_records'), $taxRecordName);
    //             $createstadium->tax_record = $taxRecordName;
    //         }

    //         // رفع صورة الملعب (اختياري)
    //         if ($request->hasFile('image')) {
    //             $image = $request->file('image');
    //             $imageName = time() . '_stadium.' . $image->getClientOriginalExtension();
    //             $image->move(public_path('createstadiums'), $imageName);
    //             $createstadium->image = $imageName;
    //         }

    //         // حفظ الأوقات والأسعار
    //         $createstadium->morning_start_time = $request->morning_start_time;
    //         $createstadium->morning_end_time = $request->morning_end_time;
    //         $createstadium->evening_start_time = $request->evening_start_time;
    //         $createstadium->evening_end_time = $request->evening_end_time;
    //         $createstadium->booking_price = $request->booking_price;
    //        // $createstadium->evening_extra_enabled = $request->evening_extra_enabled ?? 0;
    //         $createstadium->evening_extra_price_per_hour = $request->evening_extra_price_per_hour ?? null;
    //         $createstadium->team_members_count = $request->team_members_count;

    //         $createstadium->save();

    //         return redirect()->route('createstadiums.index')->with('success', 'تم إضافة الملعب بنجاح');
    //     } catch (\Throwable $th) {
    //         return redirect()->route('createstadiums.index')->with('error', 'لم يتم إضافة الملعب بنجاح');
    //     }
    // }
    public function store(Request $request)
    {
        // التحقق من البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'tax_record' => 'required|file|mimes:pdf,jpeg,png',
            'image' => 'nullable|file|mimes:jpeg,png,jpg',
            'morning_start_time' => 'required|date_format:H:i',
            'morning_end_time' => 'required|date_format:H:i',
            'evening_start_time' => 'required|date_format:H:i',
            'evening_end_time' => 'required|date_format:H:i',
            'booking_price' => 'required|numeric',
            'evening_extra_price_per_hour' => 'nullable|numeric',
            'team_members_count' => 'required|integer',
        ]);

        // حفظ الصورة (إذا كانت موجودة)
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('stadium_images', 'public');
        }

        // حفظ وثيقة الضريبة (إذا كانت موجودة)
        if ($request->hasFile('tax_record')) {
            $taxRecordPath = $request->file('tax_record')->store('tax_records', 'public');
        }

        // حفظ البيانات في قاعدة البيانات
        $stadium = new createstadium();
        $stadium->name = $request->name;
        $stadium->location = $request->location;
        $stadium->sportsuser_id = $request->sportsuser_id;
        $stadium->tax_record = $taxRecordPath ?? null;
        $stadium->image = $imagePath ?? null;
        $stadium->morning_start_time = $request->morning_start_time;
        $stadium->morning_end_time = $request->morning_end_time;
        $stadium->evening_start_time = $request->evening_start_time;
        $stadium->evening_end_time = $request->evening_end_time;
        $stadium->booking_price = $request->booking_price;
        $stadium->evening_extra_price_per_hour = $request->evening_extra_price_per_hour;
        $stadium->team_members_count = $request->team_members_count;

        $stadium->avilableservice()->sync($request->avilableservice_ids);
        $stadium->save();

        return redirect()->route('createstadiums.index')->with('success', 'تم إضافة الملعب بنجاح');
    }



    /**
     * Display the specified resource.
     */
    public function show(createstadium $createstadium)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
{
    $sports=sportsuser::all();
    $amenities = avilableservice::all();

    $stadium = createstadium::findOrFail($id);
    return view('pages.createstadiums.edit', compact('stadium','sports','amenities'));
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
        'morning_start_time' => 'required|',
        'morning_end_time' => 'required|',
        'evening_start_time' => 'required|',
        'evening_end_time' => 'required|',
        'booking_price' => 'required|numeric',
        'evening_extra_price_per_hour' => 'nullable|numeric',
        'team_members_count' => 'required|integer',
    ]);

    // العثور على الملعب بناءً على المعرف
    $stadium = createstadium::findOrFail($id);

    // حفظ الصورة (إذا كانت موجودة)
    if ($request->hasFile('image')) {
        // حذف الصورة القديمة إذا كانت موجودة
        if ($stadium->image && file_exists(storage_path('app/public/' . $stadium->image))) {
            unlink(storage_path('app/public/' . $stadium->image));
        }
        $imagePath = $request->file('image')->store('stadium_images', 'public');
        $stadium->image = $imagePath;
    }

    // حفظ وثيقة الضريبة (إذا كانت موجودة)
    if ($request->hasFile('tax_record')) {
        // حذف الوثيقة القديمة إذا كانت موجودة
        if ($stadium->tax_record && file_exists(storage_path('app/public/' . $stadium->tax_record))) {
            unlink(storage_path('app/public/' . $stadium->tax_record));
        }
        $taxRecordPath = $request->file('tax_record')->store('tax_records', 'public');
        $stadium->tax_record = $taxRecordPath;
    }

    // تحديث بيانات الملعب
    $stadium->name = $request->name;
    $stadium->location = $request->location;
    $stadium->sportsuser_id = $request->sportsuser_id;
    $stadium->morning_start_time = $request->morning_start_time;
    $stadium->morning_end_time = $request->morning_end_time;
    $stadium->evening_start_time = $request->evening_start_time;
    $stadium->evening_end_time = $request->evening_end_time;
    $stadium->booking_price = $request->booking_price;
    $stadium->evening_extra_price_per_hour = $request->evening_extra_price_per_hour;
    $stadium->team_members_count = $request->team_members_count;
    $stadium->avilableservice()->sync($request->avilableservice_ids);

    // حفظ التحديثات
    $stadium->save();

    return redirect()->route('createstadiums.index')->with('success', 'تم تحديث الملعب بنجاح');
}




    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    try {
        $createstadium = createstadium::findOrFail($id);

        // حذف ملف الضريبة إذا كان موجودًا
        if ($createstadium->tax_record && file_exists(public_path('tax_records/' . $createstadium->tax_record))) {
            unlink(public_path('tax_records/' . $createstadium->tax_record));
        }

        // حذف صورة الملعب إذا كانت موجودة
        if ($createstadium->image && file_exists(public_path('createstadiums/' . $createstadium->image))) {
            unlink(public_path('createstadiums/' . $createstadium->image));
        }

        // حذف بيانات الملعب من قاعدة البيانات
        $createstadium->delete();

        return redirect()->route('createstadiums.index')->with('success', 'تم حذف الملعب بنجاح');
    } catch (\Throwable $th) {
        return redirect()->route('createstadiums.index')->with('error', 'لم يتم حذف الملعب بنجاح');
    }
}

}
