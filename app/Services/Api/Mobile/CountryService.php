<?php
namespace App\Services\Api\Mobile;

use App\Repositories\Api\Mobile\CountryRepository;
use App\Services\AbstractService;

class CountryService extends AbstractService
{
    protected $repo;

    public function __construct(CountryRepository $repo)
    {
        parent::__construct($repo);
    }

    public function getList($params = [])
    {
        return $this->repo->getList($params);
    }
}