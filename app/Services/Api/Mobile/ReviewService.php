<?php
namespace App\Services\Api\Mobile;

use App\Repositories\Api\Mobile\ReviewRepository;
use App\Services\AbstractService;

class ReviewService extends AbstractService
{
    protected $repo;
    protected $sharedService;

    public function __construct(ReviewRepository $repo)
    {
        parent::__construct($repo);
    }

    public function getList($productId, $userId, $sortBy, $showOption, $perPage, $page)
    {
        return $this->repo->list($productId, $userId, $sortBy, $showOption, $perPage, $page);
    }
}