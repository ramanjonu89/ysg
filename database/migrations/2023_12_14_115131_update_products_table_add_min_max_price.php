<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductsTableAddMinMaxPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->double('min_price')->after('unit_price')->default(0)
                ->comment('Minimum price of the product and its variants');
            $table->double('min_final_price')->after('min_price')->default(0)
            ->comment('Minimum price of the product and its variants after discount');
            $table->double('max_price')->after('min_final_price')->default(0)
                ->comment('Maximum price of the product and its variants');
            $table->double('max_final_price')->after('max_price')->default(0)
            ->comment('Maximum price of the product and its variants after discount');
        });

        Schema::table('product_stocks', function (Blueprint $table) {
            $table->double('final_price')->after('price')->default(0)
                ->comment('Price of variant after discount');
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
            $table->dropColumn('min_price');
            $table->dropColumn('min_final_price');
            $table->dropColumn('max_price');
            $table->dropColumn('max_final_price');
        });

        Schema::table('product_stocks', function (Blueprint $table) {
            $table->dropColumn('final_price');
        });
    }
}
