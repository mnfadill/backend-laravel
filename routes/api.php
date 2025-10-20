<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PoliController;
use App\Http\Controllers\Api\KunjunganController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider within a group
| which is assigned the "api" middleware group.
|
*/

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()->toIso8601String()
    ]);
});

// Dashboard endpoints
Route::prefix('dashboard')->group(function () {
    Route::get('/statistics', [DashboardController::class, 'statistics']);
    Route::get('/poli-comparison', [DashboardController::class, 'poliComparison']);
    Route::get('/trend', [DashboardController::class, 'trend']);
    Route::get('/monthly/{month?}/{year?}', [DashboardController::class, 'monthlyReport']);
});

// Poli endpoints
Route::prefix('poli')->group(function () {
    Route::get('/', [PoliController::class, 'index']);
    Route::get('/{id}', [PoliController::class, 'show']);
});

// Kunjungan endpoints
Route::prefix('kunjungan')->group(function () {
    Route::get('/', [KunjunganController::class, 'index']);
    Route::get('/{id}', [KunjunganController::class, 'show']);
});

// Version info
Route::get('/version', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'version' => '1.0.0',
            'api_name' => 'RS Puri Asih API',
            'endpoints' => [
                'health' => '/api/health',
                'dashboard' => [
                    'statistics' => '/api/dashboard/statistics',
                    'poli_comparison' => '/api/dashboard/poli-comparison',
                    'trend' => '/api/dashboard/trend',
                    'monthly' => '/api/dashboard/monthly/{month}/{year}'
                ],
                'poli' => [
                    'list' => '/api/poli',
                    'detail' => '/api/poli/{id}'
                ],
                'kunjungan' => [
                    'list' => '/api/kunjungan',
                    'detail' => '/api/kunjungan/{id}'
                ]
            ]
        ]
    ]);
});

