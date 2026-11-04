<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateSplitOrdersShippingType extends Migration
{
    public function up()
    {
        DB::statement("
            UPDATE split_orders 
            SET shipping_type = (
                SELECT DISTINCT od.shipping_type 
                FROM order_details od 
                WHERE od.split_order_id = split_orders.id 
                AND od.shipping_type IS NOT NULL 
                LIMIT 1
            )
            WHERE shipping_type IS NULL OR shipping_type = ''
        ");
    }

    public function down()
    {
        DB::statement("
            UPDATE split_orders 
            SET shipping_type = NULL
        ");
    }
}
