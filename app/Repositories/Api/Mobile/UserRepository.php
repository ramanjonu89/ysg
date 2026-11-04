<?php
namespace App\Repositories\Api\Mobile;

use App\Repositories\AbstractRepo;
use App\User;

class UserRepository extends AbstractRepo
{
    public function getModel(): string
    {
        return User::class;
    }
}