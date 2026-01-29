<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password','is_admin','location_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

     public function isAdmin()
     {
    return $this->is_admin == 1;
     }

     public function isCashier()
     {
    return $this->is_admin == 0;
     }

     public function location()
     {
     return $this->belongsTo(Location::class);
     }

     
 
}