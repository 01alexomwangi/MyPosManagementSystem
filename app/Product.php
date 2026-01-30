<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
   protected $table = 'products';

   protected $fillable = ['product_name', 'price',
                           'alert_stock','quantity',
                           'brand_id','category_id',
                           'description','location_id',];






      public function brand()
         {
           return $this->belongsTo(Brand::class);
         }

       public function category()
          {
         return $this->belongsTo(Category::class);
          }   


          public function location()
         {
            return $this->belongsTo(Location::class);
         }

         public function saleItems()
          {
         return $this->hasMany(SaleItem::class);
          }

           //$product->saleItems  // all SaleItem rows that reference this product

}
