<?php

namespace App\Http\Controllers;

use App\Models\PointsTransfer;
use Illuminate\Http\Request;

class PointsTransferController extends Controller
{
    public function index(Request $request)
    {
        if($request->user()->role == 'user'){
            self::ok([
                'pointsTransfers' => PointsTransfer::filter([...request(['take','skip','search','sort']),'user_id' => $request->user()->id])->get(),
                'count' => PointsTransfer::filter([...request(['search', 'user_id']),'user_id' => $request->user()->id])->count()
            ]);
        }else{
            self::ok([
                'pointsTransfers' => PointsTransfer::filter(request(['take','skip','search','sort', 'user_id']))->get(),
                'count' => PointsTransfer::filter(request(['search', 'user_id']))->count()
            ]);
        }
    }

    public function create(Request $request)
    {
        self::ok(PointsTransfer::create($request->all()));
    }
}
