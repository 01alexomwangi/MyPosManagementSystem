<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddRoleToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')
                  ->default('cashier')
                  ->after('is_admin');
        });

        // 🔄 Backward compatibility
        DB::table('users')->where('is_admin', 1)->update([
            'role' => 'admin'
        ]);

        DB::table('users')->where('is_admin', 0)->update([
            'role' => 'cashier'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}
