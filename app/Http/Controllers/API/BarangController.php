<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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
                'kode' => ['required','unique:m_barang,kode','max:10'],
                'nama' => ['required','string','max:100'],
                'harga' => ['required','numeric']
            ]);
    
            DB::commit();
            $barang = Barang::create($request->all());
    
            return response()->json([
                'status' => 200,
                'message' => 'Barang berhasil dibuat.',
                'data' => $barang
            ]);
        } catch (Exception $e) {
            DB::rollBack();
    
            $statusCode = $e instanceof ValidationException ? 422 : 500;
    
            return response()->json([
                'status' => $statusCode,
                'message' => $statusCode === 422 ? 'Data validation failed.' : 'Internal Server Error.',
                'error' => $statusCode === 422 ? $e->errors() : $e->getMessage()
            ], $statusCode);
        }

    }

    public function show(string $id)
    {
        $barangs = Barang::find($id);
        if(!$barangs)  {
            return response()->json([
                'status' => 400,
                'message' => 'Barang tidak ditemukan !'
            ], 401);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Barang Ditemukan.',
                'data' => $barangs
            ], 200);
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
                'kode' => ['max:10'],
                'nama' => ['string','max:100'],
                'harga' => ['numeric']
            ]);

            DB::commit();
            $barang->update($request->all());

            return response()->json([
                'status' => 200,
                'message' => 'Barang berhasil diubah.',
                'data' => $barang
            ]);
        } catch (Exception $e) {
            DB::rollBack();
    
            $statusCode = $e instanceof ValidationException ? 422 : 500;
    
            return response()->json([
                'status' => $statusCode,
                'message' => $statusCode === 422 ? 'Data validation failed.' : 'Internal Server Error.',
                'error' => $statusCode === 422 ? $e->errors() : $e->getMessage()
            ], $statusCode);
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
