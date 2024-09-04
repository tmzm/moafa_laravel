<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // protected $with = ['category_products'];

    protected $guarded = [];

    public function category_products()
    {
        return $this->hasMany(CategoryProduct::class);
    }
}
