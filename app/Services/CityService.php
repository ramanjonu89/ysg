<?php

namespace App\Services;

use App\Repositories\CityRepository;

class CityService extends AbstractService
{
    public function __construct(CityRepository $repo)
    {
        parent::__construct($repo);
    }
}
