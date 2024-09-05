<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * @method static byOrderItemId($id)
 * @method static byProduct($id)
 * @method static byUser(Request $request)
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

        if($filters['total_price'] ?? false){

            $query->whereBetween('amount',$filters['total_price']);

        }

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

    public function scopeByUser($query,Request $request)
    {
        $query->where('user_id',$request->user()->id);
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
