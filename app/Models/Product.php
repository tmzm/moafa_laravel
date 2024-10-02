<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @method static byOwnerAndProductId($product_id, $id)
 * @method static filter($filters)
 */
class Product extends Model
{
    use HasFactory;

    protected $with = ['category_products','brand'];

    protected $guarded = [];

    /**
     * @param $query
     * @param array $filters
     */
    public function scopeFilter($query, array $filters){

        if($filters['search'] ?? false){

            $query->where(
                fn($query)=>
                $query
                    ->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('meta_description', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('meta_title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('meta_subtitle', 'like', '%' . $filters['search'] . '%')
            );

        }

        if($filters['categories'] ?? false){

            $query->whereHas('category_products', fn ($query)

            => $query->whereIn('category_id', $filters['categories']));

        }

        if($filters['take'] ?? false){

            $query->take($filters['take']);

        }

        if($filters['skip'] ?? false){

            $query->skip($filters['skip']);

        }

        if($filters['price'] ?? false){

            $query->whereBetween('price',$filters['price']);

        }

        if($filters['sort'] ?? false){

            if($filters['sort'] == 'a-z'){

                $query->orderBy('name', 'asc');
    
            }
            
            if($filters['sort'] == 'prices (low first)'){
    
                $query->orderBy('price', 'asc');
    
            }
            
            if($filters['sort'] == 'prices (high first)'){
                
                $query->orderBy('price', 'desc');
    
            }

            if($filters['sort'] == 'oldest'){
                
                $query->oldest();
    
            }

        }else{
            
            $query->latest();
            
        }

    }

    public function scopeWithRate($query)
    {
        $query->withCount(['rates as rate' => function($query) {
            $query->select(DB::raw('avg(number)'));
        }]);
    }

    public function category_products()
    {
        return $this->hasMany(CategoryProduct::class);
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function rates()
    {
        return $this->hasMany(Rate::class);
    }

}
