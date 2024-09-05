<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['product','user'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter($query, array $filters){

        if($filters['search'] ?? false){

            $query->where(
                fn($query)=>
                $query
                    ->where('comment', 'like', '%' . $filters['search'] . '%')
                    ->orWhere(
                        fn($query)=>
                        $query
                            ->whereHas('product',
                                fn($query)=>$query->where('name', 'like', '%' . $filters['search'] . '%')
                            )
                    )
            );

        }

        if($filters['user_id'] ?? false){

            $query->where('user_id', $filters['user_id']);

        }

        if($filters['take'] ?? false){

            $query->take($filters['take']);

        }

        if($filters['skip'] ?? false){

            $query->skip($filters['skip']);

        }

        if($filters['sort'] ?? false){

            if($filters['sort'] == 'oldest'){
                
                $query->oldest();
    
            }

        }else{
            
            $query->latest();
            
        }

    }
}
