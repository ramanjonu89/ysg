<?php
namespace App\Services\Api\Mobile;

use App\Repositories\Api\Mobile\SplitOrderRepository;
use App\Services\AbstractService;

class SplitOrderService extends AbstractService
{
    protected $repo;

    public function __construct(SplitOrderRepository $repo)
    {
        parent::__construct($repo);
    }

    public function getUserSplitOrders(int $userId, int $perPage = 15, int $page = 1, array $params = [])
    {
        return $this->repo->getByUserId($userId, $perPage, $page, $params);
    }

    public function trackUserOrders($userId, int $perPage = 15, int $page = 1, array $params = [])
    {
        return $this->repo->getByUserId($userId, $perPage, $page, $params);
    }
}

