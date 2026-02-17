<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id');

            $table->string('method'); // mpesa, cash, card
            $table->string('transaction_reference')->nullable();
            $table->decimal('amount', 12, 2);

            $table->enum('status', [
                'pending',
                'success',
                'failed',
                'refunded'
            ])->default('pending');

            $table->json('gateway_response')->nullable();

            $table->timestamps();

            $table->foreign('order_id')
                  ->references('id')
                  ->on('orders')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
