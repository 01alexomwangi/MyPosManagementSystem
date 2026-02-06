<?php

namespace App;

use App\Customer;
use Illuminate\Database\Eloquent\Model;

class PendingSale extends Model
{
    protected $fillable = ['customer_id', 'location_id', 'total', 'status'];

    // 🔗 One pending sale has many items
    public function items()
    {
        return $this->hasMany(PendingSaleItem::class);
    }

    // 🔗 One pending sale belongs to one customer
   public function customer()
   {
    return $this->belongsTo(Customer::class, 'customer_id');
   }

   // 🔗 One pending sale belongs to one location
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    
}

