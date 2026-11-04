<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryReviewCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_review_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id');
            $table->unsignedBigInteger('review_category_id');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('review_category_id')->references('id')->on('review_categories')->onDelete('cascade');
            
            $table->unique(['category_id', 'review_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_review_categories');
    }
}
