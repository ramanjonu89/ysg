<?php
namespace App\Repositories\Api\Mobile;

use App\Models\UserOtp;
use App\Repositories\AbstractRepo;

class UserOtpRepository extends AbstractRepo
{
    public function getModel(): string
    {
        return UserOtp::class;
    }
}