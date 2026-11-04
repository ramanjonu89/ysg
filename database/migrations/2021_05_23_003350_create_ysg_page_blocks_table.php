<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYsgPageBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ysg_page_blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('sort')->nullable();
            $table->integer('page_id')->unsigned()->index();
            $table->foreign('page_id')->references('id')->on('ysg_pages')->onDelete('cascade');
            $table->integer('block_id')->unsigned()->index();
            $table->foreign('block_id')->references('id')->on('ysg_blocks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ysg_page_blocks');
    }
}
