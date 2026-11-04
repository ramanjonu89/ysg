<?php


namespace App\Services;


use App\Repositories\EmailCorporateAccessRepository;
use App\Repositories\Irepo;

class SellerCorporateEmailService extends AbstractService
{
    public function __construct(EmailCorporateAccessRepository $repo)
    {
        $this->repo = $repo;
    }

    public function deleteBySellerId(int $sellerId): void
    {
        $this->repo->deleteBySellerId($sellerId);
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