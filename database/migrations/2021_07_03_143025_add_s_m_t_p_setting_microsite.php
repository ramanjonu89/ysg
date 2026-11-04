<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSMTPSettingMicrosite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('microsites', function (Blueprint $table) {
            $table->string('mail_driver')->nullable();
            $table->string('mail_host')->nullable();
            $table->string('mail_port')->nullable();
            $table->string('mail_username')->nullable();
            $table->string('mail_password')->nullable();
            $table->string('mail_encryption')->nullable();
            $table->string('mail_from_name')->nullable();
            $table->string('mail_from_address')->nullable();
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
            $table->drop('mail_driver');
            $table->drop('mail_host');
            $table->drop('mail_port');
            $table->drop('mail_username');
            $table->drop('mail_password');
            $table->drop('mail_encryption');
            $table->drop('mail_from_name');
            $table->drop('mail_from_address');
        });
    }
}
