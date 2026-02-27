<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'method',
        'transaction_reference',
        'amount',
        'status',
        'gateway_response'
    ];

    protected $casts = [
        'gateway_response' => 'array'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

     
}
