<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\Attendances;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function monthlyReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'required|date_format:Y-m',
            'employee_id' => 'nullable|exists:employees,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $date = Carbon::parse($request->month);
        $employeeId = $request->employee_id;

        $report = Employees::select([
            'employees.id',
            'employees.code',
            'employees.name',
            'employees.position',
            DB::raw("SUM(a.status = 'Present') as present"),
            DB::raw("SUM(a.status = 'Sick') as sick"),
            DB::raw("SUM(a.status = 'Leave') as `leave`"),
            DB::raw("SUM(a.status = 'Permission') as permission"),
            DB::raw("SUM(a.status = 'Absent') as absent"),
        ])
            ->leftJoin('attendances as a', function ($join) use ($date) {
                $join->on('a.employee_id', '=', 'employees.id')
                    ->whereYear('a.date', $date->year)
                    ->whereMonth('a.date', $date->month);
            })
            ->where('employees.status', 'Active')
            ->when($employeeId, fn($q) => $q->where('employees.id', $employeeId))
            ->groupBy(
                'employees.id',
                'employees.code',
                'employees.name',
                'employees.position'
            )
            ->get();

        if ($report->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data absensi untuk bulan yang diminta.',
                'month' => $request->month,
                'data' => []
            ], 404);
        }

        return response()->json([
            'type' => 'Rekapitulasi Absensi Bulanan',
            'month' => $request->month,
            'total_employees' => $report->count(),
            'data' => $report
        ]);
    }
}
