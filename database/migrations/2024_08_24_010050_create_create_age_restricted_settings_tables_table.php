<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreateAgeRestrictedSettingsTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('age_restricted_settings_tables', function (Blueprint $table) {
            $table->id();
            $table->integer('age');
            $table->longtext('top_content')->nullable();
            $table->longtext('body_content')->nullable();
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
        Schema::dropIfExists('age_restricted_settings_tables');
    }
}
