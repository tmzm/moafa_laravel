<?php

namespace App\Http\Controllers;

use App\Models\CouponPurchase;
use Illuminate\Http\Request;

class CouponPurchaseController extends Controller
{
    public function index(Request $request)
    {
        if($request->user()->role == 'user'){
            self::ok([
                'couponPurchases' => CouponPurchase::filter([...request(['take','skip','search','sort']),'user_id' => $request->user()->id])->get(),
                'count' => CouponPurchase::filter([...request(['search', 'user_id']),'user_id' => $request->user()->id])->count()
            ]);
        }else{
            self::ok([
                'couponPurchases' => CouponPurchase::filter(request(['take','skip','search','sort', 'user_id']))->get(),
                'count' => CouponPurchase::filter(request(['search', 'user_id']))->count()
            ]);
        }
    }
}
