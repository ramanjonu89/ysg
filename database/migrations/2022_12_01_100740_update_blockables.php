<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBlockables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $blocks = \App\Models\CustomPage\Block::where('blockable_id', '!=', 0)->get();
        foreach ($blocks as $key => $block) {
            $entity = \App\Models\Shop::find($block->blockable_id);
            $entity->blocks()->attach($block, ['sort' => $block->sort]);

            $block->page_type = 'shop';
            $block->blockable_id = 0;
            $block->blockable_type = null;
            $block->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $blocks = \App\Models\CustomPage\Block::where('blockable_id', '!=', 0);
        foreach ($blocks as $key => $block) {
            $entity = \App\Models\Shop::find($block->blockable_id);
            $entity->blocks()->detach($block);
        }
    }
}
