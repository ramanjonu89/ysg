<?php
namespace App\Services;

use App\User;
use App\Services\CorporateAccess\ShareService;

class ReferralService
{
    protected $shareService;

    public function __construct(ShareService $shareService)
    {
        $this->shareService =  $shareService;
    }

    /**
     * @param  User  $user
     * @param  string  $recipientEmail
     * @return string
     */
    public function genRegistrationLink(User $user, string $recipientEmail): string
    {
        return $this->shareService->genRegistrationLink($user, $recipientEmail);
    }
}