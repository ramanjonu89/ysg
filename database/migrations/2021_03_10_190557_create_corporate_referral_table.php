<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorporateReferralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_referral', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('domain', 255)->unique();
            $table->integer('share_limit')->default(0);
            $table->integer('share_count')->default(0);
            $table->integer('expire_after')->default(0);
            $table->enum('expire_time_unit', ['second', 'minute', 'hour', 'day'])->default('second');
            $table->text('link');
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
        Schema::dropIfExists('referral_corporate_settings');
    }
}
