<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KunjunganPasien;
use App\Models\Poli;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics()
    {
        try {
            $today = Carbon::today();
            $sameDayLastWeek = Carbon::today()->subDays(7);

            // Query optimized dengan groupBy
            $todayData = KunjunganPasien::select('poli_id', DB::raw('count(*) as total'))
                ->whereDate('tanggal_kunjungan', $today)
                ->groupBy('poli_id')
                ->pluck('total', 'poli_id');
            
            $lastWeekData = KunjunganPasien::select('poli_id', DB::raw('count(*) as total'))
                ->whereDate('tanggal_kunjungan', $sameDayLastWeek)
                ->groupBy('poli_id')
                ->pluck('total', 'poli_id');
            
            // Total counts
            $totalToday = $todayData->sum();
            $totalLastWeek = $lastWeekData->sum();
            $difference = $totalToday - $totalLastWeek;
            $percentageChange = $totalLastWeek > 0 
                ? round(($difference / $totalLastWeek) * 100, 2) 
                : 0;

            // Poli tersibuk
            $busiestPoliId = $todayData->sortDesc()->keys()->first();
            $busiestPoli = null;
            if ($busiestPoliId) {
                $poli = Poli::find($busiestPoliId);
                $busiestPoli = [
                    'id' => $poli->id,
                    'name' => $poli->nama_poli,
                    'code' => $poli->kode_poli,
                    'count' => $todayData->get($busiestPoliId, 0)
                ];
            }

            // Jenis kunjungan
            $stats = KunjunganPasien::select(
                    DB::raw('SUM(CASE WHEN jenis_kunjungan = \'baru\' THEN 1 ELSE 0 END) as pasien_baru'),
                    DB::raw('SUM(CASE WHEN jenis_kunjungan = \'kontrol\' THEN 1 ELSE 0 END) as pasien_kontrol')
                )
                ->whereDate('tanggal_kunjungan', $today)
                ->first();

            // Total bulanan
            $totalMonthly = KunjunganPasien::whereMonth('tanggal_kunjungan', $today->month)
                ->whereYear('tanggal_kunjungan', $today->year)
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'today' => [
                        'total' => $totalToday,
                        'date' => $today->format('Y-m-d'),
                        'date_formatted' => $today->format('d F Y')
                    ],
                    'comparison' => [
                        'last_week_total' => $totalLastWeek,
                        'last_week_date' => $sameDayLastWeek->format('Y-m-d'),
                        'difference' => $difference,
                        'percentage_change' => $percentageChange
                    ],
                    'busiest_poli' => $busiestPoli,
                    'visit_types' => [
                        'new_patients' => $stats->pasien_baru ?? 0,
                        'control_patients' => $stats->pasien_kontrol ?? 0
                    ],
                    'monthly' => [
                        'total' => $totalMonthly,
                        'month' => $today->format('F Y')
                    ]
                ],
                'message' => 'Statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'STATISTICS_ERROR',
                    'message' => 'Failed to retrieve statistics',
                    'details' => config('app.debug') ? $e->getMessage() : null
                ]
            ], 500);
        }
    }

    /**
     * Get poli comparison data
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function poliComparison()
    {
        try {
            $today = Carbon::today();
            $sameDayLastWeek = Carbon::today()->subDays(7);

            $todayData = KunjunganPasien::select('poli_id', DB::raw('count(*) as total'))
                ->whereDate('tanggal_kunjungan', $today)
                ->groupBy('poli_id')
                ->pluck('total', 'poli_id');
            
            $lastWeekData = KunjunganPasien::select('poli_id', DB::raw('count(*) as total'))
                ->whereDate('tanggal_kunjungan', $sameDayLastWeek)
                ->groupBy('poli_id')
                ->pluck('total', 'poli_id');
            
            $polis = Poli::active()->get();
            
            $poliComparison = $polis->map(function ($poli) use ($todayData, $lastWeekData) {
                $todayCount = $todayData->get($poli->id, 0);
                $lastWeekCount = $lastWeekData->get($poli->id, 0);
                $diff = $todayCount - $lastWeekCount;
                $percent = $lastWeekCount > 0 ? round(($diff / $lastWeekCount) * 100, 2) : 0;

                return [
                    'poli_id' => $poli->id,
                    'poli_name' => $poli->nama_poli,
                    'poli_code' => $poli->kode_poli,
                    'today' => $todayCount,
                    'last_week' => $lastWeekCount,
                    'difference' => $diff,
                    'percentage_change' => $percent,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $poliComparison,
                'message' => 'Poli comparison retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'COMPARISON_ERROR',
                    'message' => 'Failed to retrieve poli comparison',
                    'details' => config('app.debug') ? $e->getMessage() : null
                ]
            ], 500);
        }
    }

    /**
     * Get 7-day trend data
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function trend()
    {
        try {
            $dailyCounts = KunjunganPasien::select(
                    DB::raw('date(tanggal_kunjungan) as tanggal'),
                    DB::raw('count(*) as total')
                )
                ->whereBetween('tanggal_kunjungan', [Carbon::today()->subDays(6), Carbon::today()])
                ->groupBy(DB::raw('date(tanggal_kunjungan)'))
                ->pluck('total', 'tanggal');
            
            $trend = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dateKey = $date->format('Y-m-d');
                $trend[] = [
                    'date' => $dateKey,
                    'date_formatted' => $date->format('d M Y'),
                    'day_name' => $date->translatedFormat('l'),
                    'count' => $dailyCounts->get($dateKey, 0)
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $trend,
                'message' => 'Trend data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'TREND_ERROR',
                    'message' => 'Failed to retrieve trend data',
                    'details' => config('app.debug') ? $e->getMessage() : null
                ]
            ], 500);
        }
    }

    /**
     * Get monthly report
     * 
     * @param int $month
     * @param int $year
     * @return \Illuminate\Http\JsonResponse
     */
    public function monthlyReport($month = null, $year = null)
    {
        try {
            $month = $month ?? Carbon::now()->month;
            $year = $year ?? Carbon::now()->year;

            $monthlyData = KunjunganPasien::select(
                    'poli_id',
                    DB::raw('count(*) as total'),
                    DB::raw('SUM(CASE WHEN jenis_kunjungan = \'baru\' THEN 1 ELSE 0 END) as total_baru'),
                    DB::raw('SUM(CASE WHEN jenis_kunjungan = \'kontrol\' THEN 1 ELSE 0 END) as total_kontrol')
                )
                ->whereMonth('tanggal_kunjungan', $month)
                ->whereYear('tanggal_kunjungan', $year)
                ->groupBy('poli_id')
                ->get()
                ->keyBy('poli_id');

            $polis = Poli::active()->get();

            $report = $polis->map(function ($poli) use ($monthlyData) {
                $data = $monthlyData->get($poli->id);
                
                return [
                    'poli_id' => $poli->id,
                    'poli_name' => $poli->nama_poli,
                    'poli_code' => $poli->kode_poli,
                    'total' => $data->total ?? 0,
                    'new_patients' => $data->total_baru ?? 0,
                    'control_patients' => $data->total_kontrol ?? 0,
                ];
            });

            $grandTotal = $report->sum('total');
            $totalNew = $report->sum('new_patients');
            $totalControl = $report->sum('control_patients');

            return response()->json([
                'success' => true,
                'data' => [
                    'period' => [
                        'month' => (int) $month,
                        'year' => (int) $year,
                        'month_name' => Carbon::create($year, $month)->format('F Y')
                    ],
                    'summary' => [
                        'total_visits' => $grandTotal,
                        'total_new_patients' => $totalNew,
                        'total_control_patients' => $totalControl
                    ],
                    'poli_details' => $report
                ],
                'message' => 'Monthly report retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'MONTHLY_REPORT_ERROR',
                    'message' => 'Failed to retrieve monthly report',
                    'details' => config('app.debug') ? $e->getMessage() : null
                ]
            ], 500);
        }
    }
}

