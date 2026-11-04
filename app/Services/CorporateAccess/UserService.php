<?php
namespace App\Services\CorporateAccess;

use App\Repositories\EmailCorporateAccessRepository;
use App\Repositories\SellerCorporateDomainRepository;
use App\Repositories\UserCorporateAccessRepository;
use App\User;
use Illuminate\Support\Collection;

class UserService
{
    protected $sellerCorporateDomainRepo;
    protected $emailCorporateAccessRepo;
    protected $userCorporateAccessRepo;

    public function __construct(
        SellerCorporateDomainRepository $sellerCorporateDomainRepo,
        EmailCorporateAccessRepository $emailCorporateAccessRepo,
        UserCorporateAccessRepository $userCorporateAccessRepo
    )
    {
        $this->sellerCorporateDomainRepo = $sellerCorporateDomainRepo;
        $this->emailCorporateAccessRepo = $emailCorporateAccessRepo;
        $this->userCorporateAccessRepo = $userCorporateAccessRepo;
    }

    /**
     * Get list shop allow CA for user's email domain
     * @param  User  $user
     * @return Collection
     */
    public function getRootShops(User $user): Collection
    {
        $collection = $this->sellerCorporateDomainRepo->getByDomain($user->domain);

        return $this->filterActivatedShops($collection);
    }

    public function getRootShopsByIndividualEmail(User $user): Collection
    {
        $collection = $this->emailCorporateAccessRepo->getByEmail($user->email);

        return $this->filterActivatedShops($collection);
    }

    private function filterActivatedShops(Collection $shops) {
        if (!$shops) {
            return collect();
        }

        $result = [];
        foreach ($shops as $item) {
            $seller = $item->seller;
            if ($seller->EnableCorporateDomains) {
                $result[$seller->id] = $seller;
            }
        }

        return collect($result);
    }

    /**
     * Get list of shop was shared by other user
     * @param  User  $user
     * @return Collection
     */
    public function getCAList(User $user): Collection
    {
        $params = [
            'user_id' => $user->id,
            'not_expire' => true,
        ];
        return $this->userCorporateAccessRepo->getList($params);
    }

    /**
     * @param  User  $user
     * @return Collection
     */
    public function getActivatedCAList(User $user): Collection
    {
        $collection = $this->getCAList($user);
        return $collection->where('is_active', 1);
    }

    /**
     * @param  User  $user
     * @return Collection
     */
    public function getActivatedShops(User $user): Collection
    {
        $collection = $this->getActivatedCAList($user);
        if ($collection) {
            return $this->filterActivatedCAShops($collection);
        }
        return collect();
    }

    /**
     * @param  User  $user
     * @return Collection
     */
    public function getSharedShops(User $user): Collection
    {
        $collection = $this->getCAList($user);
        if ($collection) {
            return $this->filterActivatedCAShops($collection);
        }
        return collect();
    }

    /**
     * @param  Collection  $CAList
     * @return Collection
     */
    private function filterActivatedCAShops(Collection $CAList): Collection
    {
        $domains = $CAList->where('domain', '!=', null)->map(function ($item) {
            return $item->domain;
        });
        $corporateDomains = $this->sellerCorporateDomainRepo->getList(['domains' => $domains]);

        return $this->filterActivatedShops($corporateDomains);
    }

    /**
     * Check if user has any CorporateAccess that can be use
     * @param  User  $user
     * @return bool
     */
    public function hasAny(User $user): bool
    {
        if ($this->getRootShops($user)->count() || $this->getActivatedShops($user)->count()) {
            return true;
        }
        return false;
    }

    public function getAllAllowShops(User $user): Collection
    {
        $rootShops = $this->getRootShops($user);
        $rootShopsByEmail = $this->getRootShopsByIndividualEmail($user);
        $getActivatedShops = $this->getActivatedShops($user);
        return $rootShops->union($rootShopsByEmail)->union($getActivatedShops);
    }
}