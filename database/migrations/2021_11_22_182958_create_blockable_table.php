<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function(Blueprint $table) {
            $table->unsignedInteger('id')->change();
        });

        Schema::table('ysg_blocks', function (Blueprint $table) {
            $table->unsignedInteger('blockable_id');
            $table->string('blockable_type');
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
            $table->dropColumn('blockable_id');
            $table->dropColumn('blockable_type');
        });
    }
}
