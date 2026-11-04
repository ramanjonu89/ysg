<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGeneralSettingsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->string('color_menu_bar_area', 255)->default('#fef8f7')->after('frontend_color_hover');
            $table->string('color_menu_bar_main', 255)->default('#fdf0ed')->after('color_menu_bar_area');
            $table->string('color_menu_bar_text', 255)->default('#e62e04')->after('color_menu_bar_main');
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
            $table->dropColumn('color_menu_bar_area');
            $table->dropColumn('color_menu_bar_main');
            $table->dropColumn('color_menu_bar_text');
        });
    }
}
