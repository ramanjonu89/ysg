<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYsgBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ysg_blocks', function (Blueprint $table) {
            // basic information
            $table->increments('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->text('image')->nullable();
            $table->string('type')->default('block');
            $table->text('options')->nullable();
            $table->integer('sort')->nullable();

            // flags
            $table->boolean('is_enabled');

            // Relationship
            // $table->integer('page_id')->unsigned()->index();
            // $table->foreign('page_id')->references('id')->on('pages');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ysg_blocks');
    }
}
