<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYsgDiscountAmountAndCashDiscountAmountToSplitOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('split_orders', function (Blueprint $table) {
            $table->double('ysg_discount_amount', 8, 2)->default(0)->after('discount_amount');
            $table->double('cash_discount_amount', 8, 2)->default(0)->after('ysg_discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('split_orders', function (Blueprint $table) {
            $table->dropColumn('ysg_discount_amount');
            $table->dropColumn('cash_discount_amount');
        });
    }
}
