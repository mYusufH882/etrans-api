<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
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

        $info = [
            'cards' => [
                'jumlah_barang' => $data->jumlah_barang ?? null,
                'jumlah_customer' => $data->jumlah_customer ?? null,
                'jumlah_transaksi' => $data->jumlah_transaksi ?? null,
            ],
            'statistic' => 0
        ];

        return $this->successResponse($info, "Dashboard Views Analytic");
    }
}
