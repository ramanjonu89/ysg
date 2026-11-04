<?php
namespace App\Repositories;

use App\Models\FlashDeal;

class FlashDealRepository extends AbstractRepo
{
    
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return FlashDeal::class;
    }

    public function getList($params)
    {
        $query = $this->model->query();

        $query->orderBy('created_at', 'desc');

        $perPage = $params['perPage'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }
}
