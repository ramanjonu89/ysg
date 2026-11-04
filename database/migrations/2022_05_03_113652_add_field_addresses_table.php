<?php

use App\Models\Country;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFieldAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->integer('country_id')->references("id")->on("countries")->onDelete("cascade");
            $table->integer('city_id')->references("id")->on("cities")->onDelete("cascade");
        });

        DB::table('addresses')->where(['country' => 'Singapore'])->update(['country_id' => Country::where(['name' => 'Singapore'])->first()->id]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('country_id');
            $table->dropColumn('city_id');
        });
    }
}
