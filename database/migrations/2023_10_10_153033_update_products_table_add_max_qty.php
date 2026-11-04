<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductsTableAddMaxQty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('max_qty')->after('min_qty')->default(0);
            $table->timestamp('max_qty_start')->after('max_qty')->nullable();
            $table->timestamp('max_qty_end')->after('max_qty_start')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('max_qty');
            $table->dropColumn('max_qty_start');
            $table->dropColumn('max_qty_end');
        });
    }
}
