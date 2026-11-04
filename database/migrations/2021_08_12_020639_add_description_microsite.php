<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionMicrosite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('microsites', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->text('policy')->nullable();
            $table->text('return_policy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('microsites', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('terms_and_conditions');
            $table->dropColumn('policy');
            $table->dropColumn('return_policy');
        });
    }
}
