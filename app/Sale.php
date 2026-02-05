<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    // Fields we can fill via mass assignment
    protected $fillable = ['user_id', 'location_id', 'total', 'paid', 'balance'];

    // One sale has many items
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Sale belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Sale belongs to a location
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function customer()
    {
    return $this->belongsTo(Customer::class, 'customer_id');
    }

}
