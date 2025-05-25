<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TimeCalculationController extends Controller
{
    private function isBetween($hour, $start, $end)
    {
        if ($start <= $end) {
            return $hour >= $start && $hour < $end;
        } else {
            return $hour >= $start || $hour < ($end % 24);
        }
    }

    private function splitHours($startTime, $endTime)
    {
        $start = (int) explode(':', $startTime)[0];
        $end = (int) explode(':', $endTime)[0];
        $newEnd = $end < $start ? $end + 24 : $end;
        $hours = [];

        for ($i = $start; $i < $newEnd; $i++) {
            $hours[] = $i % 24;
        }

        return $hours;
    }

    private function getHours($startTime, $endTime, $hoursList)
    {
        $start = (int) explode(':', $startTime)[0];
        $end = (int) explode(':', $endTime)[0];
        $filtered = [];

        foreach ($hoursList as $hour) {
            if ($this->isBetween($hour, $start, $end)) {
                $filtered[] = $hour;
            }
        }

        return $filtered;
    }

    public function calculateTime(Request $request)
    {
        $validated = $request->validate([
            'selectedStartTime' => 'required|string',
            'selectedEndTime' => 'required|string',
            'startMorningTime' => 'required|string',
            'endMorningTime' => 'required|string',
            'startEveningTime' => 'required|string',
            'endEveningTime' => 'required|string',
        ]);

        $listHours = $this->splitHours($validated['selectedStartTime'], $validated['selectedEndTime']);
        $morningHours = $this->getHours($validated['startMorningTime'], $validated['endMorningTime'], $listHours);
        $eveningHours = $this->getHours($validated['startEveningTime'], $validated['endEveningTime'], $listHours);

        return response()->json([
            'startSelectedTime' => (int) explode(':', $validated['selectedStartTime'])[0],
            'endSelectedTime' => (int) explode(':', $validated['selectedEndTime'])[0],
            'hours' => count($listHours),
            'hoursList' => $listHours,
            'morningHours' => count($morningHours),
            'morningHoursList' => $morningHours,
            'eveningHours' => count($eveningHours),
            'eveningHoursList' => $eveningHours,
        ]);
    }
    
}
