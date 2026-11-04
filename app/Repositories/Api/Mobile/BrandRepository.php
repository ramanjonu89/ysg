<?php
namespace App\Repositories\Api\Mobile;

use App\Models\Brand;
use App\Repositories\AbstractRepo;

class BrandRepository extends AbstractRepo
{
    const PER_PAGE = 15;
    
    public function getModel(): string
    {
        return Brand::class;
    }

    /**
     * get brand by id
     * @param int $id
     * @return mixed
     */
    public function get(int $id)
    {
        return $this->model::query()
            ->where('id', $id)
            ->where('status', '=', 1)
            ->first();
    }

    /**
     * get list of brands
     * @param array $params
     * @return mixed
     */
    public function getList(array $params)
    {
        $query = $this->model::query();
        $query->where('status', '=', 1);

        $query->orderBy('created_at', 'desc');

        $perPage = $params['per_page'] ?? self::PER_PAGE;
        if ($perPage != 0) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

}
