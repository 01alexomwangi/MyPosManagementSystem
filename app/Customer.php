<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
    ];


      public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

     // 🔗 One customer can have many pending sales
    // public function pendingSales()
    // {
    //     return $this->hasMany(PendingSale::class, 'customer_id');
    // }
}