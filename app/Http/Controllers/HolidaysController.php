<?php

namespace App\Http\Controllers;

use App\Models\Holidays;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HolidaysController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search  = $request->query('search');
        $year    = $request->query('year');

        if ($perPage > 100) { $perPage = 100; }

        $query = Holidays::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($year) {
            $query->where('date', 'like', "{$year}%");
        }

        $holidays = $query->orderBy('date', 'asc')->paginate($perPage);

        return response()->json([
            'message' => 'Berhasil mengambil data hari libur.',
            'data' => $holidays->items(),
            'meta' => [
                'current_page' => $holidays->currentPage(),
                'per_page'     => $holidays->perPage(),
                'total_data'   => $holidays->total(),
                'last_page'    => $holidays->lastPage(),
                'has_more'     => $holidays->hasMorePages(),
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d|unique:holidays,date,NULL,id,deleted_at,NULL',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $holiday = Holidays::create($request->all());

        return response()->json([
            'message' => 'Hari libur berhasil ditambahkan.',
            'data' => $holiday
        ], 201);
    }

    public function show($id)
    {
        $holiday = Holidays::find($id);

        if (!$holiday) {
            return response()->json(['message' => 'Hari libur tidak ditemukan.'], 404);
        }

        return response()->json(['data' => $holiday], 200);
    }

    public function update(Request $request, $id)
    {
        $holiday = Holidays::find($id);

        if (!$holiday) {
            return response()->json(['message' => 'Hari libur tidak ditemukan.'], 404);
        }

        $validator = Validator::make($request->all(), [

            'date' => 'required|date_format:Y-m-d|unique:holidays,date,' . $id . ',id,deleted_at,NULL',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $holiday->update($request->all());

        return response()->json([
            'message' => 'Hari libur berhasil diperbarui.',
            'data' => $holiday
        ], 200);
    }

    public function destroy($id)
    {
        $holiday = Holidays::find($id);

        if (!$holiday) {
            return response()->json(['message' => 'Hari libur tidak ditemukan.'], 404);
        }

        $holiday->delete();

        return response()->json([
            'message' => 'Hari libur berhasil dihapus.'
        ], 200);
    }
}
