<?php
namespace App\Services;

use App\Repositories\RegisterRepository;

class RegisterService
{
    protected $repo;

    public function __construct(RegisterRepository $registerRepository)
    {
        $this->repo =  $registerRepository;
    }

    public function createAccount(array $data)
    {
        return $this->repo->createAccount($data);
    }

    public function activeByReferralCode(array $data, $user) :bool
    {
        $referralBy = isset($data['referral_by']) ? $data['referral_by'] : [];
        return $this->repo->activeByReferralCode($referralBy, $user);
    }
}