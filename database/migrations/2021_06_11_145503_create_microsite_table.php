<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMicrositeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('microsites', function (Blueprint $table) {
            $table->increments('id');

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->boolean('active')->nullable()->default(true);

            // Relationships
            $table->integer('seller_id')->index();
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('block_microsite', function(Blueprint $table) {
            $table->integer('sort')->nullable()->default(0);
            $table->integer('block_id')->unsigned()->index();
            $table->foreign('block_id')->references('id')->on('ysg_blocks')->onDelete('cascade');
            $table->integer('microsite_id')->unsigned()->index();
            $table->foreign('microsite_id')->references('id')->on('microsites')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('block_microsite');
        Schema::dropIfExists('microsites');
    }
}
