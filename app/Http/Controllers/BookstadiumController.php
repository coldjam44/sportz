<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\userauth;
use App\Models\bookstadium;
use Illuminate\Http\Request;
use App\Models\CreateStadium;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookstadiumController extends Controller
{
  
 public function getAllStadiums(Request $request)
{
    try {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مصادق عليه'], 401);
        }

        $perPage = $request->input('per_page', 10);
        $sport_id = $request->query('sport_id');
        $location = $request->query('stadium_location'); // 👈 هنا
        $minRate = $request->query('min_rate');
        $maxRate = $request->query('max_rate');

        $query = CreateStadium::with(['sportsuser', 'rates', 'avilableservice'])
            ->withAvg('rates as average_rate', 'rate');

        if ($sport_id) {
            $query->whereHas('sportsuser', function ($q) use ($sport_id) {
                $q->where('id', $sport_id);
            });
        }

        if ($location) {
            $query->where('location', 'like', '%' . $location . '%');
        }

        if ($minRate !== null || $maxRate !== null) {
            $query->havingRaw('average_rate >= ?', [$minRate ?? 0]);

            if ($maxRate !== null) {
                $query->havingRaw('average_rate <= ?', [$maxRate]);
            }
        }

        $stadiumsPaginated = $query->paginate($perPage);

        $stadiumsPaginated->getCollection()->transform(function ($stadium) {
            $services = $stadium->avilableservice->map(function ($service) {
                return [
                    'name_en' => $service->name_en,
                    'name_ar' => $service->name_ar,
                    'image_url' => asset('avilableservices/' . $service->image)
                ];
            });

            return [
                'id' => $stadium->id,
                'name' => $stadium->name,
                'location' => $stadium->location,
                'image_url' => $stadium->image ? asset($stadium->image) : null,
                'team_members_count' => $stadium->team_members_count,
                'morning_start_time' => $stadium->morning_start_time,
                'morning_end_time' => $stadium->morning_end_time,
                'evening_start_time' => $stadium->evening_start_time,
                'evening_end_time' => $stadium->evening_end_time,
                'booking_price' => $stadium->booking_price,
                'evening_extra_price_per_hour' => $stadium->evening_extra_price_per_hour,
                'provider_id' => $stadium->providerauth_id,
                'sport_id' => $stadium->sportsuser?->id,
                'sportname_ar' => $stadium->sportsuser?->name_ar,
                'sportname_en' => $stadium->sportsuser?->name_en,
                'average_rate' => round($stadium->average_rate ?? 0, 2),
                'ratings_count' => $stadium->rates->count(),
                'services' => $services
            ];
        });

        return response()->json([
            'data' => $stadiumsPaginated->items(),
            'total_stadiums' => $stadiumsPaginated->total(),
            'total_pages' => $stadiumsPaginated->lastPage(),
            'current_page' => $stadiumsPaginated->currentPage(),
        ]);
    } catch (\Exception $e) {
        return response()->json(['message' => 'حدث خطأ: ' . $e->getMessage()], 500);
    }
}

  
 public function getStadiumDetails(Request $request, $id) 
{
    try {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مصادق عليه'], 401);
        }

        $stadium = CreateStadium::with(['sportsuser', 'rates', 'avilableservice'])->find($id);

        if (!$stadium) {
            return response()->json(['message' => 'الملعب غير موجود'], 404);
        }

        $perPage = $request->input('per_page', 10);

        // جلب كل الحجوزات الخاصة بهذا الملعب
        $bookings = BookStadium::where('createstadium_id', $id)
            ->select('date', 'start_time', 'end_time', 'booking_type')
            ->orderBy('date', 'desc')
            ->paginate($perPage);

        // نحول المجموعة الحالية من الحجوزات
        $bookingsTransformed = $bookings->getCollection()->map(function ($b) {
            return [
                'date' => $b->date,
                'start_time' => $b->start_time,
                'end_time' => $b->end_time,
                'booking_type' => $b->booking_type
            ];
        });

        // نُعيد التجميع بمجموعة حسب التاريخ
        $groupedByDate = $bookingsTransformed->groupBy('date')->map(function ($items) {
            return $items->map(function ($item) {
                return [
                    'start_time' => $item['start_time'],
                    'end_time' => $item['end_time'],
                    'booking_type' => $item['booking_type']
                ];
            });
        });

        // تحضير قائمة الخدمات المتاحة مع رابط الصورة
        $services = $stadium->avilableservice->map(function ($service) {
            return [
                'name_ar' => $service->name_ar,
                'name_en' => $service->name_en,
                'image_url' => asset('avilableservices/' . $service->image)
            ];
        });

        return response()->json([
            'stadium' => [
                'id' => $stadium->id,
                'name' => $stadium->name,
                'location' => $stadium->location,
                'image_url' => $stadium->image ? asset($stadium->image) : null,
                'team_members_count' => $stadium->team_members_count,
                'morning_start_time' => $stadium->morning_start_time,
                'morning_end_time' => $stadium->morning_end_time,
                'evening_start_time' => $stadium->evening_start_time,
                'evening_end_time' => $stadium->evening_end_time,
                'booking_price' => $stadium->booking_price,
                'evening_extra_price_per_hour' => $stadium->evening_extra_price_per_hour,
                'provider_id' => $stadium->providerauth_id,
                'sport_id' => $stadium->sportsuser?->id,
                'sportname_ar' => $stadium->sportsuser?->name_ar,
                'sportname_en' => $stadium->sportsuser?->name_en,
                'average_rate' => $stadium->rates->avg('rate'),
              'ratings_count' => $stadium->rates->count(), // ← عدد التقييمات

                'services' => $services
            ],
            'booked_dates_and_times' => $groupedByDate,
            'pagination' => [
                'total_bookings' => $bookings->total(),
                'total_pages' => $bookings->lastPage(),
                'current_page' => $bookings->currentPage(),
                'per_page' => $bookings->perPage(),
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json(['message' => 'حدث خطأ: ' . $e->getMessage()], 500);
    }
}

  
  
  
  
  public function store(Request $request)
{
    $request->validate([
        'date' => 'required|date_format:Y-m-d',
        'createstadium_id' => 'required|exists:createstadium,id',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i',
        'booking_type' => 'required|in:individual,team,field',
        'players_count' => 'nullable|integer|min:1',
        'teams_count' => 'nullable|integer|min:1',
        'min_players_per_team' => 'nullable|integer|min:1'
    ]);

    try {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مصادق عليه'], 401);
        }

        $userauth = UserAuth::where('phone', $user->phone_number)->first();
        if (!$userauth) {
            return response()->json(['message' => 'المستخدم غير موجود في قاعدة البيانات'], 400);
        }

        $stadium = CreateStadium::findOrFail($request->createstadium_id);
        $hours = (strtotime($request->end_time) - strtotime($request->start_time)) / 3600;
        $total_price = $hours * $stadium->booking_price;

        if (
            $request->start_time >= $stadium->evening_start_time &&
            $request->end_time <= $stadium->evening_end_time
        ) {
            $total_price += $hours * $stadium->evening_extra_price_per_hour;
        }

        $player_price = null;
        $remaining_teams = null;

        if ($request->booking_type == 'individual') {
            $required_players = $stadium->team_members_count;

            $currentPlayers = BookStadium::where('booking_type', 'individual')
                ->where('createstadium_id', $request->createstadium_id)
                ->where('date', $request->date)
                ->where('start_time', $request->start_time)
                ->where('end_time', $request->end_time)
                ->sum('players_count');

            $remaining_players = max($required_players - $currentPlayers, 0);

            if ($request->players_count > $remaining_players) {
                return response()->json([
                    'message' => "لا يمكن تسجيل هذا العدد، اللاعبين المتبقين فقط: $remaining_players"
                ], 400);
            }

            $player_price = $remaining_players > 0 ? $total_price / $remaining_players : 0;
            $total_price *= $request->players_count;
        }

        if ($request->booking_type == 'team') {
            if (!$request->teams_count || !$request->min_players_per_team) {
                return response()->json([
                    'message' => 'يجب إدخال عدد الفرق والحد الأدنى لكل فريق'
                ], 400);
            }

            $required_players = $request->teams_count * $request->min_players_per_team;

            $currentPlayers = BookStadium::where('booking_type', 'team')
                ->where('createstadium_id', $request->createstadium_id)
                ->where('date', $request->date)
                ->where('start_time', $request->start_time)
                ->where('end_time', $request->end_time)
                ->sum('players_count');

            $remaining_players = max($required_players - $currentPlayers, 0);

            if ($remaining_players < $request->min_players_per_team) {
                return response()->json([
                    'message' => "لا يمكن تسجيل هذا العدد، المتبقي فقط: $remaining_players"
                ], 400);
            }

            $total_price *= $request->teams_count;
            $remaining_teams = $request->teams_count; // ✅ هنا التعديل
        }

        $booking = BookStadium::create([
            'createstadium_id' => $request->createstadium_id,
            'userauth_id' => $userauth->id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'booking_type' => $request->booking_type,
            'players_count' => $request->players_count ?? null,
            'teams_count' => $request->teams_count,
            'min_players_per_team' => $request->min_players_per_team,
            'total_price' => $total_price,
            'player_price' => $player_price,
            'remaining_teams' => $remaining_teams, // ✅ التعيين هنا
        ]);

        return response()->json([
            'message' => 'تم إنشاء الحجز بنجاح',
            'data' => $booking
        ], 201);

    } catch (\Exception $e) {
        return response()->json(['message' => 'حدث خطأ: ' . $e->getMessage()], 500);
    }
}

    // EndPoint لجلب الحجوزات الفردية مع عدد اللاعبين المتبقيين
public function getIndividualBookings(Request $request)
{
    $perPage = $request->input('per_page', 10);
    $sportId = $request->input('sport_id');
    $location = $request->input('stadium_location'); // 👈 هنا
    $minRate = $request->input('min_rate');
    $maxRate = $request->input('max_rate');

    $query = BookStadium::with([
        'stadium' => function ($query) {
            $query->select('id', 'name', 'location', 'image', 'team_members_count', 'sportsuser_id')
                  ->withAvg('rates as average_rate', 'rate');
        },
        'stadium.sportsuser' => function ($query) {
            $query->select('id', 'name_ar', 'name_en');
        },
        'stadium.rates',
    ])->where('booking_type', 'individual') ->where(function ($q) {
        $q->whereNull('remaining_players')
          ->orWhere('remaining_players', '>', 0);
    });

    if ($sportId) {
        $query->whereHas('stadium', function ($q) use ($sportId) {
            $q->where('sportsuser_id', $sportId);
        });
    }

    if ($location) {
        $query->whereHas('stadium', function ($q) use ($location) {
            $q->where('location', 'like', '%' . $location . '%');
        });
    }

    if ($minRate !== null || $maxRate !== null) {
        $query->whereHas('stadium.rates', function ($q) use ($minRate, $maxRate) {
            $q->select(DB::raw('avg(rate) as average_rate'))
              ->groupBy('createstadium_id')
              ->havingRaw('avg(rate) >= ?', [$minRate ?? 0]);
            if ($maxRate !== null) {
                $q->havingRaw('avg(rate) <= ?', [$maxRate]);
            }
        });
    }

    $individualBookings = $query->paginate($perPage);

    $individualBookings->getCollection()->transform(function ($booking) {
        $stadium = $booking->stadium;
        $sport = $stadium->sportsuser ?? null;
        $teamMembersCount = $stadium?->team_members_count ?? 0;
        $remainingPlayers = $booking->remaining_players;

        return [
            'id' => $booking->id,
                                'min_players_per_team' => $booking->min_players_per_team,

            'userauth_id' => $booking->userauth_id,
            'createstadium_id' => $booking->createstadium_id,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'booking_type' => $booking->booking_type,
            'total_price' => $booking->total_price,
            'players_count' => $booking->players_count,
            'date' => $booking->date,
            'created_at' => $booking->created_at->format('H:i'),
            'remaining_players' => $remainingPlayers,
            'player_price' => $teamMembersCount > 0 ? round($booking->total_price / $teamMembersCount, 2) : 0,
            'stadium_name' => $stadium?->name,
            'stadium_location' => $stadium?->location,
            'stadium_image_url' => $stadium && $stadium->image ? asset($stadium->image) : null,
            'stadium_team_members_count' => $teamMembersCount,
            'average_rate' => round($stadium->average_rate ?? 0, 2),
            'ratings_count' => $stadium && $stadium->rates ? $stadium->rates->count() : 0,
            'sport_id' => $sport?->id,
            'sportname_ar' => $sport?->name_ar,
            'sportname_en' => $sport?->name_en,
        ];
    });

    return response()->json([
        'data' => $individualBookings->items(),
        'total_bookings' => $individualBookings->total(),
        'total_pages' => $individualBookings->lastPage(),
        'current_page' => $individualBookings->currentPage(),
    ]);
}

  
   public function joinIndividualBooking(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookstadia,id',
        ]);

        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['message' => 'المستخدم غير مصادق عليه'], 401);
            }

            // البحث عن المستخدم في userauths عبر رقم الهاتف
            $userauth = UserAuth::where('phone', $user->phone_number)->first();

            if (!$userauth) {
                return response()->json(['message' => 'المستخدم غير موجود في قاعدة البيانات'], 400);
            }

            // جلب بيانات الحجز الفردي
            $booking = BookStadium::where('id', $request->booking_id)
                ->where('booking_type', 'individual')
                ->firstOrFail();

            // **ضبط remaining_players إذا كان null**
            if (is_null($booking->remaining_players)) {
                $booking->remaining_players = $booking->players_count;
                $booking->save();
            }

            // **🚫 التحقق من توفر الأماكن**
            if ($booking->remaining_players <= 0) {
                return response()->json(['message' => 'الحجز ممتلئ بالفعل، لا يمكن الانضمام'], 400);
            }

            // ✅ تقليل عدد اللاعبين المتبقين فقط
            $booking->decrement('remaining_players');

            // **✅ إذا لم يتبقَ أماكن، اعتبر الحجز مكتملًا**
            if ($booking->remaining_players == 0) {
                $booking->status = 'completed';
                $booking->save();
            }

            return response()->json([
                'message' => 'تم تسجيلك في الحجز بنجاح!',
                'remaining_players' => $booking->remaining_players, // عدد اللاعبين المتبقين بعد التسجيل
                'status' => $booking->status // حالة الحجز بعد التحديث
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }



public function getTeamBookings(Request $request)
{
    $perPage = $request->input('per_page', 10);
    $sportId = $request->input('sport_id');
    $location = $request->input('stadium_location');
    $minRate = $request->input('min_rate');
    $maxRate = $request->input('max_rate');

   $query = BookStadium::with([
    'stadium' => function ($query) {
        $query->select('id', 'name', 'location', 'image', 'team_members_count', 'sportsuser_id')
              ->withAvg('rates as average_rate', 'rate');
    },
    'stadium.sportsuser' => function ($query) {
        $query->select('id', 'name_ar', 'name_en');
    },
    'stadium.rates',
])->where('booking_type', 'team')
  ->where(function ($q) {
      $q->whereNull('remaining_teams')
        ->orWhere('remaining_teams', '>', 0);
  });



    if ($sportId) {
        $query->whereHas('stadium', function ($q) use ($sportId) {
            $q->where('sportsuser_id', $sportId);
        });
    }

    if ($location) {
        $query->whereHas('stadium', function ($q) use ($location) {
            $q->where('location', 'like', '%' . $location . '%');
        });
    }

    if ($minRate !== null || $maxRate !== null) {
        $query->whereHas('stadium.rates', function ($q) use ($minRate, $maxRate) {
            $q->select(DB::raw('avg(rate) as average_rate'))
              ->groupBy('createstadium_id')
              ->havingRaw('avg(rate) >= ?', [$minRate ?? 0]);
            if ($maxRate !== null) {
                $q->havingRaw('avg(rate) <= ?', [$maxRate]);
            }
        });
    }

    $teamBookings = $query->paginate($perPage);

    $teamBookings->getCollection()->transform(function ($booking) {
        $stadium = $booking->stadium;
        $sport = $stadium->sportsuser ?? null;

        $teamMembersCount = max($stadium?->team_members_count ?? 1, 1);
        $pricePerPlayer = round($booking->total_price / $teamMembersCount, 2);

        return [
            'id' => $booking->id,
            'min_players_per_team' => $booking->min_players_per_team,
            'userauth_id' => $booking->userauth_id,
            'createstadium_id' => $booking->createstadium_id,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'booking_type' => $booking->booking_type,
            'total_price' => $booking->total_price,
            'price_per_player' => $pricePerPlayer,
            'remaining_teams' => $booking->remaining_teams ?? 1,
            'date' => $booking->date,
            'created_at' => $booking->created_at->format('H:i'),
            'stadium_name' => $stadium?->name,
            'stadium_location' => $stadium?->location,
            'stadium_image_url' => $stadium && $stadium->image ? asset($stadium->image) : null,
            'stadium_team_members_count' => $teamMembersCount,
            'sport_id' => $sport?->id,
            'sportname_ar' => $sport?->name_ar,
            'sportname_en' => $sport?->name_en,
            'average_rate' => round($stadium->average_rate ?? 0, 2),
            'ratings_count' => $stadium && $stadium->rates ? $stadium->rates->count() : 0,
        ];
    });

    return response()->json([
        'data' => $teamBookings->items(),
        'total_bookings' => $teamBookings->total(),
        'total_pages' => $teamBookings->lastPage(),
        'current_page' => $teamBookings->currentPage(),
    ]);
}




 public function joinTeamBooking(Request $request)
{
    $request->validate([
        'booking_id' => 'required|exists:bookstadia,id',
        'players_count' => 'nullable|integer|min:1', // لو موجود يبقى فردي
    ]);

    try {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مصادق عليه'], 401);
        }

        $userauth = UserAuth::where('phone', $user->phone_number)->first();
        if (!$userauth) {
            return response()->json(['message' => 'المستخدم غير موجود'], 400);
        }

        $booking = BookStadium::where('id', $request->booking_id)
            ->where('booking_type', 'team')
            ->firstOrFail();

        $playersToAdd = 0;

        // إجمالي عدد اللاعبين المسموح به
        $totalPlayersAllowed = $booking->teams_count * $booking->min_players_per_team;

        // عدد الأماكن المتبقية الفعلية
        $availableSlots = $totalPlayersAllowed - $booking->players_count;

        // حجز فريق كامل
        if (is_null($request->players_count)) {
            if ($booking->remaining_teams <= 0) {
                return response()->json(['message' => 'لا يوجد فرق متبقية للحجز'], 400);
            }

            if ($booking->min_players_per_team > $availableSlots) {
                return response()->json([
                    'message' => "لا يمكن حجز فريق كامل، المتاح فقط: {$availableSlots} لاعب"
                ], 400);
            }

            $playersToAdd = $booking->min_players_per_team;
            $booking->players_count += $playersToAdd;
            $booking->remaining_teams -= 1;
        }
        // حجز أفراد
        else {
            $playersToAdd = $request->players_count;

            if ($playersToAdd > $availableSlots) {
                return response()->json([
                    'message' => "العدد المطلوب يتعدى العدد المتبقي المتاح للحجز، المتاح فقط: {$availableSlots} لاعب"
                ], 400);
            }

            $booking->players_count += $playersToAdd;

            // تحديث عدد الفرق المتبقية بعد الزيادة
            $completeTeams = intdiv($booking->players_count, $booking->min_players_per_team);
            $booking->remaining_teams = max($booking->teams_count - $completeTeams, 0);
        }

        // إذا اكتمل الحجز
        if ($booking->remaining_teams == 0) {
            $booking->status = 'completed';
        }

        $booking->save();

        return response()->json([
            'message' => 'تم الحجز بنجاح',
            'players_added' => $playersToAdd,
            'remaining_teams' => $booking->remaining_teams,
            'status' => $booking->status
        ]);
    } catch (\Exception $e) {
        return response()->json(['message' => 'خطأ: ' . $e->getMessage()], 500);
    }
}





