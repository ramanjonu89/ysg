<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreGeneralSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->boolean('show_css_banner')->default(true)->comment('Show the CSS banner on Menu bar');
            $table->string('css_banner')->nullable(true);
            $table->string('css_url')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn('show_css_banner');
            $table->dropColumn('css_banner');
            $table->dropColumn('css_url');
        });
    }
}
