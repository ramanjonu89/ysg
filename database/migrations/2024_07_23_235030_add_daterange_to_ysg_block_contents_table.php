<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDaterangeToYsgBlockContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ysg_blocks', function (Blueprint $table) {
            $table->integer('start_date')->after('sort');
            $table->integer('end_date')->after('start_date');
        });

        Schema::table('ysg_block_contents', function (Blueprint $table) {
            $table->integer('start_date')->after('is_enabled');
            $table->integer('end_date')->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ysg_blocks', function (Blueprint $table) {
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
        });

        Schema::table('ysg_block_contents', function (Blueprint $table) {
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
        });
    }
}
