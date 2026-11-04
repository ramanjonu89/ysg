<?php
namespace App\Repositories\CustomPage;

use App\Models\CustomPage\BlockContent;
use App\Repositories\AbstractRepo;

class BlockContentRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return BlockContent::class;
    }

    public function getList($params)
    {
    }

    public function getListByBlockId($id)
    {
        $query = $this->model::where('block_id', '=', $id);
        return $query->get();
    }
}
