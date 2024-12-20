<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        if($request->user()->role == 'user'){
            self::ok([
                'coupons' => Coupon::filter([...request(['take','skip','search','sort']),'user_id' => $request->user()->id])->get(),
                'count' => Coupon::filter([...request(['search', 'user_id']),'user_id' => $request->user()->id])->count()
            ]);
        }else{
            self::ok([
                'coupons' => Coupon::filter(request(['take','skip','search','sort', 'user_id']))->get(),
                'count' => Coupon::filter(request(['search', 'user_id']))->count()
            ]);
        }
    }

    public function create(Request $request)
    {
        self::ok(
            Coupon::create([
                'user_id' => $request->user_id ?? null,
                'code' =>  $request->code,
                'price' =>  $request->price,
                'discount' => $request->discount,
                'discount_type' => $request->discount_type ?? 'PERCENTAGE'
            ])
        );
    }

    public function show($coupon_id): void
    {
        $coupon = Coupon::find($coupon_id);

        if($coupon)
            self::ok($coupon);

        self::notFound();
    }

    public function update(Request $request, $coupon_id): void
    {
        $coupon = Coupon::find($coupon_id);

        if($coupon){
            $coupon->update([
                'user_id' => $request->user_id ?? null,
                'code' => $request->code ?? null,
                'price' =>  $request->price ?? null,
                'discount' => $request->discount ?? null,
                'discount_type' => $request->discount_type ?? null
            ]);

            self::ok($coupon);
        }

        self::notFound();
    }

    public function destroy($coupon_id): void
    {
        $coupon = Coupon::find($coupon_id);

        if($coupon){
            $coupon->delete();

            self::ok();
        }

        self::notFound();
    }

    public function unique($code)
    {
        if(Coupon::firstWhere('code',$code)){
            self::ok();
        }

        self::notFound();
    }
}
