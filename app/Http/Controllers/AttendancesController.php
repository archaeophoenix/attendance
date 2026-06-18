<?php

namespace App\Http\Controllers;

use App\Models\Attendances;
use App\Models\Employees;
use App\Models\Holidays;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AttendancesController extends Controller
{
    public function checkIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now()->toTimeString();

        $isHoliday = Holidays::where('date', $today)->exists();
        if ($isHoliday) {
            return response()->json(['message' => 'Hari ini adalah hari libur nasional.'], 400);
        }

        $employee = Employees::find($request->employee_id);
        if ($employee->status !== 'Active') {
            return response()->json(['message' => 'Status karyawan tidak aktif.'], 403);
        }

        $existingAttendance = Attendances::where('employee_id', $request->employee_id)
            ->where('date', $today)
            ->first();

        if ($existingAttendance) {
            return response()->json(['message' => 'Anda sudah melakukan check-in hari ini.'], 400);
        }

        $attendance = Attendances::create([
            'employee_id' => $request->employee_id,
            'date' => $today,
            'check_in' => $now,
            'status' => 'Present'
        ]);

        return response()->json([
            'message' => 'Check-in berhasil.',
            'data' => $attendance
        ], 201);
    }

    public function checkOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now()->toTimeString();

        $attendance = Attendances::where('employee_id', $request->employee_id)
            ->where('date', $today)
            ->where('status', 'Present')
            ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Anda belum melakukan check-in hari ini atau status Anda bukan Present.'], 400);
        }

        if ($attendance->check_out) {
            return response()->json(['message' => 'Anda sudah melakukan check-out hari ini.'], 400);
        }

        $attendance->update([
            'check_out' => $now
        ]);

        return response()->json([
            'message' => 'Check-out berhasil.',
            'data' => $attendance
        ], 200);
    }

    public function absence(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|in:Sick,Leave,Permission',
            'note' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $existingAttendance = Attendances::where('employee_id', $request->employee_id)
            ->where('date', $request->date)
            ->first();

        if ($existingAttendance) {
            return response()->json(['message' => 'Sudah ada data presensi/izin pada tanggal tersebut.'], 400);
        }

        $attendance = Attendances::create([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'check_in' => '00:00:00',
            'check_out' => '00:00:00',
            'status' => $request->status,
            'note' => $request->note
        ]);

        return response()->json([
            'message' => 'Pengajuan ' . $request->status . ' berhasil dicatat.',
            'data' => $attendance
        ], 201);
    }

}