public function getStadiumReservations(Request $request, $stadium_id) {
    try {
        // جلب بيانات الملعب
        $stadium = CreateStadium::findOrFail($stadium_id);

        if ($request->has('date')) {
            // التحقق من صحة تنسيق التاريخ قبل المعالجة
            try {
                $date = Carbon::createFromFormat('Y-m-d', $request->date)->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json(['message' => 'تنسيق التاريخ غير صالح، يرجى إدخال التاريخ بصيغة YYYY-MM-DD.'], 422);
            }

            // جلب جميع الحجوزات لهذا الملعب في التاريخ المحدد
            $reservations = BookStadium::where('createstadium_id', $stadium_id)
->whereDate('date', $date)
                ->with('user')
                ->get()
                ->map(function ($booking) use ($stadium) {
                    $start_time = Carbon::parse($booking->start_time);
                    $end_time = Carbon::parse($booking->end_time);
                    $total_hours = $start_time->diffInHours($end_time);

                    // أوقات النهار والليل
                    $morning_start = (int) $stadium->morning_start_time;
                    $morning_end = (int) $stadium->morning_end_time;
                    $evening_start = (int) $stadium->evening_start_time;
                    $evening_end = (int) $stadium->evening_end_time;

                    // أسعار الساعة
                    $day_price_per_hour = (float) $stadium->booking_price;
                    $night_price_per_hour = (float) $stadium->evening_extra_price_per_hour;

                    $day_hours = 0;
                    $night_hours = 0;

                    // حساب عدد الساعات النهارية والليلية
                    for ($hour = $start_time->hour; $hour < $end_time->hour; $hour++) {
                        if ($hour >= $morning_start && $hour < $morning_end) {
                            $day_hours++;
                        } else {
                            $night_hours++;
                        }
                    }

                    // حساب التكلفة
                    $day_total_price = $day_hours * $day_price_per_hour;
                    $night_total_price = $night_hours * $night_price_per_hour;
                    $total_price = $day_total_price + $night_total_price;

                    return [
                        'id' => $booking->id,
                        'userauth_id' => $booking->userauth_id,
                        'user_name' => optional($booking->user)->first_name . ' ' . optional($booking->user)->last_name,
                        'phone' => optional($booking->user)->phone,
                        'createstadium_id' => $booking->createstadium_id,
                        'start_time' => $booking->start_time,
                        'end_time' => $booking->end_time,
                        'total_hours' => $total_hours,
                        'day_hours' => $day_hours,
                        'night_hours' => $night_hours,
                        'day_price_per_hour' => $day_price_per_hour,
                        'night_price_per_hour' => $night_price_per_hour,
                        'day_total_price' => $day_total_price,
                        'night_total_price' => $night_total_price,
                        'total_price' => $total_price,
                        'booking_type' => $booking->booking_type,
                        'players_count' => $booking->players_count,
                        'teams_count' => $booking->teams_count,
                        'min_players_per_team' => $booking->min_players_per_team,
                        'created_at' => $booking->created_at,
                        'updated_at' => $booking->updated_at,
                    ];
                });

            return response()->json($reservations, 200);
        }

        // في حالة عدم إدخال تاريخ، إرجاع جميع التواريخ الفريدة
        $dates = BookStadium::where('createstadium_id', $stadium_id)
    ->pluck('date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->unique()
            ->values();

        return response()->json($dates, 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'حدث خطأ أثناء جلب البيانات: ' . $e->getMessage()
        ], 500);
    }
}



