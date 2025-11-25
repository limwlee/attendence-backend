<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * POST /api/clock-in
     */
    public function clockIn(Request $request)
    {
        $user = $request->user();

        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($attendance && $attendance->clock_in) {
            return response()->json([
                'message' => 'Already clocked in for today.',
            ], 400);
        }

        if (! $attendance) {
            $attendance = new Attendance();
            $attendance->user_id = $user->id;
            $attendance->date = $today;
        }

        $attendance->clock_in = now();
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
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
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

        $attendance->clock_out = now();
        $attendance->save();

        return response()->json([
            'message'    => 'Clock out recorded.',
            'attendance' => $attendance,
        ]);
    }

    /**
     * GET /api/attendance
     */
    public function history(Request $request)
    {
        $user = $request->user();

        $records = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'data' => $records,
        ]);
    }
}
