<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    protected function nowLocal(): Carbon
    {
        return Carbon::now('Asia/Kuala_Lumpur');
    }

    /**
     * POST /api/clock-in
     */
    public function clockIn(Request $request)
    {
        $user = $request->user();

        $nowLocal   = $this->nowLocal();
        $todayLocal = $nowLocal->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $todayLocal)
            ->first();

        if ($attendance && $attendance->clock_in) {
            return response()->json([
                'message' => 'Already clocked in for today.',
            ], 400);
        }

        if (! $attendance) {
            $attendance = new Attendance();
            $attendance->user_id = $user->id;
            $attendance->date = $todayLocal;
        }

        $attendance->clock_in = $nowLocal;
        $attendance->save();

        return response()->json([
            'message'    => 'Clock in recorded.',
            'attendance' => $attendance,
        ]);
    }

    /**
     * POST /api/clock-out
     */
    public function clockOut(Request $request)
    {
        $user = $request->user();

        $nowLocal   = $this->nowLocal();
        $todayLocal = $nowLocal->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $todayLocal)
            ->first();

        if (! $attendance || ! $attendance->clock_in) {
            return response()->json([
                'message' => 'You have not clocked in today.',
            ], 400);
        }

        if ($attendance->clock_out) {
            return response()->json([
                'message' => 'Already clocked out for today.',
            ], 400);
        }

        $attendance->clock_out = $nowLocal;
        $attendance->save();

        return response()->json([
            'message'    => 'Clock out recorded.',
            'attendance' => $attendance,
        ]);
    }

    /**
     * GET /api/history
     */
    public function history(Request $request)
    {
        $user = $request->user();

        $records = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function (Attendance $att) {
                return [
                    'date' => $att->date,

                    'clock_in' => $att->clock_in
                        ? Carbon::parse($att->clock_in)
                        ->timezone('Asia/Kuala_Lumpur')
                        ->format('Y-m-d H:i')
                        : null,

                    'clock_out' => $att->clock_out
                        ? Carbon::parse($att->clock_out)
                        ->timezone('Asia/Kuala_Lumpur')
                        ->format('Y-m-d H:i')
                        : null,
                ];
            });

        return response()->json([
            'data' => $records,
        ]);
    }
}
