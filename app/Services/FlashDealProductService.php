<?php
namespace App\Services;

use App\Repositories\FlashDealProductRepository;

class FlashDealProductService extends AbstractService
{
    protected $repo;
    public function __construct(FlashDealProductRepository $repo)
    {
        return parent::__construct($repo);
    }
}