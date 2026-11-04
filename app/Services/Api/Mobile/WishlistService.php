<?php
namespace App\Services\Api\Mobile;

use App\Repositories\Api\Mobile\WishlistRepository;
use App\Services\AbstractService;

class WishlistService extends AbstractService
{
    protected $repo;

    public function __construct(WishlistRepository $repo)
    {
        parent::__construct($repo);
    }

    public function getListByUser($userId)
    {
        return $this->repo->getListByUser($userId);
    }
}