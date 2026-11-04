<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateProductStocksTableRemoveSoftDelete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $status = DB::delete("
            DELETE FROM `product_stocks`
            WHERE `deleted_at` IS NOT NULL
            ",
            []);


        Schema::table('product_stocks', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
}
