<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUnitAndSkuColumnsVarchar128 extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('unit', 128)->change();
        });

        Schema::table('product_stocks', function (Blueprint $table) {
            $table->string('sku', 128)->change();
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('unit', 200)->change();
        });

        Schema::table('product_stocks', function (Blueprint $table) {
            $table->string('sku', 255)->change();
        });
    }
}
