<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewReviewCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('review_review_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('review_id');
            $table->unsignedBigInteger('review_category_id');
            $table->timestamps();

            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('cascade');
            $table->foreign('review_category_id')->references('id')->on('review_categories')->onDelete('cascade');
            
            $table->unique(['review_id', 'review_category_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('review_review_categories');
    }
}

