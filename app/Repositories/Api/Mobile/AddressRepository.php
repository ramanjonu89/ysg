<?php
namespace App\Repositories\Api\Mobile;

use App\Models\Address;
use App\Repositories\AbstractRepo;

class AddressRepository extends AbstractRepo
{    
    public function getModel(): string
    {
        return Address::class;
    }

    /**
     * get list of addresses
     * @param array $params
     * @return mixed
     */
    public function resetDefaultAddress($user_id)
    {
        return $this->model->where('user_id', $user_id)->update(['set_default' => false]);
    }
}
