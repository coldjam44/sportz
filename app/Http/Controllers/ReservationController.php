<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
//use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Models\userauth;
use App\Models\bookstadium;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TimeCalculationController;
use App\Models\CreateStadium;
class ReservationController extends Controller
{


    // public function myReservations(Request $request)
    // {
    //     try {
    //         $user = JWTAuth::parseToken()->authenticate();
    //         if (!$user) {
    //             return response()->json(['message' => 'المستخدم غير مصادق عليه'], 401);
    //         }

    //         // البحث عن المستخدم في جدول `userauths`
    //         $userauth = UserAuth::where('phone', $user->phone_number)->first();
    //         if (!$userauth) {
    //             return response()->json(['message' => 'المستخدم غير موجود في قاعدة البيانات'], 400);
    //         }

    //         // جلب حجوزات المستخدم
    //         $reservations = BookStadium::where('userauth_id', $userauth->id)
    //             ->orderBy('start_time', 'desc')
    //             ->get();

    //         // معالجة الحجوزات وإضافة حالة الحجز
    //         $reservations->transform(function ($reservation) {
    //             // ✅ حساب اللاعبين المتبقين إذا كان `remaining_players` غير موجود
    //             if (is_null($reservation->remaining_players)) {
    //                 $reservation->remaining_players = max(0, $reservation->players_count - $reservation->num_players_joined);
    //                 $reservation->save();
    //             }

    //             // ✅ تحديد حالة الحجز بناءً على العدد المتبقي من اللاعبين أو الفرق
    //             if (
    //                 ($reservation->booking_type == 'individual' && $reservation->remaining_players == 0) ||
    //                 ($reservation->booking_type == 'team' && $reservation->remaining_teams == 0)
    //             ) {
    //                 if ($reservation->status !== 'completed') {
    //                     $reservation->status = 'completed';
    //                     $reservation->save();
    //                 }
    //             } else {
    //                 $reservation->status = 'pending'; // اجعل الحالة "قيد الانتظار" إذا لم يكتمل الحجز بعد
    //             }

    //             // ✅ تحديث النصوص لحالة الحجز
    //             if ($reservation->status === 'cancelled') {
    //                 $reservation->status_text = "حجز ملغي";
    //             } elseif ($reservation->status === 'completed') {
    //                 $reservation->status_text = "حجز مكتمل";
    //             } else {
    //                 $reservation->status_text = "الحجز لم يكتمل بعد";
    //             }

    //             return $reservation;
    //         });

    //         // ✅ تقسيم الحجوزات إلى `current` و `past`
    //         $currentReservations = $reservations->filter(function ($res) {
    //             return $res->status === 'pending';
    //         })->values();

    //         $pastReservations = $reservations->filter(function ($res) {
    //             return $res->status === 'completed' || $res->status === 'cancelled';
    //         })->values();

