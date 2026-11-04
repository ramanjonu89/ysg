<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBlockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ysg_blocks', function (Blueprint $table) {
            $table->string('page_type')->default('page')->comment('Page types: page, microsite');

            // Relationship
            $table->integer('ysg_page_id')->unsigned()->nullable()->index();
            $table->foreign('ysg_page_id')->references('id')->on('ysg_pages')->onDelete('cascade');

            $table->integer('microsite_id')->unsigned()->nullable()->index();
            $table->foreign('microsite_id')->references('id')->on('microsites')->onDelete('cascade');
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
            $table->dropColumn('page_type');
        });
    }
}
