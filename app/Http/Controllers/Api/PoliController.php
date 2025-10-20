<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Poli;
use Illuminate\Http\Request;

class PoliController extends Controller
{
    /**
     * Get all poli
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $polis = Poli::active()->get()->map(function ($poli) {
                return [
                    'id' => $poli->id,
                    'name' => $poli->nama_poli,
                    'code' => $poli->kode_poli,
                    'is_active' => $poli->is_active
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $polis,
                'message' => 'Poli list retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'POLI_LIST_ERROR',
                    'message' => 'Failed to retrieve poli list',
                    'details' => config('app.debug') ? $e->getMessage() : null
                ]
            ], 500);
        }
    }

    /**
     * Get poli by ID
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $poli = Poli::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $poli->id,
                    'name' => $poli->nama_poli,
                    'code' => $poli->kode_poli,
                    'is_active' => $poli->is_active,
                    'created_at' => $poli->created_at,
                    'updated_at' => $poli->updated_at
                ],
                'message' => 'Poli retrieved successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'POLI_NOT_FOUND',
                    'message' => 'Poli not found'
                ]
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'POLI_ERROR',
                    'message' => 'Failed to retrieve poli',
                    'details' => config('app.debug') ? $e->getMessage() : null
                ]
            ], 500);
        }
    }
}

