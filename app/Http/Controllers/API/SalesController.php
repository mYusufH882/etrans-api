<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sales;
use App\Models\Sales_Det;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesController extends Controller
{
    public function getTransaction(Request $request)
    {
        $filters = $request->all();
        $sale = Sales::query();

        // if (isset($filters['customer_name'])) {
        //     $sale->whereHas('customer', function($query) use ($filters) {
        //         $query->where('name', 'like', '%' . $filters['customer_name'] . '%');
        //     });
        // }
    
        // if (isset($filters['date_from']) && isset($filters['date_to'])) {
        //     $sale->whereBetween('tgl', [$filters['date_from'], $filters['date_to']]);
        // }

        if(isset($filters['query'])) {
            $sale->where(function($query) use ($filters) {
                $query->where('kode', 'like', '%' . $filters['query'] . '%')
                    ->orWhereHas('customer', function($q) use ($filters) {
                        $q->where('name', 'like', '%' . $filters['query'] . '%');
                    })
                    ->orWhereHas('details.barang', function($q) use ($filters) {
                        $q->where('nama', 'like', '%' . $filters['query'] . '%');
                    });
            });
        }

        $sales = $sale->with('customer', 'details.barang')->get();

        $sales->each(function($item) {
            $item->makeHidden([
                'id', 'kode', 'tgl', 'cust_id', 'total_bayar', 'created_at', 'updated_at', 'customer'
            ]);

            $item->no_transaksi = $item->kode;
            $item->tanggal = $item->tgl;
            $item->nama_customer = $item->customer->name;

            $item->details->each(function($detail) {
                $detail->makeHidden(['id', 'sales_id', 'barang_id', 'qty', 'harga_bandrol', 'diskon_pct', 'diskon_nilai', 'harga_diskon', 'barang', 'created_at', 'updated_at']);
                $detail->jumlah_barang = $detail->qty;
            });
        });

        return response()->json([
            'status' => 200,
            'message' => 'Daftar Transaksi',
            'data' => $sales
        ]);
    }

    public function storeTransaction(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'tgl' => 'required|date',
                'cust_id' => 'required|exists:m_customer,id',
                'details' => 'required|array',
                'details.*.barang_id' => 'required|exists:m_barang,id',
                'details.*.harga_bandrol' => 'required|numeric|min:0',
                'details.*.qty' => 'required|integer|min:1',
                'details.*.diskon_pct' => 'required|numeric|min:0|max:100',
                'details.*.diskon_nilai' => 'required|numeric|min:0',
                'details.*.harga_diskon' => 'required|numeric|min:0',
                'details.*.total' => 'required|numeric|min:0',
                'subtotal' => 'required|numeric|min:0',
                'diskon' => 'required|numeric|min:0',
                'ongkir' => 'required|numeric|min:0',
                'total_bayar' => 'required|numeric|min:0'
            ]);

            $sale = new Sales();
            $sale->kode = Sales::generateCode($request->tgl);
            $sale->tgl = $request->tgl;
            $sale->cust_id = $request->cust_id;
            $sale->subtotal = $request->subtotal;
            $sale->diskon = $request->diskon;
            $sale->ongkir = $request->ongkir;
            $sale->total_bayar = $request->total_bayar;
            $sale->save();

            foreach($request->details as $detail) {
                $salesDetail = new Sales_Det();
                $salesDetail->sales_id = $sale->id;
                $salesDetail->barang_id = $detail['barang_id'];
                $salesDetail->harga_bandrol = $detail['harga_bandrol'];
                $salesDetail->qty = $detail['qty'];
                $salesDetail->diskon_pct = $detail['diskon_pct'];
                $salesDetail->diskon_nilai = $detail['diskon_nilai'];
                $salesDetail->harga_diskon = $detail['harga_diskon'];
                $salesDetail->total = $detail['total'];
                $salesDetail->save();
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Transaksi berhasil dibuat.',
                'data' => $sale
            ]);
        } catch(ValidationException $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 422,
                'message' => 'Internal Server Error.',
                'error' => $e->errors()
            ], 422);
        } catch(Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'Internal Server Error.',
                'error' => $e->getMessage()
            ]);
        }
    }
}
