<?php 

namespace App\Traits;

use Illuminate\Validation\ValidationException;

trait ApiResponse
{
    public function successResponse($data, $message = "Operasi Berhasil!", $status = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
    }

    public function errorResponse($error, $message = "Operasi Gagal!!!", $status = 500)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $error
        ]);
    }

    public function failedResponse($message = 'Operasi Tidak Ditemukan!!!', $status = 400)
    {
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function handleApiException($e)
    {
        $statusCode = $e instanceof ValidationException ? 422 : 500;
    
        return response()->json([
            'status' => $statusCode,
            'message' => $statusCode === 422 ? 'Data validation failed.' : 'Internal Server Error.',
            'error' => $statusCode === 422 ? $e->errors() : $e->getMessage()
        ], $statusCode);
    }
}