    //         return response()->json([
    //             'current' => $currentReservations,
    //             'past' => $pastReservations,
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'حدث خطأ: ' . $e->getMessage()], 500);
    //     }
    // }

    //     public function myReservations(Request $request)
    // {
    //     try {
    //         $user = JWTAuth::parseToken()->authenticate();
    //         if (!$user) {
    //             return response()->json(['message' => 'المستخدم غير مصادق عليه'], 401);
    //         }

    //         // البحث عن المستخدم في userauths عبر رقم الهاتف
    //         $userauth = UserAuth::where('phone', $user->phone_number)->first();
    //         if (!$userauth) {
    //             return response()->json(['message' => 'المستخدم غير موجود في قاعدة البيانات'], 400);
    //         }

    //         // 1️⃣ جلب الحجوزات التي أنشأها المستخدم
    //         $createdReservations = BookStadium::where('userauth_id', $userauth->id);

    //         // 2️⃣ جلب الحجوزات التي انضم إليها المستخدم كفرد (بناءً على players_count و remaining_players)
    //         $individualReservations = BookStadium::where('booking_type', 'individual')
    //             ->whereRaw('players_count - remaining_players > 0');

    //         // 3️⃣ جلب الحجوزات التي انضم إليها المستخدم كفريق (بناءً على teams_count و remaining_teams)
    //         $teamReservations = BookStadium::where('booking_type', 'team')
    //             ->whereRaw('teams_count - remaining_teams > 0');

    //         // دمج جميع الحجوزات في Query واحد بدون تكرار
    //         $reservations = $createdReservations
    //             ->union($individualReservations)
    //             ->union($teamReservations)
    //             ->orderBy('start_time', 'desc')
    //             ->get();

    //         // معالجة الحجوزات وإضافة حالة الحجز
    //         $reservations->transform(function ($reservation) {
    //             // ✅ حساب اللاعبين المتبقين إذا كان `remaining_players` غير موجود
    //             if (is_null($reservation->remaining_players)) {
    //                 $reservation->remaining_players = max(0, $reservation->players_count - $reservation->num_players_joined);
    //                 $reservation->save();
    //             }

    //             // ✅ تحديد حالة الحجز بناءً على العدد المتبقي من اللاعبين أو الفرق
    //             if (
    //                 ($reservation->booking_type == 'individual' && $reservation->remaining_players == 0) ||
    //                 ($reservation->booking_type == 'team' && $reservation->remaining_teams == 0)
    //             ) {
    //                 if ($reservation->status !== 'completed') {
    //                     $reservation->status = 'completed';
    //                     $reservation->save();
    //                 }
    //             } else {
    //                 $reservation->status = 'pending'; // اجعل الحالة "قيد الانتظار" إذا لم يكتمل الحجز بعد
    //             }

    //             // ✅ تحديث النصوص لحالة الحجز
    //             if ($reservation->status === 'cancelled') {
    //                 $reservation->status_text = "حجز ملغي";
    //             } elseif ($reservation->status === 'completed') {
    //                 $reservation->status_text = "حجز مكتمل";
    //             } else {
    //                 $reservation->status_text = "الحجز لم يكتمل بعد";
    //             }

    //             return $reservation;
    //         });

    //         // ✅ تقسيم الحجوزات إلى `current` و `past`
    //         $currentReservations = $reservations->filter(function ($res) {
    //             return $res->status === 'pending';
    //         })->values();

    //         $pastReservations = $reservations->filter(function ($res) {
    //             return $res->status === 'completed' || $res->status === 'cancelled';
    //         })->values();

    //         return response()->json([
    //             'current' => $currentReservations,
    //             'past' => $pastReservations,
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'حدث خطأ: ' . $e->getMessage()], 500);
    //     }
    // }

    public function myReservations(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['message' => 'المستخدم غير مصادق عليه'], 401);
            }

            $userauth = UserAuth::where('phone', $user->phone_number)->first();
            if (!$userauth) {
                return response()->json(['message' => 'المستخدم غير موجود في قاعدة البيانات'], 400);
            }

            $bookings = BookStadium::with(['stadium', 'stadium.sportsuser', 'participants.user'])
                ->where(function ($query) use ($userauth) {
                    $query->where('userauth_id', $userauth->id) // الحجوزات اللي هو عاملها
                        ->orWhereHas('participants', function ($q) use ($userauth) {
                            $q->where('userauth_id', $userauth->id); // الحجوزات اللي انضم ليها
                        });
                })
                ->orderBy('start_time', 'desc')
                ->get();


                $results = $bookings->map(function ($reservation) use ($userauth) {
                    $stadium = $reservation->stadium;
                
                    // جلب بيانات الاستاد
                    $stadiumdata = CreateStadium::findOrFail($reservation->createstadium_id);
                
                    // تجهيز أوقات البداية والنهاية للحجز
                    $start = strtotime($reservation->start_time);
                    $end = strtotime($reservation->end_time);
                    $morning_start = strtotime($stadiumdata->morning_start_time);
                    $morning_end = strtotime($stadiumdata->morning_end_time);
                    $evening_start = strtotime($stadiumdata->evening_start_time);
                    $evening_end = strtotime($stadiumdata->evening_end_time);
                
                    // تجهيز طلب دالة حساب الوقت
                    $timeCalcRequest = new Request([
                        'selectedStartTime' => date('H:i', $start),
                        'selectedEndTime' => date('H:i', $end),
                        'startMorningTime' => date('H:i', $morning_start),
                        'endMorningTime' => date('H:i', $morning_end),
                        'startEveningTime' => date('H:i', $evening_start),
                        'endEveningTime' => date('H:i', $evening_end),
                    ]);
                
                    // استدعاء دالة حساب الوقت
                    $timeCalculationController = new TimeCalculationController();
                    $response = $timeCalculationController->calculateTime($timeCalcRequest);
                
                    // فك JSON من الرد
                    $timeCalculationData = json_decode($response->getContent(), true);
                
                    // استخراج الساعات الصباحية والمسائية
                    $morning_hours = $timeCalculationData['morningHours'] ?? 0;
                    $evening_hours = $timeCalculationData['eveningHours'] ?? 0;
                
                    $duration_hours = $morning_hours + $evening_hours;
                    $duration_text = $duration_hours . ' ساعات';
                
                    // حساب الأسعار بناءً على الساعات
                    $morning_total_price = $morning_hours * $stadium->booking_price;
                    $evening_total_price = $evening_hours * ($stadium->booking_price + ($stadium->evening_extra_price_per_hour ?? 0));
                    
                    $total_price_calculated = $morning_total_price + $evening_total_price;
                
                    // حساب اللاعبين المشاركين الكلي للحجز نفسه (نفس الملعب والتاريخ والوقت)
                    $totalJoinedPlayers = BookStadium::where('createstadium_id', $reservation->createstadium_id)
                        ->where('date', $reservation->date)
                        ->where('start_time', $reservation->start_time)
                        ->where('end_time', $reservation->end_time)
                        ->sum('players_count');
                
                    $remaining = 0;
                    $player_price = 0;
                
                    $players = $reservation->participants->map(fn($p) => trim("{$p->first_name} {$p->last_name}"));
                
                    if ($reservation->booking_type === 'individual') {
                        $remaining = max(0, $reservation->remaining_players);
                        // استعمال السعر المحسوب بدل السعر المخزن
                        $player_price = $reservation->players_count > 0
                            ? number_format($total_price_calculated / $reservation->players_count, 2)
                            : 0;
                    }
                
                    if ($reservation->booking_type === 'team') {
                        $total_players_needed = $reservation->teams_count * $reservation->min_players_per_team;
                        $remaining = max(0, $total_players_needed - $totalJoinedPlayers);
                        $player_price = $total_players_needed > 0
                            ? number_format($total_price_calculated / $total_players_needed, 2)
                            : 0;
                    }
                
                    // تحديث حالة الحجز بناءً على العدد المتبقي
                    if (
                        ($reservation->booking_type == 'individual' && $remaining == 0) ||
                        ($reservation->booking_type == 'team' && $reservation->remaining_teams == 0)
                    ) {
                        $reservation->status = 'completed';
                    } elseif ($reservation->status !== 'cancelled') {
                        $reservation->status = 'pending';
                    }
                
                    $status_text = match ($reservation->status) {
                        'cancelled' => "حجز ملغي",
                        'completed' => "حجز مكتمل",
                        default => "الحجز لم يكتمل بعد"
                    };
                
                    $data = [
                        'booking_id' => $reservation->id,
                        'booking_code' => '#' . $reservation->id,
                        'booking_type' => $reservation->booking_type,
                        'status' => $reservation->status,
                        'status_text' => $status_text,
                        'stadium_name' => $stadium->name ?? '',
                        'morning_start_time' =>  $stadium->morning_start_time ?? '',
                        'morning_end_time' =>  $stadium->morning_end_time ?? '',
                        'evening_start_time' =>  $stadium->evening_start_time ?? '',
                        'evening_end_time' =>  $stadium->evening_end_time ?? '',
                        'stadium_image' => $stadium->image ? asset($stadium->image) : '',
                        'location' => $stadium->location ?? '',
                        'duration' => $duration_text,
                        'distance' => '2.4 كم',
                        'date' => $reservation->date,
                        'start_time' => $reservation->start_time,
                        'end_time' => $reservation->end_time,
                        'morning_hours' => $morning_hours,
                        'morning_total_price' => $morning_total_price,
                        'evening_hours' => $evening_hours,
                        'evening_total_price' => $evening_total_price,
                        // تعيين السعر الكلي المحسوب
                        'total_price' => (int) $total_price_calculated,
                        'final_price' => (int) $total_price_calculated,
                        'joined_players' => (int) $totalJoinedPlayers,
                        'remaining_players' => $remaining,
                        'players' => $players,
                        'sport_id' => $stadium?->sportsuser?->id,
                        'sportname_ar' => $stadium?->sportsuser?->name_ar,
                        'sportname_en' => $stadium?->sportsuser?->name_en,
                        'average_rate' => $stadium->rates ? $stadium->rates->avg('rate') : null,
                        'ratings_count' => $stadium->rates ? $stadium->rates->count() : 0,
                        'summary' => [
                            'calculation' => 'player_price = total_price / players_count',
                            'players_count' => (int) $reservation->players_count,
                            'joined_players' => (int) $totalJoinedPlayers,
                            'remaining_players' => $remaining,
                            'morning_hours' => $morning_hours,
                            'evening_hours' => $evening_hours,
                            'morning_price_per_hour' => (float) $stadium->booking_price,
                            'evening_price_per_hour' => (float) ($stadium->booking_price + ($stadium->evening_extra_price_per_hour ?? 0)),
                            'morning_total' => $morning_total_price,
                            'evening_total' => $evening_total_price,
                            'total_price' => $total_price_calculated,
                            'player_price' => $player_price,
                            'status' => $reservation->status,
                        ],
                        'time_calculation_response' => $timeCalculationData,
                    ];
                
                    if ($reservation->booking_type == 'field') {
                        $data['price_per_hour'] = $stadium->booking_price;
                        $data['extra_price_per_hour'] = $stadium->evening_extra_price_per_hour ?? 0;
                        $data['hours'] = $duration_hours;
                    }
                
                    if ($reservation->booking_type == 'individual') {
                        $data['player_price'] = $player_price;
                    }
                
                    if ($reservation->booking_type == 'team') {
                        $data['team_price_per_player'] = $total_price_calculated;
                        $data['price_per_player'] = $player_price;
                        $data['teams_count'] = $reservation->teams_count;
                        $data['min_players_per_team'] = $reservation->min_players_per_team;
                    }
                
                    return $data;
                });
                


            // Apply filtering
            $type = $request->input('type');
            $filtered = match ($type) {
                'current' => $results->where('status', 'pending')->values(),
                'past' => $results->whereIn('status', ['completed', 'cancelled'])->values(),
                'created' => $results,
                default => $results,
            };

            // Pagination
            $perPage = $request->input('per_page', 10);
            $currentPage = LengthAwarePaginator::resolveCurrentPage();

            $paginated = new LengthAwarePaginator(
                array_values($filtered->forPage($currentPage, $perPage)->all()),
                $filtered->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return response()->json([
                $type ?? 'all' => [
                    'data' => $paginated->items(),
                    'total' => $paginated->total(),
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }




    public function cancelReservation(Request $request, $id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // البحث عن userauth_id المرتبط بالمستخدم
            $userauth = UserAuth::where('phone', $user->phone_number)->first();
            if (!$userauth) {
                return response()->json(['message' => 'المستخدم غير موجود'], 400);
            }

            // البحث عن الحجز
            $reservation = BookStadium::where('id', $id)->first();

            if (!$reservation) {
                return response()->json(['message' => 'الحجز غير موجود'], 403);
            }

            // التحقق مما إذا كان المستخدم هو صاحب الحجز أو مشارك فيه
            if ($reservation->userauth_id !== $userauth->id) {
                return response()->json(['message' => 'ليس لديك صلاحية إلغاء هذا الحجز'], 403);
            }

            // ✅ تحديث حالة الحجز إلى "ملغي"
            $reservation->status = 'cancelled';
            $reservation->cancellation_reason = $request->input('reason', 'لم يتم تحديد السبب');
            // $reservation->status_text = 'حجز ملغي';

            // ✅ تحديث remaining_players أو remaining_teams عند الإلغاء
            if ($reservation->booking_type === 'individual') {
                // زيادة عدد اللاعبين المتبقين عند إلغاء الحجز الفردي
                $reservation->remaining_players = min($reservation->players_count, $reservation->remaining_players + 1);
            } elseif ($reservation->booking_type === 'team') {
                // زيادة عدد الفرق المتبقية عند إلغاء الحجز كفريق
                $reservation->remaining_teams = min($reservation->teams_count, $reservation->remaining_teams + 1);
            }

            $reservation->save();

            return response()->json([
                'message' => 'تم إلغاء الحجز بنجاح',
                'status' => 'حجز ملغي'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }




















    public function reservationDetails($id, Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $userauth = UserAuth::where('phone', $user->phone_number)->first();
            if (!$userauth) return response()->json(['message' => 'المستخدم غير موجود'], 400);

            $bookingType = $request->input('booking_type', null);

            $reservation = BookStadium::with(['stadium', 'stadium.provider', 'participants.user'])
                ->when($bookingType, function ($query, $bookingType) {
                    return $query->where('booking_type', $bookingType);
                })
                ->find($id);

            if (!$reservation) return response()->json(['message' => 'الحجز غير موجود'], 403);

            $start = Carbon::parse($reservation->start_time);
            $end = Carbon::parse($reservation->end_time);
            $duration = $start->diffInHours($end);

            $stadium = $reservation->stadium;

            // توقيتات الصباح والمساء للملعب
            $morning_start = Carbon::parse($stadium->morning_start_time);
            $morning_end = Carbon::parse($stadium->morning_end_time);
            $evening_start = Carbon::parse($stadium->evening_start_time);
            $evening_end = Carbon::parse($stadium->evening_end_time)->addDay(); // تجاوز منتصف الليل

            // نحسب عدد الساعات في كل فترة
            $morning_hours = 0;
            $evening_hours = 0;

            $current = $start->copy();
            while ($current < $end) {
                $nextHour = $current->copy()->addHour();

                if ($current >= $morning_start && $current < $morning_end) {
                    $morning_hours++;
                } elseif ($current >= $evening_start || $current < $evening_end) {
                    $evening_hours++;
                }

                $current = $nextHour;
            }

            $morning_total_price = $morning_hours * $stadium->booking_price;
            $evening_total_price = $evening_hours * ($stadium->evening_extra_price_per_hour ?? 0);

            $data = [
                'booking_id' => $reservation->id,
                'booking_code' => '#' . $reservation->id,
                'booking_type' => $reservation->booking_type,
                'status' => $reservation->status,
                'status_text' => $reservation->status == 'completed' ? 'حجز مكتمل' : ($reservation->status == 'cancelled' ? 'حجز ملغي' : 'الحجز لم يكتمل بعد'),
                'stadium_name' => $stadium->name ?? '',
                'stadium_image' => $stadium->image ? asset('' . $stadium->image) : '',
                'location' => $stadium->location ?? '',
                'distance' => '2.4 كم',
                'date' => $reservation->date,
                'start_time' => $reservation->start_time,
                'end_time' => $reservation->end_time,
                'duration' => $duration . ' ساعات',

                // الجديد
                'morning_hours' => $morning_hours,
                'morning_total_price' => $morning_total_price,
                'evening_hours' => $evening_hours,
                'evening_total_price' => $evening_total_price,
            ];

            // حساب سعر الفرد عند الحجز من نوع 'field'
            if ($reservation->booking_type == 'field') {
                $data['price_per_hour'] = $stadium->booking_price;
                $data['extra_price_per_hour'] = $stadium->evening_extra_price_per_hour ?? 0;
                $data['hours'] = $duration;
                $data['total_price'] = $reservation->total_price + $evening_total_price;
                $data['final_price'] = $reservation->total_price + $evening_total_price;
            }

            if ($reservation->booking_type == 'individual') {
                $joined = $reservation->participants->count();
                $remaining = max(0, $reservation->players_count - $joined);

                // حساب السعر الإجمالي بناءً على ساعات الصباح والمساء
                $morning_price_per_hour = $stadium->booking_price;
                $evening_price_per_hour = $stadium->evening_extra_price_per_hour ?? 0;

                $total_price = ($morning_hours * $morning_price_per_hour) + ($evening_hours * $evening_price_per_hour);

                // حساب سعر الفرد
                if ($reservation->players_count > 0) {
                    $price_per_player = number_format($total_price / $reservation->players_count, 2);
                } else {
                    $price_per_player = 0; // إذا كان عدد اللاعبين 0
                }

                // إضافة سعر الفرد في البيانات المرسلة
                $data['player_price'] = $price_per_player;
                $data['joined_players'] = $joined;
                $data['remaining_players'] = $remaining;
                $data['players'] = $reservation->participants->map(fn($p) => $p->user->name ?? '');
            }



            // حساب سعر الفرد عند الحجز من نوع 'team'
            if ($reservation->booking_type == 'team') {
                $joined = $reservation->participants->count();
                $total_players_needed = $reservation->teams_count * $reservation->min_players_per_team;
                $remaining = max(0, $total_players_needed - $joined);

                $price_per_player = $total_players_needed > 0
                    ? number_format($reservation->total_price / $total_players_needed, 2)
                    : 0;

                $data['team_price_per_player'] = $reservation->total_price; // السعر الإجمالي
                $data['price_per_player'] = $price_per_player; // ✅ السعر للفرد الواحد
                $data['teams_count'] = $reservation->teams_count;
                $data['min_players_per_team'] = $reservation->min_players_per_team;
                $data['joined_players'] = $joined;
                $data['remaining_players'] = $remaining;
                $data['players'] = $reservation->participants->map(fn($p) => $p->user->name ?? '');
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['message' => 'خطأ: ' . $e->getMessage()], 500);
        }
    }




    public function categorizedReservations()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // البحث عن `userauth_id` المرتبط بالمستخدم
            $userauth = UserAuth::where('phone', $user->phone_number)->first();

            if (!$userauth) {
                return response()->json(['message' => 'المستخدم غير موجود'], 400);
            }

            $reservations = BookStadium::where('userauth_id', $userauth->id)
                ->orderBy('start_time', 'desc')
                ->get();

            // تحديث بيانات الحجز وإضافة معلومات عن عدد اللاعبين أو الفرق المتبقية
            $reservations->transform(function ($reservation) {
                $reservation->full_start_time = Carbon::parse($reservation->created_at->toDateString() . ' ' . $reservation->start_time);

                $reservation->remaining_players = max(0, $reservation->num_players_required - $reservation->num_players_joined);
                $reservation->remaining_teams = max(0, $reservation->num_teams_required - $reservation->num_teams_joined);

                return $reservation;
            });

            // ✅ تصنيف الحجوزات إلى `current` و `past`
            $currentReservations = $reservations->filter(function ($res) {
                return $res->full_start_time >= now() // الحجز لم يبدأ بعد
                    || ($res->booking_type == 'individual' && $res->remaining_players > 0) // لا يزال يحتاج لاعبين
                    || ($res->booking_type == 'team' && $res->remaining_teams > 0); // لا يزال يحتاج فرق
            })->values();

            $pastReservations = $reservations->filter(function ($res) {
                return $res->full_start_time < now() // الحجز انتهى
                    && !($res->booking_type == 'individual' && $res->remaining_players > 0) // لا يحتاج لاعبين
                    && !($res->booking_type == 'team' && $res->remaining_teams > 0); // لا يحتاج فرق
            })->values();

            return response()->json([
                'current' => $currentReservations,
                'past' => $pastReservations,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }
}
