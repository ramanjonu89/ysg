<?php

namespace App\Services;

use App\Repositories\AddressRepository;

class AddressService extends AbstractService
{
    public function __construct(AddressRepository $repo)
    {
        parent::__construct($repo);
    }
}