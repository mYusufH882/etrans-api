<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 200,
            'message' => 'Info Simple Application v1.1.0x'
        ], 200);
    }
}
