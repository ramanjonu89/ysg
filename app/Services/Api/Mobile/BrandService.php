<?php
namespace App\Services\Api\Mobile;

use App\Repositories\Api\Mobile\BrandRepository;
use App\Services\AbstractService;
use Illuminate\Http\Request;

class BrandService extends AbstractService
{
    protected $repo;
    protected $sharedService;

    public function __construct(
        \App\Services\BrandService $sharedService,
        BrandRepository $repo
    ) {
        parent::__construct($repo);
        $this->sharedService = $sharedService;
    }

    public function getList(array $params)
    {
        return $this->repo->getList($params);
    }

    public function get($id)
    {
        return $this->repo->get($id);
    }

    public function getBrandsByShop($shop)
    {
        return $this->sharedService->getBrandsByShop($shop);
    }
}