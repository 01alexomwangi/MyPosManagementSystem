<?php

namespace App;

use App\Customer;
use Illuminate\Database\Eloquent\Model;

class PendingSale extends Model
{
    protected $fillable = ['customer_id', 'location_id', 'total', 'status'];

    public function items()
    {
        return $this->hasMany(PendingSaleItem::class);
    }

   public function customer()
{
    return $this->belongsTo(Customer::class, 'customer_id');
}

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    
}

