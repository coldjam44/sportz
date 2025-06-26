<?php

namespace App\Http\Controllers\Apis;

use Carbon\Carbon;
use App\Models\sportsuser;
use App\Models\providerauth;

use Illuminate\Http\Request;
use App\Models\CreateStadium;
use App\Http\Trait\GeneralTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CreatestadiumController extends Controller
{
    use GeneralTrait;

    public function __construct()
    {
        $this->middleware('auth:api'); // التأكد من أن التوكن صالح
    }

    public function index()
    {
        $provider = providerauth::where('phone_number', auth()->user()->phone_number)->first(); // جلب المستخدم المصادق عليه من التوكن

        if (!$provider) {
            return response()->json([
                'status' => false,
                'message' => 'المستخدم غير مصادق عليه',
            ], 401);
        }

        // جلب الملاعب التي أنشأها المزود فقط
        $stadiums = CreateStadium::where('providerauth_id', $provider->id)->with(['avilableservice', 'sportsuser', 'rates'])
            ->get()->map(function ($stadium) {
                return [
                    'id' => $stadium->id,
                    'name' => $stadium->name,
                    'sportsuser_id' => $stadium->sportsuser_id,
                    'sportsuser_name_en' => $stadium->sportsuser ? $stadium->sportsuser->name_en : null,
                    'sportsuser_name_ar' => $stadium->sportsuser ? $stadium->sportsuser->name_ar : null,
                    'location' => $stadium->location,
                    'image_url' => $stadium->image ? asset($stadium->image) : null,
                    'tax_record_url' => $stadium->tax_record ? asset($stadium->tax_record) : null,
                    'morning_start_time' => $stadium->morning_start_time ? Carbon::parse($stadium->morning_start_time)->format('H') : null,
                    'morning_end_time' => $stadium->morning_end_time ? Carbon::parse($stadium->morning_end_time)->format('H') : null,
                    'evening_start_time' => $stadium->evening_start_time ? Carbon::parse($stadium->evening_start_time)->format('H') : null,
                    'evening_end_time' => $stadium->evening_end_time ? Carbon::parse($stadium->evening_end_time)->format('H') : null,
                    'booking_price' => $stadium->booking_price,
                    'evening_extra_price_per_hour' => $stadium->evening_extra_price_per_hour,
                    'team_members_count' => $stadium->team_members_count,
                    'avilableservices_en' => $stadium->avilableservice->pluck('name_en'),
                    'avilableservices_ar' => $stadium->avilableservice->pluck('name_ar'),
                    'average_rate' => $stadium->rates->avg('rate'),
                    'is_hidden' => $stadium->is_hidden,
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'الملاعب الخاصة بك',
            'data' => $stadiums
        ]);
    }


    public function store(Request $request)
    {
        $provider = providerauth::where('phone_number', auth()->user()->phone_number)->first(); // جلب المستخدم المصادق عليه من التوكن

        if (!$provider) {
            return response()->json([
                'status' => false,
                'message' => 'المستخدم غير مصادق عليه',
            ], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'sportsuser_id' => 'required|integer',
            'tax_record' => 'nullable|file|mimes:pdf,jpeg,png',
            'image' => 'nullable|file|mimes:jpeg,png,jpg',
            'morning_start_time' => 'required|integer',
            'morning_end_time' => 'required|integer',
            'evening_start_time' => 'required|integer',
            'evening_end_time' => 'required|integer',
            'booking_price' => 'required|numeric',
            'evening_extra_price_per_hour' => 'nullable|numeric',
            'team_members_count' => 'required|integer',
            'is_hidden' => 'nullable|boolean',
        ]);




        // إنشاء الملعب وربطه بالمزود (provider)
        $stadium = new CreateStadium($validated);
        $stadium->providerauth_id = $provider->id; // إضافة معرف المزود المصادق عليه

        // تحويل الأوقات إلى صيغة `H:i:s`
        $stadium->morning_start_time = Carbon::createFromFormat('H', $request->morning_start_time)->format('H:i:s');
        $stadium->morning_end_time = Carbon::createFromFormat('H', $request->morning_end_time)->format('H:i:s');
        $stadium->evening_start_time = Carbon::createFromFormat('H', $request->evening_start_time)->format('H:i:s');
        $stadium->evening_end_time = Carbon::createFromFormat('H', $request->evening_end_time)->format('H:i:s');

        // رفع الصور
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('stadium_images'), $imageName);
            $stadium->image = 'stadium_images/' . $imageName;
        }

        if ($request->hasFile('tax_record')) {
            $taxRecordName = time() . '_' . $request->file('tax_record')->getClientOriginalName();
            $request->file('tax_record')->move(public_path('stadium_images'), $taxRecordName);
            $stadium->tax_record = 'stadium_images/' . $taxRecordName;
        }

        $stadium->save();
        $stadium->avilableservice()->sync($request->avilableservice_ids ?? []);

        return response()->json([
            'status' => true,
            'message' => 'تم إضافة الملعب بنجاح',
            'data' => $stadium
        ]);
    }


    /**
     * عرض تفاصيل ملعب معين
     */
    public function show($id)
    {
        $stadium = CreateStadium::with('avilableservice')->find($id);

        if (!$stadium) {
            return response()->json(['status' => false, 'message' => 'الملعب غير موجود'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'تفاصيل الملعب',
            'data' => [
                'id' => $stadium->id,
                'name' => $stadium->name,
                'location' => $stadium->location,
                'image_url' => $stadium->image ? asset($stadium->image) : null,
                'tax_record_url' => $stadium->tax_record ? asset($stadium->tax_record) : null,
                'morning_start_time' => $stadium->morning_start_time,
                'morning_end_time' => $stadium->morning_end_time,
                'evening_start_time' => $stadium->evening_start_time,
                'evening_end_time' => $stadium->evening_end_time,
                'booking_price' => $stadium->booking_price,
                'evening_extra_price_per_hour' => $stadium->evening_extra_price_per_hour,
                'team_members_count' => $stadium->team_members_count,
                'avilableservices' => $stadium->avilableservice->pluck('name'),
            ]
        ]);
    }

    /**
     * تحديث ملعب
     */


    public function update(Request $request, $id)
    {
        $provider = providerauth::where('phone_number', auth()->user()->phone_number)->first();

        if (!$provider) {
            return response()->json([
                'status' => false,
                'message' => 'المستخدم غير مصادق عليه',
            ], 401);
        }

        try {
            $stadium = CreateStadium::where('id', $id)
                ->where('providerauth_id', $provider->id)
                ->firstOrFail();

            // الحقول التي نطبق عليها القفل
            $lockedFields = [
                'booking_price',
                'team_members_count',
                'morning_start_time',
                'morning_end_time',
                'evening_start_time',
                'evening_end_time',
                'evening_extra_price_per_hour',
            ];

            // جلب الحجوزات الجارية أو المستقبلية غير الملغاة مع تفاصيلها
            $activeBookings = DB::table('bookstadia')
                ->where('createstadium_id', $stadium->id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) {
                    $query->whereDate('date', '>', now()->toDateString())
                        ->orWhere(function ($q) {
                            $q->whereDate('date', now()->toDateString())
                                ->where('end_time', '>', now()->format('H:i:s'));
                        });
                })
                ->select('id', 'date', 'start_time', 'end_time')
                ->get();

            // تحقق إذا يوجد حجوزات نشطة
            if ($activeBookings->isNotEmpty()) {
                // تحقق هل يوجد تغيير فعلي في القيم المقفولة؟
                $isLockedFieldChanged = false;
                foreach ($lockedFields as $field) {
                    // القيمة الجديدة المرسلة
                    $newValue = $request->has($field) ? $request->input($field) : null;

                    // القيمة الحالية في قاعدة البيانات
                    $currentValue = $stadium->{$field};

                    // لتحويل أوقات الصباح والمساء من integer إلى صيغة "H:i:s" (مثلاً) يجب مقارنة صحيحة
                    if (in_array($field, ['morning_start_time', 'morning_end_time', 'evening_start_time', 'evening_end_time'])) {
                        if ($newValue !== null) {
                            // حول القيمة الجديدة (integer) إلى صيغة "H:i:s"
                            $newValueFormatted = Carbon::createFromFormat('H', $newValue)->format('H:i:s');
                            if ($newValueFormatted !== $currentValue) {
                                $isLockedFieldChanged = true;
                                break;
                            }
                        }
                    } else {
                        // مقارنة بسيطة للقيم الرقمية أو السعر
                        if ($newValue !== null && $newValue != $currentValue) {
                            $isLockedFieldChanged = true;
                            break;
                        }
                    }
                }

                if ($isLockedFieldChanged) {
                    $reservationsDetails = $activeBookings->map(function ($booking) {
                        $bookingDateTimeEnd = Carbon::parse($booking->date . ' ' . $booking->end_time);
                        $now = Carbon::now();

                        $remainingMinutes = $now->diffInMinutes($bookingDateTimeEnd, false);
                        if ($remainingMinutes > 0) {
                            $remaining = $now->diffForHumans($bookingDateTimeEnd, [
                                'parts' => 3,
                                'short' => true,
                                'syntax' => Carbon::DIFF_ABSOLUTE,
                            ]);
                        } else {
                            $remaining = 'انتهى';
                        }

                        return [
                            'reservation_id' => $booking->id,
                            'date' => $booking->date,
                            'start_time' => $booking->start_time,
                            'end_time' => $booking->end_time,
                            'time_remaining' => $remaining,
                        ];
                    });

                    return response()->json([
                        'status' => false,
                        'message' => 'لا يمكن تعديل الأسعار أو أوقات الحجز بسبب وجود حجوزات جارية أو مستقبلية.',
                        'reservations' => $reservationsDetails,
                    ], 400);
                }
            }

            // إذا لم يوجد حجوزات نشطة أو لم يتم تعديل القيم المقفولة استمر بالتحديث

            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'sportsuser_id' => 'nullable|integer',
                'tax_record' => 'nullable|file|mimes:pdf,jpeg,png',
                'image' => 'nullable|file|mimes:jpeg,png,jpg',
                'morning_start_time' => 'nullable|integer',
                'morning_end_time' => 'nullable|integer',
                'evening_start_time' => 'nullable|integer',
                'evening_end_time' => 'nullable|integer',
                'booking_price' => 'nullable|numeric',
                'evening_extra_price_per_hour' => 'nullable|numeric',
                'team_members_count' => 'nullable|integer',
                'is_hidden' => 'nullable|boolean',
            ]);

            $stadium->fill($validated);

            if ($request->has('morning_start_time')) {
                $stadium->morning_start_time = Carbon::createFromFormat('H', $request->morning_start_time)->format('H:i:s');
            }
            if ($request->has('morning_end_time')) {
                $stadium->morning_end_time = Carbon::createFromFormat('H', $request->morning_end_time)->format('H:i:s');
            }
            if ($request->has('evening_start_time')) {
                $stadium->evening_start_time = Carbon::createFromFormat('H', $request->evening_start_time)->format('H:i:s');
            }
            if ($request->has('evening_end_time')) {
                $stadium->evening_end_time = Carbon::createFromFormat('H', $request->evening_end_time)->format('H:i:s');
            }

            if ($request->hasFile('image')) {
                if ($stadium->image && file_exists(public_path($stadium->image))) {
                    unlink(public_path($stadium->image));
                }

                $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
                $request->file('image')->move(public_path('stadium_images'), $imageName);
                $stadium->image = 'stadium_images/' . $imageName;
            }

            if ($request->hasFile('tax_record')) {
                if ($stadium->tax_record && file_exists(public_path($stadium->tax_record))) {
                    unlink(public_path($stadium->tax_record));
                }

                $taxRecordName = time() . '_' . $request->file('tax_record')->getClientOriginalName();
                $request->file('tax_record')->move(public_path('stadium_images'), $taxRecordName);
                $stadium->tax_record = 'stadium_images/' . $taxRecordName;
            }

            $stadium->evening_extra_price_per_hour = $request->input('evening_extra_price_per_hour', null);
            $stadium->save();

            if ($request->has('avilableservice_ids')) {
                $avilableserviceIds = $request->avilableservice_ids;
                if (is_array($avilableserviceIds)) {
                    if (in_array(-1, $avilableserviceIds)) {
                        $stadium->avilableservice()->detach();
                    } else {
                        $stadium->avilableservice()->sync($avilableserviceIds);
                    }
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث الملعب بنجاح',
                'data' => $stadium
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء التحديث',
                'error' => $e->getMessage()
            ], 500);
        }
    }









    /**
     * حذف ملعب
     */
    public function destroy($id)
    {
        $provider = providerauth::where('phone_number', auth()->user()->phone_number)->first();;

        if (!$provider) {
            return response()->json([
                'status' => false,
                'message' => 'المستخدم غير مصادق عليه',
            ], 401);
        }

        try {
            $stadium = CreateStadium::where('id', $id)
                ->where('providerauth_id', $provider->id)
                ->firstOrFail();

            // حذف الملفات المرتبطة
            Storage::disk('public')->delete([$stadium->image, $stadium->tax_record]);

            // حذف جميع الخدمات المرتبطة
            $stadium->avilableservice()->detach();

            // حذف السجل
            $stadium->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم حذف الملعب بنجاح'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء الحذف',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
