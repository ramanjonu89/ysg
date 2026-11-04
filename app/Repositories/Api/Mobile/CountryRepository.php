<?php
namespace App\Repositories\Api\Mobile;

use App\Models\Country;
use App\Repositories\AbstractRepo;

class CountryRepository extends AbstractRepo
{    
    public function getModel(): string
    {
        return Country::class;
    }

    public function getList($params = [])
    {
        $query = $this->model->newQuery();

        if(!empty($params['status'])) {
            $query->where('status', $params['status']);
        }

        $query->orderBy('name', 'asc');

        return $query->get();
    }
}
