<?php

namespace App\Http\Controllers\Apis;

use App\Models\aboutus;
use Illuminate\Http\Request;
use App\Http\Trait\GeneralTrait;
use App\Http\Controllers\Controller;

class AboutusController extends Controller
{
    use GeneralTrait;

public function index(Request $request)
{
    $query = aboutus::query();

    // تطبيق الفلترة باستخدام LIKE للبحث الجزئي
    if ($request->has('section_ar')) {
        $query->where('section_ar', 'like', '%' . $request->section_ar . '%');
    }
    if ($request->has('section_en')) {
        $query->where('section_en', 'like', '%' . $request->section_en . '%');
    }

    $aboutus = $query->get()->groupBy(function ($item) {
        return $item->section_ar . '|' . $item->section_en;
    })->map(function ($items, $key) {
        [$section_ar, $section_en] = explode('|', $key);
        
        return [
            'section_ar' => $section_ar,
            'section_en' => $section_en,
            'data' => $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title_ar' => $item->title_ar,
                    'title_en' => $item->title_en,
                    'description_ar' => $item->description_ar,
                    'description_en' => $item->description_en,
                ];
            })->values()
        ];
    })->values();

    return $this->returnData('aboutus', $aboutus);
}





public function store(Request $request)
{
    try {
        // التحقق من صحة البيانات
        $request->validate([
            'section_ar' => 'required|string',
            'section_en' => 'required|string',
            'data' => 'required|array',
            'data.*.title_ar' => 'required|string',
            'data.*.title_en' => 'required|string',
            'data.*.description_ar' => 'required|string',
            'data.*.description_en' => 'required|string',
        ]);

        $aboutusRecords = [];

        // إدخال جميع البيانات في قاعدة البيانات
        foreach ($request->data as $entry) {
            $aboutusRecords[] = aboutus::create([
                'section_ar' => $request->section_ar,
                'section_en' => $request->section_en,
                'title_ar' => $entry['title_ar'],
                'title_en' => $entry['title_en'],
                'description_ar' => $entry['description_ar'],
                'description_en' => $entry['description_en'],
            ]);
        }

        return $this->returnData('aboutus', $aboutusRecords);

    } catch (\Exception $e) {
        return $this->returnError('E001', 'حدث خطأ أثناء حفظ البيانات');
    }
}




public function update(Request $request)
{
    try {
        // التحقق من صحة البيانات
        $request->validate([
            'section_ar' => 'nullable|string',
            'section_en' => 'nullable|string',
            'data' => 'nullable|array',
            'data.*.id' => 'nullable|exists:aboutus,id',
            'data.*.title_ar' => 'nullable|string',
            'data.*.title_en' => 'nullable|string',
            'data.*.description_ar' => 'nullable|string',
            'data.*.description_en' => 'nullable|string',
        ]);

        $updatedRecords = [];

        foreach ($request->data as $entry) {
            $aboutus = aboutus::where('id', $entry['id'])
                ->where('section_ar', $request->section_ar)
                ->where('section_en', $request->section_en)
                ->first();

            if ($aboutus) {
                $aboutus->update([
                    'title_ar' => $entry['title_ar'],
                    'title_en' => $entry['title_en'],
                    'description_ar' => $entry['description_ar'],
                    'description_en' => $entry['description_en'],
                ]);

                $updatedRecords[] = $aboutus;
            }
        }

        return $this->returnData('aboutus', $updatedRecords);

    } catch (\Exception $e) {
        return $this->returnError('E002', 'حدث خطأ أثناء التحديث');
    }
}


    public function show($id)
    {
        $aboutus = aboutus::findOrFail($id);


        return response()->json([
            'status' => true,
            'data' => $aboutus
        ]);
    }

    public function destroy($id)
    {
        $aboutus = aboutus::findOrFail($id)->delete();
        if(!$aboutus){
            return $this->returnError('E001','data not found');
        }else{
            return $this->returnSuccessMessage('data deleted');
        }




    }
}

