<?php
namespace App\Repositories\Api\Mobile;

use App\Models\City;
use App\Repositories\AbstractRepo;

class CityRepository extends AbstractRepo
{    
    public function getModel(): string
    {
        return City::class;
    }
}
