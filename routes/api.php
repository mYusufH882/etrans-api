<?php

use App\Http\Controllers\API\BarangController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\SalesController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', AuthController::class);

Route::middleware('auth:api')->group(function() {
    Route::get('/user', [AuthController::class, 'userProfile']);

    Route::apiResource('barang', BarangController::class);
    Route::apiResource('customer', CustomerController::class);
    
    Route::get('/transactions', [SalesController::class, 'getTransaction']);
    Route::post('/transactions', [SalesController::class, 'storeTransaction']);

    Route::get('/last-transaction-number', [SalesController::class, 'getLastTransactionNumber']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
