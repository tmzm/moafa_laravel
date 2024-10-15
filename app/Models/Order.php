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

            => $query->where('first_name','like' , '%' . $filters['search'] . '%')

            ->orWhere('last_name','like' , '%' . $filters['search'] . '%'));

        }

        if($filters['user_id'] ?? false){

            $query->where('user_id', $filters['user_id']);

        }

        if($filters['coupon_id'] ?? false){

            $query->where('coupon_id', $filters['coupon_id']);

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
        return $query->with(['order_items.product', 'coupon'])->get()->map(function ($order) {
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

    protected $with = ['user','order_items','location', 'coupon', 'prescription'];

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

    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }
}
