<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYsgBlockContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ysg_block_contents', function (Blueprint $table) {
            // basic information
            $table->increments('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->text('options')->nullable();
            $table->text('image')->nullable();
            $table->integer('sort')->nullable();
            $table->string('css_class')->nullable();
            $table->string('other_text')->nullable();

            // flags
            $table->boolean('is_enabled');

            // Relationship
            $table->integer('block_id')->unsigned()->index();
            $table->foreign('block_id')->references('id')->on('ysg_blocks')->onDelete('cascade');

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
        Schema::dropIfExists('ysg_block_contents');
    }
}
