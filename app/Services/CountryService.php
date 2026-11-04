<?php

namespace App\Services;

use App\Repositories\CountryRepository;

class CountryService extends AbstractService
{
    protected $repo;

    public function __construct(CountryRepository $repo)
    {
        parent::__construct($repo);
    }

    public function getList()
    {
        return $this->repo->getList();
    }
}
