<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddManagerRoleToUsersTable extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'cashier', 'manager') NOT NULL DEFAULT 'cashier'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'cashier') NOT NULL DEFAULT 'cashier'");
    }
}