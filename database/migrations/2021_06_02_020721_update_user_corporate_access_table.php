<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserCorporateAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_corporate_access', function (Blueprint $table) {
            $table->smallInteger('user_level')->default(1)->after('user_id');
            $table->bigInteger('parent_id')->default(0)->after('user_level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_corporate_access', function (Blueprint $table) {
            $table->dropColumn('user_level');
            $table->dropColumn('parent_id');
        });
    }
}
