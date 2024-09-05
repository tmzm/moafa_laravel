<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * @method static byOrderItemId($id)
 * @method static byProduct($id)
 */
class Order extends Model
{
    use HasFactory;

    public function scopeFilter($query, array $filters){

        if($filters['search'] ?? false){

            $query->whereHas('user', fn ($query)

            => $query->where('name','like' , '%' . $filters['search'] . '%'));

        }

        if($filters['take'] ?? false){

            $query->take($filters['take']);

        }

        if($filters['skip'] ?? false){

            $query->skip($filters['skip']);

        }

        // if($filters['total_price'] ?? false){

        //     $query->whereBetween('amount',$filters['total_price']);

        // }

        if($filters['status'] ?? false){

            $query->where('status',$filters['status']);

        }

        if($filters['payment_status'] ?? false){

            $query->where('status',$filters['payment_status']);

        }

        if($filters['sort'] ?? false){

            if($filters['sort'] == 'oldest'){
                
                $query->oldest();
    
            }

        }else{
            
            $query->latest();
            
        }
            
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
    
    public function scopeWithTotalPrice($query)
    {
        return $query->with(['orderItems.product', 'coupon'])->get()->map(function ($order) {
            $totalPriceBeforeCoupon = $order->orderItems->sum(function ($orderItem) {
                $product = $orderItem->product;
                $price = $product->is_offer ? $product->price * ($product->offer / 100) : $product->price;
                return $orderItem->quantity * $price;
            });

            if ($order->coupon) {
                if ($order->coupon->discount_type == 'percentage') {
                    $totalPrice = $totalPriceBeforeCoupon * (1 - ($order->coupon->discount / 100));
                } elseif ($order->coupon->discount_type == 'fixed') {
                    $totalPrice = $totalPriceBeforeCoupon - $order->coupon->discount;
                } else {
                    $totalPrice = $totalPriceBeforeCoupon;
                }
            } else {
                $totalPrice = $totalPriceBeforeCoupon;
            }

            $order->total_price = $totalPrice;
            return $order;
        });
    }

    public function scopeByOrderItemId($query,$order_item_id)
    {
       $query->whereHas('order_items',fn ($query) =>
            $query->where('id',$order_item_id)
        );
    }

    public function scopeByProduct($query,$product_id)
    {
        $query->whereHas('order_items',fn($query)=>
        $query->whereHas('product',fn($query)=>
        $query->where('id',$product_id)
        )
        );
    }

    protected $guarded = [];

    protected $with = ['user','order_items','location'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
