<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $data = DB::table('m_barang')
            ->select(
                DB::raw('(SELECT COUNT(*) FROM m_barang) as jumlah_barang'),
                DB::raw('(SELECT COUNT(*) FROM m_customer) as jumlah_customer'),
                DB::raw('(SELECT COUNT(*) FROM t_sales) as jumlah_transaksi')
            )
            ->first();

        $statistic = DB::table('t_sales')
                        ->selectRaw("TO_CHAR(tgl, 'MM') AS month, SUM(total_bayar) AS price_month")
                        ->groupByRaw("TO_CHAR(tgl, 'MM')")
                        ->orderByRaw("MIN(tgl)")
                        ->get();

        $totalBalance = DB::table('t_sales')->sum('total_bayar');
        $previousPrice = null;

        foreach ($statistic as $index => $row) {
            if($previousPrice !== null) {
                $currentPrice = $row->price_month;
                $growthRate =  (($currentPrice - $previousPrice) / $previousPrice) * 100;
                $statistic[$index]->growth_rate = round($growthRate, 2);
            } else {
                $statistic[$index]->growth_rate = null; 
            }

            $previousPrice = $row->price_month;
        }
                
        $info = [
            'cards' => [
                'jumlah_barang' => $data->jumlah_barang ?? null,
                'jumlah_customer' => $data->jumlah_customer ?? null,
                'jumlah_transaksi' => $data->jumlah_transaksi ?? null,
            ],
            'statistic' => $statistic->isEmpty() ? null : $statistic,
            'total_balance' => $totalBalance ?? 0,
        ];

        return $this->successResponse($info, "Dashboard Views Analytic");
    }
}
