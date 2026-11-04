<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;

class SplitOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('split_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->double('sub_total', 8, 2);
            $table->double('grand_total', 8, 2);
            $table->double('shipping_cost', 8, 2)->default(0);
            $table->string('delivery_status')->default(Order::SHIPPING_STATUS_PENDING);
            $table->double('discount_amount', 8, 2)->default(0);
            $table->string('tracking_number')->default('')->nullable();

            $table->integer('order_id')->index();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->integer('seller_id')->unsigned()->index();
            $table->foreign('seller_id')->references('id')->on('users');

            $table->timestamps();
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->string('product_name')->default('')->after('product_id');
            $table->integer('split_order_id')->unsigned()->index()->after('order_id')->nullable();
            $table->foreign('split_order_id')->references('id')->on('split_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('split_orders');
        Schema::table('order_details', function (Blueprint $table) {
            // $table->dropColumn('product_name');
        });
    }
}
