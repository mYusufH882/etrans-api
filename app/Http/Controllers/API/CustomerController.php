<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CustomerController extends Controller
{
    use ApiResponse;
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

        return $this->successResponse($customers, 'Daftar Data Customer.');
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

            return $this->successResponse($customer, "Customer Berhasil Ditambahkan.");
        } catch (Exception $e) {
            DB::rollBack();
    
            return $this->handleApiException($e);
        }
    }

    public function show(string $id)
    {
        $customers = Customer::find($id);

        if (!$customers) {
            return $this->failedResponse("Customer Tidak Ditemukan!!!");
        } else {
            return $this->successResponse($customers, "Customer Berhasil Ditemukan.");
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

            return $this->successResponse($customer, "Customer Berhasil Diubah.");
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
            $customer = Customer::find($id);

            DB::commit();
            $customer->delete();

            return $this->successResponse($customer, "Customer Berhasil Dihapus.");
        } catch (Exception $e) {
            DB::rollBack();

            return $this->errorResponse($e->getMessage(), "Internal Server Error");
        }
    }
}
