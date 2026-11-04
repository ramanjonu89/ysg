<?php

namespace App\Services;

use App\Repositories\CustomerProductRepository;

class CustomerProductService extends AbstractService
{
    public function __construct(CustomerProductRepository $repo)
    {
        parent::__construct($repo);
    }
}
