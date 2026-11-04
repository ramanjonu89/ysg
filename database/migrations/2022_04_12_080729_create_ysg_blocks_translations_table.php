<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYsgBlocksTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ysg_blocks_translations', function (Blueprint $table) {
            $table->id();
            $table->integer('ysg_block_id');
            $table->integer('language_id');
            $table->string('title',255)->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
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
        Schema::dropIfExists('ysg_blocks_translations');
    }
}
