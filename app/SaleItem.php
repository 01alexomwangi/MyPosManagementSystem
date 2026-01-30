<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    // Fields we can fill via mass assignment
    protected $fillable = ['sale_id', 'product_id', 'quantity', 'price', 'subtotal'];

    // Each item belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Each item belongs to a sale
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
