<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     */
    public function index(Request $request)
    {
        self::get_user_orders($request);
    }

    /**
     * Show the form for creating a new resource.
     * @param Request $request
     */
    public function create(Request $request)
    {
        self::create_order_by_request($request);
    }

    /**
     * Display the specified resource.
     * @param Request $request
     * @param $order_id
     */
    public function show(Request $request,$order_id): void
    {
        $order = Order::find($order_id);

        if($order)
            self::ok($order);

        self::notFound();
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param $order_id
     */
    public function update(Request $request, $order_id): void
    {
        self::update_order_by_request_and_order($request,$order_id);
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param $order_id
     */
    public function destroy(Request $request,$order_id): void
    {
         self::delete_order($request,$order_id);
    }
}
