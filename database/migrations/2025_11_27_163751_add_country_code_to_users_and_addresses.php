<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryCodeToUsersAndAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string('country_code', 5)->after('code')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('country_code', 5)->after('postal_code')->nullable();
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->string('country_code', 5)->after('postal_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn('country_code');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('country_code');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('country_code');
        });
    }
}