public function getIndividualBookingById(Request $request, $id)
{
    $booking = BookStadium::with([
        'stadium' => function ($query) {
            $query->select('id', 'name', 'location', 'image', 'team_members_count', 'sportsuser_id')
                  ->withAvg('rates as average_rate', 'rate');
        },
        'stadium.sportsuser' => function ($query) {
            $query->select('id', 'name_ar', 'name_en');
        },
        'stadium.rates',
    ])->where('booking_type', 'individual')
      ->find($id);

    if (!$booking) {
        return response()->json(['message' => 'الحجز غير موجود'], 404);
    }

    $stadium = $booking->stadium;
    $sport = $stadium->sportsuser ?? null;
    $teamMembersCount = $stadium?->team_members_count ?? 0;

    // استخدام القيمة المخزنة لـ remaining_players
    $remainingPlayers = $booking->remaining_players;

    $bookingData = [
        'id' => $booking->id,
        'userauth_id' => $booking->userauth_id,
        'createstadium_id' => $booking->createstadium_id,
        'start_time' => $booking->start_time,
        'end_time' => $booking->end_time,
        'booking_type' => $booking->booking_type,
        'total_price' => $booking->total_price,
        'players_count' => $booking->players_count,
        'date' => $booking->date,
        'created_at' => $booking->created_at->format('H:i'),
        'remaining_players' => $remainingPlayers,
        'player_price' => $teamMembersCount > 0 ? round($booking->total_price / $teamMembersCount, 2) : 0,

        'stadium_name' => $stadium?->name,
        'stadium_location' => $stadium?->location,
        'stadium_image_url' => $stadium && $stadium->image ? asset($stadium->image) : null,
        'stadium_team_members_count' => $teamMembersCount,

        'average_rate' => round($stadium->average_rate ?? 0, 2),
        'ratings_count' => $stadium && $stadium->rates ? $stadium->rates->count() : 0,

        'sport_id' => $sport?->id,
        'sportname_ar' => $sport?->name_ar,
        'sportname_en' => $sport?->name_en,
    ];

    return response()->json([
        'data' => $bookingData,
    ]);
}



