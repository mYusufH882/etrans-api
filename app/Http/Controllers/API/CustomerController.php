<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

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
                'kode' => ['required','unique:m_customer,kode','max:10'],
                'name' => ['required','string','max:100'],
                'telp' => ['required','string','max:20']
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
        $customers = Customer::find($id);

        if (!$customers) {
            return response()->json([
                'status' => 400,
                'message' => 'Customer tidak ditemukan !'
            ], 401);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Customer Ditemukan.',
                'data' => $customers
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
            $customer = Customer::find($id);

            $request->validate([
                'kode' => ['string','max:10'],
                'name' => ['string','max:100'],
                'telp' => ['string','max:20']
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
            $customer = Customer::find($id);

            DB::commit();
            $customer->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Customer berhasil dihapus.',
                'data' => $customer
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'Internal Sever Error',
                'error' => $e->getMessage()
            ]);
        }
    }
}
