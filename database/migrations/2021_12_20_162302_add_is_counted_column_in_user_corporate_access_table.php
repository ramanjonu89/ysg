<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCountedColumnInUserCorporateAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_corporate_access', function (Blueprint $table) {
            $table->boolean('is_counted')->default(true)->after('is_active');
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
            $table->dropColumn('is_counted');
        });
    }
}
