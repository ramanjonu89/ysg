<?php
namespace App\Services\Api\Mobile;

use App\Repositories\Api\Mobile\CityRepository;
use App\Services\AbstractService;

class CityService extends AbstractService
{
    protected $repo;

    public function __construct(CityRepository $repo)
    {
        parent::__construct($repo);
    }

    public function getList($params = [])
    {
        return $this->repo->getList($params);
    }
}