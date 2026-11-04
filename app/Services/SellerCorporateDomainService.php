<?php


namespace App\Services;


use App\Repositories\CustomPage\SellerCorporateDomainRepository;
use App\Repositories\Irepo;

class SellerCorporateDomainService extends AbstractService
{
    protected $repo;

    public function __construct(SellerCorporateDomainRepository $repo)
    {
        $this->repo = $repo;
    }

    public function deleteBySellerId(int $sellerId): void
    {
        $this->repo->deleteBySellerId($sellerId);
    }

    public function removeDomainAccess($sellerIds, $domains)
    {
        $this->repo->removeDomainAccess($sellerIds, $domains);
    }

    public function addDomainAccess($sellerIds, $domains)
    {
        $this->repo->addDomainAccess($sellerIds, $domains);
    }

}