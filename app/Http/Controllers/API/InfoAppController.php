<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InfoAppController extends Controller
{
    public function index()
    {
        return "v1.0";
    }
}
