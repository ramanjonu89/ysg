<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeSomeFieldYsgPagesTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ysg_pages_translations', function (Blueprint $table) {
            $table->longText('content')->change();
            $table->string('meta_title',255)->change();
            $table->string('meta_description',255)->change();
            $table->string('meta_keywords',255)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
