<?php

namespace App\Services;

use App\Repositories\UserCorporateAccessRepository;
use App\User;

class UserCorporateAccessService extends AbstractService
{
    public function __construct(UserCorporateAccessRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * @param  string  $token
     * @param  User|null  $user
     */
    public function checkValid(string $token, ?User $user)
    {

    }
}