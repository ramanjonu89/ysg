<?php
namespace App\Repositories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CountryRepository extends AbstractRepo
{
    
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return Country::class;
    }

    public function getList()
    {
        $query = $this->model->query();

        $query->where('status', Country::STATUS_ACTIVE);
        $query->orderBy('name', 'asc');

        return $query->get();
    }

}
