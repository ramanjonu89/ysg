<?php
namespace App\Services\Api\Mobile;

use App\Repositories\Api\Mobile\ProductRepository;
use App\Services\AbstractService;

class ProductService extends AbstractService
{
    protected $repo;
    protected $sharedService;

    public function __construct(\App\Services\ProductService $sharedService,ProductRepository $repo)
    {
        parent::__construct($repo);
        $this->sharedService = $sharedService;
    }

    public function getList($request, array $params)
    {
        return [];
    }

    public function buildListResponse($request, $paging): array
    {
        return [];
    }

    public function getHomeFeaturedProducts($request)
    {
        return $this->sharedService->getHomeFeaturedProducts();
    }

    public function getRelatedProducts($product)
    {
        return $this->sharedService->getRelatedProducts($product);
    }

    public function getTopSellingProductBySeller($product)
    {
        return $this->sharedService->getTopSellingProductBySeller($product);
    }
}