<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PendingSaleItem extends Model
{
    protected $fillable = ['pending_sale_id', 'product_id', 'quantity', 'price', 'total_amount'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function pendingSale()
    {
        return $this->belongsTo(PendingSale::class);
    }
}

