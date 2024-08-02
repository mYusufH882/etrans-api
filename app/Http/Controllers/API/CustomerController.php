<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customerQuery = Customer::query();

        // if(isset($filters['query'])) {
        //     $customerQuery->where('nama');
        // }

        $customers = $customerQuery->get();

        return response()->json([
            'status' => 200,
            'message' => 'Daftar Data Customer',
            'data' => $customers
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
                'name' => 'required|string|max:100',
                'telp' => 'required|string|max:20'
            ]);
    
            $data = [
                'kode' => $request->kode,
                'name' => $request->name,
                'telp' => $request->telp,
            ];
    
            DB::commit();
            $customer = Customer::create($data);

            return response()->json([
                'status' => 200,
                'message' => 'Customer berhasil ditambahkan.',
                'data' => $customer
            ]);
        }  catch(ValidationException $e) {
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
            $customer = Customer::find($id);
            
            $request->validate([
                'kode' => 'unique:m_customer|max:10',
                'name' => 'string|max:100',
                'telp' => 'string|max:20'
            ]);
    
            $data = [
                'kode' => $request->kode,
                'name' => $request->name,
                'telp' => $request->telp,
            ];

            DB::commit();
            $customer->update($data);

            return response()->json([
                'status' => 200,
                'message' => 'Customer berhasil diubah.',
                'data' => $customer
            ]);
        }  catch(ValidationException $e) {
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
            $customer = Customer::find($id);

            DB::commit();
            $customer->delete();
            
            return response()->json([
                'status' => 200,
                'message' => 'Customer berhasil dihapus.',
                'data' => $customer
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
