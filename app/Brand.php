<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    // Allow mass assignment for 'name'
    protected $fillable = ['name'];

    
    // Optional: relationship to Product
    public function products()
    {
        return $this->hasMany(Product::class);
    }


}
