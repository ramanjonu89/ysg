<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerCorporateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seller_corporate_domains', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seller_id');
            $table->string('domain');

            $table->foreign('seller_id')
                ->references('id')->on('sellers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seller_corporate_domains');
    }
}
