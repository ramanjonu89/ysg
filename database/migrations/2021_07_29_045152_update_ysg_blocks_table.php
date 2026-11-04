<?php

use App\Models\CustomPage\Block;
use App\Repositories\CustomPage\BlockRepository;
use Illuminate\Database\Migrations\Migration;

class UpdateYsgBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $repo = new BlockRepository();
        $blocks = $repo->getList([
            'type' => Block::TYPE_SHOP_LIST,
            'perPage' => 0,
        ]);
        foreach ($blocks as $block) {
            $data = ['type' => Block::TYPE_CA_SHOP_LIST];
            $repo->update($block->id, $data);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $repo = new BlockRepository();
        $blocks = $repo->getList([
            'type' => Block::TYPE_CA_SHOP_LIST,
            'perPage' => 0,
        ]);
        foreach ($blocks as $block) {
            $data = ['type' => Block::TYPE_SHOP_LIST];
            $repo->update($block->id, $data);
        }
    }
}
