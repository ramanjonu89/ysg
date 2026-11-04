<?php
namespace App\Repositories;

use App\Models\Address;

class AddressRepository extends AbstractRepo
{
    public function getModel()
    {
        return Address::class;
    }
}