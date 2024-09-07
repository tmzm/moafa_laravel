<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['user'];

    public function scopeFilter($query, array $filters){

        if($filters['search'] ?? false){

            $query->whereHas('user', fn ($query)

            => $query->where('name','like' , '%' . $filters['search'] . '%'))->orWhere('code','like' , '%' . $filters['search'] . '%');

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

        if($filters['sort'] ?? false){

            if($filters['sort'] == 'oldest'){
                
                $query->oldest();
    
            }

        }else{
            
            $query->latest();
            
        }
            
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
