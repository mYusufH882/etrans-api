<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class BarangController extends Controller
{
    use ApiResponse;
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

        return $this->successResponse($barangs, 'Daftar Data Barang');
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
    
            return $this->successResponse($barang, 'Barang berhasil dibuat.');
        } catch (Exception $e) {
            DB::rollBack();
    
            return $this->handleApiException($e);
        }

    }

    public function show(string $id)
    {
        $barangs = Barang::find($id);
        if(!$barangs)  {
            return $this->failedResponse('Barang Tidak Ditemukan!!!');
        } else {
            return $this->successResponse($barangs, 'Barang Ditemukan.');
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

            return $this->successResponse($barang, 'Barang Berhasil Diubah.');
        } catch (Exception $e) {
            DB::rollBack();
    
            return $this->handleApiException($e);
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
    
            return $this->successResponse($barang, 'Barang Berhasil Dihapus.');
        } catch(Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 'Internal Server Error');
        }
    }
}
