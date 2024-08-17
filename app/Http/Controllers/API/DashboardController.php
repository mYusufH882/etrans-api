<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
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
            'cards' => $data,
            'statistic' => 0
        ];

        return response()->json([
            'status' => 200,
            'data' => $info 
        ]);
    }
}
