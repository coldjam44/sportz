<?php

namespace App\Http\Controllers\Apis;

use App\Models\providerrate;
use Illuminate\Http\Request;
use App\Models\createstadium;
use App\Http\Trait\GeneralTrait;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ProviderrateController extends Controller
{
    use GeneralTrait;


public function index(Request $request)
{
    // جلب `stadium_id` و `store_id` من الطلب
    $stadiumId = $request->input('stadium_id');
    $storeId = $request->input('store_id');

    // بناء الاستعلام مع الفلاتر حسب الحاجة
    $query = providerrate::query();

    if ($stadiumId) {
        $query->where('stadium_id', $stadiumId);
    }

    if ($storeId) {
        $query->where('store_id', $storeId);
    }

    // إجمالي عدد التقييمات لكل `stadium_id` و `store_id`
    $ratesSummary = providerrate::selectRaw('
        stadium_id, store_id, COUNT(rate) as total_rates
    ')
    ->groupBy('stadium_id', 'store_id')
    ->get();

    // تنفيذ الاستعلام مع التقسيم إلى صفحات (10 تقييمات لكل صفحة)
    $allRates = $query->orderBy('created_at', 'desc')->paginate(10);

    // تعديل البيانات بحيث يتم عرض التقييمات فقط بدون تفاصيل التنقل
    $filteredRates = [
        'current_page' => $allRates->currentPage(),
        'data' => $allRates->items(), // فقط بيانات التقييمات بدون بيانات التنقل
        'per_page' => $allRates->perPage(),
        'total' => $allRates->total(),
    ];

    // إضافة "منذ وقت" لكل تقييم
    foreach ($filteredRates['data'] as $rate) {
        $rate->time_ago = Carbon::parse($rate->created_at)->diffForHumans();
    }

    return $this->returnData('providerrates', [
        'rates_summary' => $ratesSummary,
        'all_rates' => $filteredRates
    ]);
}




   public function store(Request $request)
{
    try {
        // ✅ التحقق من أن أحد الحقلين فقط موجود (stadium_id أو store_id)
        if (!$request->filled('stadium_id') && !$request->filled('store_id')) {
            return $this->returnError('E002', 'You must provide either stadium_id or store_id.');
        }
        if ($request->filled('stadium_id') && $request->filled('store_id')) {
            return $this->returnError('E003', 'You can only provide either stadium_id or store_id, not both.');
        }

        // ✅ التحقق من الحقول الأخرى
        $request->validate([
            'name' => 'required',
            'rate' => 'required',
            'description' => 'required',
        ], [
            'name.required' => 'The name field is required.',
            'rate.required' => 'The rate field is required.',
            'description.required' => 'The description field is required.',
        ]);

        // ✅ حفظ البيانات
        $providerrate = new providerrate();
        $providerrate->stadium_id = $request->stadium_id;
        $providerrate->store_id = $request->store_id;
        $providerrate->name = $request->name;
        $providerrate->rate = $request->rate;
        $providerrate->description = $request->description;
        $providerrate->save();

        return $this->returnData('providerrate', $providerrate);

    } catch (\Exception $e) {
        return $this->returnError('E001', 'error');
    }
}


   public function update(Request $request)
{
    try {
        // ✅ التحقق من أن أحد الحقلين فقط موجود (stadium_id أو store_id)
        if (!$request->filled('stadium_id') && !$request->filled('store_id')) {
            return $this->returnError('E002', 'You must provide either stadium_id or store_id.');
        }
        if ($request->filled('stadium_id') && $request->filled('store_id')) {
            return $this->returnError('E003', 'You can only provide either stadium_id or store_id, not both.');
        }

        // ✅ التحقق من الحقول الأخرى
        $request->validate([
            'name' => 'required',
            'rate' => 'required',
            'description' => 'required',
        ], [
            'name.required' => 'The name field is required.',
            'rate.required' => 'The rate field is required.',
            'description.required' => 'The description field is required.',
        ]);

        // ✅ البحث عن التقييم وتحديثه
        $providerrate = providerrate::findOrFail($request->id);
        $providerrate->update([
            'stadium_id' => $request->stadium_id,
            'store_id' => $request->store_id,
            'name' => $request->name,
            'rate' => $request->rate,
            'description' => $request->description,
        ]);

        return $this->returnData('providerrate', $providerrate);

    } catch (\Exception $e) {
        return $this->returnError('E001', 'error');
    }
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $providerrate = providerrate::findOrFail($id);
        $providerrate->delete();
        if(!$providerrate){
            return $this->returnError('E001','data not found');
        }else{
            return $this->returnSuccessMessage('data deleted');
        }    }
}


