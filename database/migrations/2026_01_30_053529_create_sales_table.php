<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
              $table->foreignId('user_id')
          ->constrained()
          ->onDelete('cascade');

           $table->foreignId('location_id')
          ->constrained()
          ->onDelete('cascade');

           $table->decimal('total', 10, 2);
           $table->decimal('paid', 10, 2);
           $table->decimal('balance', 10, 2);
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
        Schema::dropIfExists('sales');
    }
}
