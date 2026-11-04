<?php
namespace App\Repositories;

use App\Models\City;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CityRepository extends AbstractRepo
{
    
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return City::class;
    }

}
