<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingSaleItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_sale_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pending_sale_id')->constrained(); // link to pending_sales
    $table->foreignId('product_id')->constrained(); // product added to cart
    $table->integer('quantity');
    $table->decimal('price', 10, 2);
    $table->decimal('total_amount', 10, 2);
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pending_sale_items');
    }
}
