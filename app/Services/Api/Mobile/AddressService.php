<?php
namespace App\Services\Api\Mobile;

use App\Models\Address;
use App\Repositories\Api\Mobile\AddressRepository;
use App\Services\AbstractService;

class AddressService extends AbstractService
{
    protected $repo;

    public function __construct(AddressRepository $repo)
    {
        parent::__construct($repo);
    }

    public function resetDefaultAddress($user_id)
    {
        return $this->repo->resetDefaultAddress($user_id);
    }

    public function setDefault($user_id, Address $address)
    {
        return $this->repo->setDefault($user_id, $address);
    }
}