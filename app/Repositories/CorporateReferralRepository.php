<?php
namespace App\Repositories;

use App\Models\CorporateReferral;
use App\SellerCorporateDomains;
use App\Utility\DateTimeHelper;

class CorporateReferralRepository extends AbstractRepo
{
    public function getModel(): string
    {
        return CorporateReferral::class;
    }

    /**
     * @param  string  $domain
     * @return CorporateReferral|null
     */
    public function getByDomain(string $domain): ?CorporateReferral
    {
        return $this->model::where('domain', '=', $domain)->first();
    }
}
