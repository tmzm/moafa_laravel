<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * @method static byProductOrders($product)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function scopeByProductOrders($query,$product)
    {
        $query->whereHas('orders',fn($query)=>
        $query->whereHas('order_items',fn($query)=>
        $query->whereHas('product',fn($query)=>
        $query->where('id',$product->id)
        )));
    }

    public function scopeFilter($query, array $filters){

        if($filters['search'] ?? false){

            $query->where('first_name','like' , '%' . $filters['search'] . '%')

            ->orWhere('last_name','like' , '%' . $filters['search'] . '%');

        }

        if($filters['take'] ?? false){

            $query->take($filters['take']);

        }

        if($filters['skip'] ?? false){

            $query->skip($filters['skip']);

        }
            
        $query->latest();
    }

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function points_transfers()
    {
        return $this->hasMany(PointsTransfer::class);
    }

    public function sended_messages()
    {
        return $this->hasMany(Message::class,'sender_id');
    }

    public function received_messages()
    {
        return $this->hasMany(Message::class,'receiver_id');
    }

    public function points()
    {
        $deposits = $this->points_transfers()->where('type', 'deposit')
            ->sum('amount');
            
        $withdrawals = $this->points_transfers()->where('type', 'withdrawal')
            ->sum('amount');

        return $deposits - $withdrawals;
    }
}
