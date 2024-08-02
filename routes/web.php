<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::any('{any}', function () {
    return response()->json([
        'status' => 401,
        'message' => 'Unauthorized'
    ]);
})->where('any', '.*')->middleware(['throttle:120,1', 'api.auth']);