public function getTeamBookingById(Request $request, $id)
{
    $booking = BookStadium::with([
        'stadium' => function ($query) {
            $query->select('id', 'name', 'location', 'image', 'team_members_count', 'sportsuser_id')
                  ->withAvg('rates as average_rate', 'rate');
        },
        'stadium.sportsuser' => function ($query) {
            $query->select('id', 'name_ar', 'name_en');
        },
        'stadium.rates',
    ])->where('booking_type', 'team')
      ->whereNull('players_count')
      ->find($id);

    if (!$booking) {
        return response()->json(['message' => 'الحجز غير موجود'], 404);
    }

    $stadium = $booking->stadium;
    $sport = $stadium->sportsuser ?? null;

    $teamMembersCount = max($stadium?->team_members_count ?? 1, 1);
    $pricePerPlayer = round($booking->total_price / $teamMembersCount, 2);

    $bookingData = [
        'id' => $booking->id,
        'userauth_id' => $booking->userauth_id,
        'createstadium_id' => $booking->createstadium_id,
        'start_time' => $booking->start_time,
        'end_time' => $booking->end_time,
        'booking_type' => $booking->booking_type,
        'total_price' => $booking->total_price,
        'price_per_player' => $pricePerPlayer,
        'remaining_teams' => $booking->remaining_teams ?? 1,
        'date' => $booking->date,
        'created_at' => $booking->created_at->format('H:i'),

        'stadium_name' => $stadium?->name,
        'stadium_location' => $stadium?->location,
        'stadium_image_url' => $stadium && $stadium->image ? asset($stadium->image) : null,
        'stadium_team_members_count' => $teamMembersCount,

        'sport_id' => $sport?->id,
        'sportname_ar' => $sport?->name_ar,
        'sportname_en' => $sport?->name_en,

        'average_rate' => round($stadium->average_rate ?? 0, 2),
        'ratings_count' => $stadium && $stadium->rates ? $stadium->rates->count() : 0,
    ];

    return response()->json([
        'data' => $bookingData,
    ]);
}


