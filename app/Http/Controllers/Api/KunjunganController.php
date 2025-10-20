<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KunjunganPasien;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KunjunganController extends Controller
{
    /**
     * Get kunjungan list with pagination
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $date = $request->get('date');
            $poliId = $request->get('poli_id');

            $query = KunjunganPasien::with('poli');

            if ($date) {
                $query->whereDate('tanggal_kunjungan', $date);
            }

            if ($poliId) {
                $query->where('poli_id', $poliId);
            }

            $kunjungan = $query->orderBy('tanggal_kunjungan', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $kunjungan,
                'message' => 'Kunjungan list retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'KUNJUNGAN_LIST_ERROR',
                    'message' => 'Failed to retrieve kunjungan list',
                    'details' => config('app.debug') ? $e->getMessage() : null
                ]
            ], 500);
        }
    }

    /**
     * Get kunjungan by ID
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $kunjungan = KunjunganPasien::with('poli')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $kunjungan,
                'message' => 'Kunjungan retrieved successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'KUNJUNGAN_NOT_FOUND',
                    'message' => 'Kunjungan not found'
                ]
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'KUNJUNGAN_ERROR',
                    'message' => 'Failed to retrieve kunjungan',
                    'details' => config('app.debug') ? $e->getMessage() : null
                ]
            ], 500);
        }
    }
}

