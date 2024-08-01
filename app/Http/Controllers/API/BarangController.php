<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barangQuery = Barang::query();

        // if(isset($filters['query'])) {
        //     $barangQuery->where('nama');
        // }

        $barangs = $barangQuery->get();

        return response()->json([
            'status' => 200,
            'message' => 'Daftar Data Barang',
            'data' => $barangs
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'kode' => 'required|unique:m_customer|max:10',
                'nama' => 'required|string|max:100',
                'harga' => 'required|numeric'
            ]);
    
            DB::commit();
            $barang = Barang::create($request->all());
    
            return response()->json([
                'status' => 200,
                'message' => 'Barang berhasil dibuat.',
                'data' => $barang
            ]);
        } catch(Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 500,
                'message' => 'Internal Sever Error',
                'error' => $e->getMessage()
            ]);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        try {
            $barang = Barang::find($id);

            $request->validate([
                'kode' => 'unique:m_customer|max:10',
                'nama' => 'string|max:100',
                'harga' => 'numeric'
            ]);

            DB::commit();
            $barang->update($request->all());

            return response()->json([
                'status' => 200,
                'message' => 'Barang berhasil diubah.',
                'data' => $barang
            ]);
        } catch(Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 500,
                'message' => 'Internal Sever Error',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $barang = Barang::find($id);
            if(!$barang) throw new BadRequestException('Barang not found !!!');
    
            DB::commit();
            $barang->delete();
    
            return response()->json([
                'status' => 200,
                'message' => 'Barang berhasil dihapus',
                'data' => $barang
            ]);
        } catch(Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 500,
                'message' => 'Internal Sever Error',
                'error' => $e->getMessage()
            ]);
        }
    }
}
