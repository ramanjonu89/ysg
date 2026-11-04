<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSellersTable4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->boolean('active_corporate_access_code')->default(false)->after('company_id');
            $table->string('corporate_access_code', 255)->nullable()->unique()->after('active_corporate_access_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn('active_corporate_access_code');
            $table->dropColumn('corporate_access_code');
        });
    }
}
