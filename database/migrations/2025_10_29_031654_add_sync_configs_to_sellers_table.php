<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSyncConfigsToSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->boolean('sync_products_enabled')->default(true)->after('company_id');
            $table->boolean('sync_stocks_enabled')->default(true)->after('sync_products_enabled');
            $table->boolean('sync_orders_enabled')->default(true)->after('sync_stocks_enabled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn(['sync_products_enabled', 'sync_stocks_enabled', 'sync_orders_enabled']);
        });
    }
}
