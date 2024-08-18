<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return $this->successResponse("E-Trans v1.1.1x App Available");
    }
}
