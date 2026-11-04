<?php
namespace App\Repositories\CustomPage;

use App\Models\CustomPage\PageBlock;
use App\Repositories\AbstractRepo;

class PageBlockRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return PageBlock::class;
    }

    public function getListByPageId($id)
    {
        $query = $this->model::where('page_id', '=', $id);
        return $query->get();
    }
}
