<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
    ];


      public function sales()
    {
        return $this->hasMany(Sale::class, 'customer_id');
    }

     // 🔗 One customer can have many pending sales
    public function pendingSales()
    {
        return $this->hasMany(PendingSale::class, 'customer_id');
    }
}