public function getStadiumById(Request $request, $id)
{
    try {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json(['message' => 'المستخدم غير مصادق عليه'], 401);
        }

        $stadium = CreateStadium::with(['sportsuser', 'rates', 'avilableservice'])
            ->withAvg('rates as average_rate', 'rate')
            ->find($id);

        if (!$stadium) {
            return response()->json(['message' => 'الملعب غير موجود'], 404);
        }

        $services = $stadium->avilableservice->map(function ($service) {
            return [
                'name_en' => $service->name_en,
                'name_ar' => $service->name_ar,
                'image_url' => asset('avilableservices/' . $service->image)
            ];
        });

        return response()->json([
            'data' => [
                'id' => $stadium->id,
              
                'name' => $stadium->name,
                'location' => $stadium->location,
                'image_url' => $stadium->image ? asset($stadium->image) : null,
                'team_members_count' => $stadium->team_members_count,
                'morning_start_time' => $stadium->morning_start_time,
                'morning_end_time' => $stadium->morning_end_time,
                'evening_start_time' => $stadium->evening_start_time,
                'evening_end_time' => $stadium->evening_end_time,
                'booking_price' => $stadium->booking_price,
                'evening_extra_price_per_hour' => $stadium->evening_extra_price_per_hour,
                'provider_id' => $stadium->providerauth_id,
                'sport_id' => $stadium->sportsuser?->id,
                'sportname_ar' => $stadium->sportsuser?->name_ar,
                'sportname_en' => $stadium->sportsuser?->name_en,
                'average_rate' => round($stadium->average_rate ?? 0, 2),
                'ratings_count' => $stadium->rates->count(),
                'services' => $services
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json(['message' => 'حدث خطأ: ' . $e->getMessage()], 500);
    }
}









}
