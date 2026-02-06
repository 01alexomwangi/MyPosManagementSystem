<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',     // legacy
        'role',         // new system
        'location_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /* ======================================   //mutator
       PASSWORD HANDLING
    ====================================== */
 
    // public function setPasswordAttribute($value)
    // {
    //     if ($value) {
    //         $this->attributes['password'];   //= bcrypt($value);
    //     }
    // }

    /* ======================================
       BACKWARD COMPATIBILITY
       (DO NOT REMOVE YET)
    ====================================== */

    public function isAdmin()
    {
        return $this->role === 'admin' || $this->is_admin == 1;
    }

    public function isCashier()
    {
        return $this->role === 'cashier';
    }

    /* ======================================
       NEW ROLE SYSTEM
    ====================================== */

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function hasRole(string $role)
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles)
    {
        return in_array($this->role, $roles);
    }

    /* ======================================
       RELATIONSHIPS
    ====================================== */

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
     
 
}