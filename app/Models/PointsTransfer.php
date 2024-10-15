<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointsTransfer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['user'];

    public function scopeFilter($query, array $filters){

        if($filters['search'] ?? false){

            $query->whereHas('user', fn ($query)

            => $query->where('first_name','like' , '%' . $filters['search'] . '%')

            ->orWhere('last_name','like' , '%' . $filters['search'] . '%'));

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
