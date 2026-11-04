<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateYsgPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ysg_pages', function (Blueprint $table) {
            $table->dropColumn('allow_domains');
            $table->boolean('exclusive_access')->default(true)->after('need_login');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ysg_pages', function (Blueprint $table) {
            $table->dropColumn('exclusive_access');
            $table->text('allow_domains')->nullable()->after('need_login');
        });
    }
}
