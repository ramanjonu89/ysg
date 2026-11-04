<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCorporateAccessTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('corporate_access_tokens', function (Blueprint $table) {
            $table->renameColumn('deleted_at', 'activated_at');
            $table->bigInteger('created_by')->default(0)->after('token');
            $table->bigInteger('activated_by')->default(0)->after('created_by');
            $table->dateTime('expire_at')->after('activated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('corporate_access_tokens', function (Blueprint $table) {
            $table->renameColumn('activated_at', 'deleted_at');
            $table->dropColumn('created_by');
            $table->dropColumn('activated_by');
            $table->dropColumn('expire_at');
        });
    }
}
