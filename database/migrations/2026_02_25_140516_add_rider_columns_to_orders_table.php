<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRiderColumnsToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {

            // 🔹 Pickup Details
            $table->decimal('pickup_latitude', 10, 7)->nullable();
            $table->decimal('pickup_longitude', 10, 7)->nullable();
            $table->text('pickup_address')->nullable();

            // 🔹 Dropoff Details
            $table->decimal('dropoff_latitude', 10, 7)->nullable();
            $table->decimal('dropoff_longitude', 10, 7)->nullable();
            $table->text('dropoff_address')->nullable();

            // 🔹 Recipient Info
            $table->string('recipient_name')->nullable();
            $table->string('recipient_mobile')->nullable();
            $table->text('delivery_notes')->nullable();

            // 🔹 Financial Separation
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->string('payment_status')->default('pending');

            // 🔹 Rider Tracking
            $table->string('rider_reference')->nullable();
            $table->string('rider_id')->nullable();
            $table->string('rider_name')->nullable();
            $table->string('rider_mobile')->nullable();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropColumn([
                'pickup_latitude',
                'pickup_longitude',
                'pickup_address',
                'dropoff_latitude',
                'dropoff_longitude',
                'dropoff_address',
                'recipient_name',
                'recipient_mobile',
                'delivery_notes',
                'subtotal',
                'payment_status',
                'rider_reference',
                'rider_id',
                'rider_name',
                'rider_mobile',
            ]);
        });
    }
}