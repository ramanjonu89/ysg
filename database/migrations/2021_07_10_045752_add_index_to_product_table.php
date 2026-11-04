<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug', 255)->change();
            $table->index('unit');
            $table->index('user_id');
            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index('brand_id');
            $table->index('slug');
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->mediumText('slug')->change();
            $table->dropIndex([
                'unit',
                'user_id',
                'category_id',
                'subcategory_id',
                'brand_id',
                'slug',
                'created_at',
                'updated_at',
            ]);
        });
    }
}
