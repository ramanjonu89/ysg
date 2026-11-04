<?php
namespace App\Repositories;

use App\Models\SellerCorporateDomain;

class SellerCorporateDomainRepository extends AbstractRepo
{
    const PER_PAGE = 15;

    public function getModel(): string
    {
        return SellerCorporateDomain::class;
    }

    /**
     * get list of Seller Corporate Domain
     * @param array $params
     * @return mixed
     */
    public function getList(array $params)
    {
        $sellerId = $params['sellerId'] ?? null;
        $domains = $params['domains'] ?? null;

        $query = $this->model::query();

        if ($sellerId != null) {
            $query->where('seller_id', $sellerId);
        }

        if ($domains) {
            $query->whereIn('domain', $domains);
        }

        return $query->get();
    }

    public function getByDomain(string $domain) {
        $query = $this->model::query();
        $query->where('domain', '=', $domain);
        return $query->get();
    }
}
