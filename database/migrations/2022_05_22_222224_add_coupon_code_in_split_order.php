<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCouponCodeInSplitOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('split_orders', function (Blueprint $table) {
            $table->string('coupon_code')->nullable();
        });

        Schema::table('orders', function(Blueprint $table) {
            $table->string('coupon_code')->nullable();
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
            $table->dropColumn('coupon_code');
        });

        Schema::table('orders', function(Blueprint $table) {
            $table->dropColumn('coupon_code');
        });
    }
}
