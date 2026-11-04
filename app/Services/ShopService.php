<?php
namespace App\Services;

use App\Mail\EmailManager;
use App\Models\Shop;
use App\Models\AgeRestrictedSetting;
use App\Repositories\ShopRepository;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ShopService extends AbstractService implements PageInterface
{
    protected $user;
    protected $repo;

    protected $productService;

    public function __construct(ShopRepository $repo, ProductService $productService)
    {
        $this->repo = $repo;
        $this->productService = $productService;
        parent::__construct($repo);
    }

    /**
     * get list of seller for display at front end
     * @return Collection
     */
    public function getList(): Collection
    {
        return $this->repo->getList([
            'verification_status' => 1,
            'perPage' => 0,
        ]);
    }

    public function getBySlug(string $slug) {
        return $this->repo->getBySlug($slug);
    }

    public function getBySlugWithReviews(string $slug) {
        return $this->repo->getBySlugWithReviews($slug);
    }

    public function getTotalReviews($shop): int
    {
        $products = $shop->user->products()->withCount('reviews')->get();
        $total = 0;

        foreach ($products as $key => $product) {
            $total += $product->reviews_count;
        }
        return $total;
    }

    public function getTotalRatings($shop): int
    {
        $products = $shop->user->products()->with('reviews')->get();
        $total = 0;

        foreach ($products as $key => $product) {
            $total += $product->reviews->sum('rating');
        }
        return $total;
    }

    public function getProducts($shop, $params = [])
    {
        return $this->repo->getProducts($shop, $params);
    }

    public function getActivatedBlocks($shop, $params = [])
    {
        return $shop->blocks->where('is_enabled', 1);
    }

    public function getFeaturedProducts($shop, $params = [])
    {
        $ids = explode(',', $shop->featured_products);
        $params['ids'] = $ids;
        $params['perPage'] = 0;

        $collection = $this->repo->getProducts($shop, $params)->keyBy('id');
        foreach ($ids as $index => $id) {
            if (isset($collection[$id])) {
                $ids[$index] = $collection[$id];
            } else unset($ids[$index]);
        }

        return $ids;
    }

    /**
     * Add product id to list of feature product
     * @param $shop
     * @param $addIds
     * @return bool
     */
    public function addFeaturedProduct($shop, $addIds): bool
    {
        $currentIds = [];
        if ($shop->featured_products) {
            $currentIds = explode(',', trim($shop->featured_products));
            foreach ($addIds as $newId) {
                if (!in_array($newId, $currentIds)) {
                    $currentIds[] = $newId;
                }
            }
        }

        if (empty($currentIds)) {
            $currentIds = $addIds;
        }
        $data['featured_products'] = implode(',', $currentIds);
        $this->update($shop->id, $data);

        return true;
    }

    /**
     * Remove product id to list of feature product
     * @param $shop
     * @param $productId
     * @return bool
     */
    public function removeFeaturedProduct($shop, $productId): bool
    {
        $ids = explode(',', $shop->featured_products);
        foreach ($ids as $index => $id) {
            if ($productId == $id) {
                unset($ids[$index]);
                break;
            }
        }
        $data['featured_products'] = implode(',', $ids);
        $this->update($shop->id, $data);
        return true;
    }

    /**
     * @param  Shop  $shop
     * @return bool
     */
    public function isPrivate(Shop $shop): bool
    {
        return $shop->companyType == User::COMPANY_TYPE_PRIVATE_EVENT ;
    }

    /**
     * Check if shop still in private or not
     * @param  Shop  $shop
     * @return bool
     */
    public function isInPrivateEvent(Shop $shop): bool
    {
        if ($this->isPrivate($shop)) {
            $user = $shop->user;
            $startDate = Carbon::parse($user->company_start_date)->startOfDay();
            $endDate = Carbon::parse($user->company_end_date)->endOfDay();
            $now = Carbon::now();
            if ($startDate->lt($now) && $endDate->gt($now) ) {
                return true;
            }
        }
        return false;
    }

    public function isPendingPrivateEvent(Shop $shop): bool
    {
        $startDate = Carbon::parse($shop->user->company_start_date)->startOfDay();
        $now = Carbon::now();
        return $this->isPrivate($shop) && $startDate->gt($now);
    }

    public function isExpiredPrivateEvent(Shop $shop): bool
    {
        $endDate = Carbon::parse($shop->user->company_end_date)->endOfDay();
        $now = Carbon::now();
        return $this->isPrivate($shop) && $now->gt($endDate);
    }

    /**
     * @param  Request  $request
     * @return Shop|null
     */
    public function getShopFromRoute(Request $request): ?Shop
    {
        $shop = null;
        $routerName = $request->route()->getName();
        if ($routerName == 'product') {
            if ($product = $this->productService->getBySlug($request->slug)) {
                $shop = $product->user->shop;
            }
        } else {
            $shop = $this->getBySlug($request->slug);
        }

        return $shop;
    }

    public function updateBlockIndex($id, $data): bool
    {
        $shop = $this->get($id);
        foreach ($shop->blocks as $item) {
            if (isset($data[$item->id])) {
                $shop->blocks()->detach($item);
                $shop->blocks()->attach($item, ['sort' => $data[$item->id]]);
            }
        }
        return true;
    }

    public function getSelectOptionsArray() {
        return $this->repo->getSelectOptionsArray();
    }

    public function sendMailToManagerSellerRegistration ($data) {
        $mailData['view'] = 'emails.newsletter';
        $mailData['subject'] = "Seller requested";
        $mailData['from'] = env('MAIL_USERNAME');
        $mailData['content'] = "Hello Admin, Please check admin panel for seller registration request";
        $mailData['name'] = $data['name'];
        $mailData['company_name'] = $data['company_name'];
        $mailData['phone'] = $data['phone'];
        $mailData['email'] = $data['email'];
        $mailData['website'] = $data['website'];
        // Mail::to('shizku@gmail.com')->queue(new EmailManager($mailData));
        Mail::to('cs@ysg.com.sg')->queue(new EmailManager($mailData));
    }

    public function getListForCoupon()
    {
        $params = [
            'verification_status' => 1,
            'perPage' => 0,
        ];

        return $this->repo->getListForCoupon($params);
    }

    public function isShopAdult($categories) {
        foreach ($categories as $categoty) {
            if ($categoty->is_age_restricted === AgeRestrictedSetting::TWENTYONE_AND_ABOVE) {
                return true;
            }
        }

        return false;
    }
}