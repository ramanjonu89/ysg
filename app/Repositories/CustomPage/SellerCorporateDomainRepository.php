<?php
namespace App\Repositories\CustomPage;

use App\Models\SellerCorporateDomain;
use App\Repositories\AbstractRepo;

class SellerCorporateDomainRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return SellerCorporateDomain::class;
    }

    public function getList()
    {
        $query = $this->model::query();
        return $query->get();
    }

    public function deleteBySellerId(int $sellerId): void
    {
        $this->model->where('seller_id', $sellerId)->delete();
    }

    public function removeDomainAccess($sellerIds, $domains)
    {
        $this->model->whereIn('seller_id', $sellerIds)->whereIn('domain', $domains)->delete();
    }

    public function addDomainAccess($sellerIds, $domains)
    {
        foreach ($sellerIds as $id) {
            foreach ($domains as $domain) {
                $this->create([
                    'seller_id' => $id,
                    'domain' => $domain
                ]);
            }
        }
    }
}
