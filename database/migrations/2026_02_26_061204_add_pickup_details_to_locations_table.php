<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPickupDetailsToLocationsTable extends Migration
{
    public function up()
    {
        Schema::table('locations', function (Blueprint $table) {

            // Store address
            $table->text('address')->nullable()->after('name');

            // GPS coordinates
            $table->decimal('latitude', 10, 7)->nullable()->after('address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');

        });
    }

    public function down()
    {
        Schema::table('locations', function (Blueprint $table) {

            $table->dropColumn([
                'address',
                'latitude',
                'longitude'
            ]);

        });
    }
}
