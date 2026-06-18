<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeesController extends Controller
{

    public function index(Request $request)
    {

        $perPage = $request->query('per_page', 10);
        $search = $request->query('search');
        $status = $request->query('status');
        $position = $request->query('position');

        if ($perPage > 100) {
            $perPage = 100;
        }

        $query = Employees::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($position) {
            $query->where('position', $position);
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'message' => 'Berhasil mengambil data pegawai.',
            'data' => $employees->items(),
            'meta' => [
                'current_page' => $employees->currentPage(),
                'total_data' => $employees->total(),
                'last_page' => $employees->lastPage(),
                'has_more' => $employees->hasMorePages(),
                'per_page' => $employees->perPage(),
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:employees,code',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|unique:employees,phone',
            'position' => 'nullable|in:Staff,Admin',
            'status' => 'nullable|in:Active,Inactive',
            'join_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employee = Employees::create($request->all());

        return response()->json([
            'message' => 'Pegawai berhasil ditambahkan.',
            'data' => $employee
        ], 201);
    }

    public function show($id)
    {
        $employee = Employees::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Data pegawai tidak ditemukan.'], 404);
        }

        return response()->json(['data' => $employee], 200);
    }

    public function update(Request $request, $id)
    {
        $employee = Employees::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Data pegawai tidak ditemukan.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:employees,code,' . $id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone' => 'required|string|unique:employees,phone,' . $id,
            'position' => 'nullable|in:Staff,Admin',
            'status' => 'nullable|in:Active,Inactive',
            'join_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employee->update($request->all());

        return response()->json([
            'message' => 'Data pegawai berhasil diperbarui.',
            'data' => $employee
        ], 200);
    }

    public function destroy($id)
    {
        $employee = Employees::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Data pegawai tidak ditemukan.'], 404);
        }

        $employee->delete();

        return response()->json([
            'message' => 'Data pegawai berhasil dihapus (Soft Delete).'
        ], 200);
    }
}
