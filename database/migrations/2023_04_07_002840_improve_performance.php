<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ImprovePerformance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ysg_block_contents_translation', function (Blueprint $table) {
            $table->index('ysg_block_content_id');
            $table->index('language_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('ysg_block_contents_translation', function (Blueprint $table) {
            $table->dropIndex('ysg_block_content_id');
            $table->dropIndex('language_id');
        });
    }
}
