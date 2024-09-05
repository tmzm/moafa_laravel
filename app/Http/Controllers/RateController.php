<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function index()
    {
        self::ok([
            'rates' => Rate::filter(request(['search','take','skip','sort','user_id']))->get(),
            'count' => Rate::filter(request(['search']))->count()
        ]);
    } 
}
