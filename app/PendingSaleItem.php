<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PendingSaleItem extends Model
{
    protected $fillable = ['pending_sale_id', 'product_id', 'quantity', 'price', 'total_amount'];

   // 🔗 Each item belongs to one product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
 
     // 🔗 Each item belongs to one pending sale
    public function pendingSale()
    {
        return $this->belongsTo(PendingSale::class);
    }
}

