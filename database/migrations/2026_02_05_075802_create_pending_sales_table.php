<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_sales', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained(); // link to your customers table
    $table->foreignId('location_id')->constrained(); // branch/location
    $table->decimal('total', 10, 2);
    $table->enum('status', ['pending', 'completed'])->default('pending'); // pending until cashier completes
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
        Schema::dropIfExists('pending_sales');
    }
}
