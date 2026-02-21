<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixOrdersStatusEnum extends Migration
{
    public function up()
    {
        // Step 1: Allow ALL possible values temporarily
        DB::statement("
            ALTER TABLE orders 
            MODIFY status ENUM(
                'pending_payment',
                'processing',
                'completed',
                'cancelled',
                'paid',
                'failed'
            ) NOT NULL DEFAULT 'pending_payment'
        ");

        // Step 2: Convert old values
        DB::statement("
            UPDATE orders 
            SET status = 'completed' 
            WHERE status = 'paid'
        ");

        DB::statement("
            UPDATE orders 
            SET status = 'cancelled' 
            WHERE status = 'failed'
        ");

        // Step 3: Remove old values permanently
        DB::statement("
            ALTER TABLE orders 
            MODIFY status ENUM(
                'pending_payment',
                'processing',
                'completed',
                'cancelled'
            ) NOT NULL DEFAULT 'pending_payment'
        ");
    }

    public function down()
    {
        // Restore old structure if rolled back
        DB::statement("
            ALTER TABLE orders 
            MODIFY status ENUM(
                'pending_payment',
                'paid',
                'failed'
            ) NOT NULL DEFAULT 'pending_payment'
        ");
    }
}