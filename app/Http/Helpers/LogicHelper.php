<?php

namespace App\Http\Helpers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait LogicHelper
{
    public function save_image_to_public_directory(Request $request): bool|string
    {
        try{
            if ($request->hasfile('image')) {
                $image = $request->file('image');
                $imageName = time().'_'.$request->file('image')->getBasename().'.'.$request->file('image')->getClientOriginalExtension();
                copy($image, public_path('images/' . $imageName));
                return '/images/' .  $imageName;
            }
        }catch(Exception $e){
            return '/images/placeholder.jpg';
        }
        return '/images/placeholder.jpg';
    }

    public function delete_image($image_path): void
    {
        if (File::exists($image_path)) {
            File::delete($image_path);
        }
    }

    public function check_products_quantity($orderItems): void
    {
        foreach ($orderItems as $orderItem) {
            $product = Product::find($orderItem['product_id']);
            if(!$product || ($product['is_quantity'] && $product['quantity'] < $orderItem['quantity']))
                self::unHandledError('product don`t have enough quantity');
        }
    }

    public function save_order_total_price($order): void
    {
        $totalPriceBeforeCoupon = $order->order_items->sum(function ($orderItem) {
            $product = $orderItem->product;
            $price = $product->is_offer ? $product->price * (1 - ($product->offer / 100)) : $product->price;
            return $orderItem->quantity * $price;
        });

        if ($order->coupon) {
            if ($order->coupon->discount_type == 'PERCENTAGE') {
                $totalPrice = $totalPriceBeforeCoupon * (1 - ($order->coupon->discount / 100));
            } elseif ($order->coupon->discount_type == 'FIXED') {
                $totalPrice = $totalPriceBeforeCoupon - $order->coupon->discount;
            } else {
                $totalPrice = $totalPriceBeforeCoupon;
            }
        } else {
            $totalPrice = $totalPriceBeforeCoupon;
        }

        $order->total_price = ceil($totalPrice);

        $order->save();
    }
}
