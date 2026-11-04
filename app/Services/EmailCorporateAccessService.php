<?php

namespace App\Services;

use App\Repositories\EmailCorporateAccessRepository;

class EmailCorporateAccessService extends AbstractService
{
    public function __construct(EmailCorporateAccessRepository $repo)
    {
        $this->repo = $repo;
    }

    public function removeEmailAccess($sellerIds, $emails)
    {
        $this->repo->removeEmailAccess($sellerIds, $emails);
    }

    public function addEmailAccess($sellerIds, $emails)
    {
        $this->repo->addEmailAccess($sellerIds, $emails);
    }

}