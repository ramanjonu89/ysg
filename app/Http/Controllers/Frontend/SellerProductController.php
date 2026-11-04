<?php
namespace App\Http\Controllers\Frontend;

use App\BusinessSetting;
use App\Models\AgeRestrictedSetting;
use App\Models\ProductStock;
use App\Services\CategoryService;
use App\Services\CorporateAccessService;
use App\Services\CouponService;
use App\Services\ProductService;
use App\Services\SellerService;
use App\Services\ShopService;
use App\Services\StoreAccessService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SellerProductController extends Controller
{
    /**
     * @param StoreAccessService
     */
    protected $storeAccessService;

    /**
     * @param ProductService
     */
    protected $productService;

    /**
     * @param SellerService
     */
    protected $sellerService;

    /**
     * @param CategoryService
     */
    protected $categoryService;

    /**
     * @param CouponService
     */
    protected $couponService;

    /**
     * @param ShopService
     */
    protected $shopService;

    /**
     * @param CorporateAccessService
     */
    protected $corporateAccessService;

    public function __construct(
        ProductService $productService,
        SellerService $sellerService,
        CategoryService $categoryService,
        CouponService $couponService,
        StoreAccessService $storeAccessService
    )
    {
        $this->productService = $productService;
        $this->sellerService = $sellerService;
        $this->categoryService = $categoryService;
        $this->couponService = $couponService;
        $this->storeAccessService = $storeAccessService;
    }

    /**
     * Show form input for both AccessCode and CACode
     * @param  Request  $request
     * @param $slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function AccessCodeForm(Request $request, $slug)
    {
        $product = $this->productService->getBySlug($slug);
        $shop = $product->user->shop;

        if ($this->storeAccessService->isNeedAccessCode($shop)) {
            $title = $shop->access_code_message;
        } else {
            $title = sprintf('You are trying to access %s, this Product required an access code, please fill below', $product->name);
        }
        $route = $request->route();
        $routeName = str_replace(".access-form", "", $route->getName());

        return view(
            'frontend.ca_code_form',
            [
                'title' => $title,
                'actionUrl' => route($routeName, $route->parameters()),
            ]
        );
    }

    public function product(Request $request, $slug)
    {
        $detailedProduct        = $this->productService->findBy('slug', $slug);
        if ($detailedProduct    != null && $detailedProduct->published && $detailedProduct->is_enable_product) {

            $category           = $this->categoryService->get($detailedProduct->category_id);
            $isAgeRestricted    = $category ? $category->is_age_restricted : 0;
            $reviewCategories   = $category ? $category->reviewCategories()->where('enabled', true)->orderBy('title')->get() : collect();
            $isConfirmAge = session()->get('is_confirm_age');
            if (!isset($isConfirmAge) && $isAgeRestricted === AgeRestrictedSetting::TWENTYONE_AND_ABOVE) {
                return redirect()->route('age_restricted.get_form_confirm', ['value' => $isAgeRestricted, 'slug' => $slug]);
            }

            if ($detailedProduct->only_microsite && !isMicrosite()) {
                session(['isMicrosite'  => true]);
                session(['shop'         => $detailedProduct->user->shop]);
                session(['microsite'    => $detailedProduct->user->seller->microsite]);
            }

            $sellerUser     = $this->sellerService->getByUser($detailedProduct->user_id);
            if ($sellerUser->isExpired()) {
                abort(404);
            }
            $coupons = $this->couponService->extractUsableCoupon($detailedProduct->user->shop->sellerCoupons, [$detailedProduct->id]);

            $currentUser        = Auth::user();
            updateCartSetup();
            if ($request->has('product_referral_code')) {
                Cookie::queue('product_referral_code', $request->product_referral_code, 43200);
                Cookie::queue('referred_product_id', $detailedProduct->id, 43200);
            }
            $businessSettings = BusinessSetting::all()->pluck('value', 'type')->toArray();
            if ($detailedProduct->digital == 1) {
                return view('frontend.digital_product_details',
                    compact('detailedProduct', 'currentUser', 'businessSettings', 'isAgeRestricted', 'reviewCategories')
                );
            }

            $relatedProducts        = $this->productService->getRelatedProducts($detailedProduct);
            $topSellingProducts     = $this->productService->getTopSellingProducts($detailedProduct->user_id);

            return view('frontend.product_details',
                compact('detailedProduct', 'currentUser', 'businessSettings', 'isAgeRestricted', 'relatedProducts', 'topSellingProducts', 'coupons', 'reviewCategories')
            );
        }
        abort(404);
    }

    public function checkStock(Request $request)
    {
        $product = $this->productService->getFirstWithConditions(['id' => $request->product_id]);
        $choiceOptions = json_decode($product->choice_options, true) ?: [];

        $variants = (array) $request->get('variants', []);
        $available = [];

        if (empty($variants)) {
            $attrId = $choiceOptions[0]['attribute_id'] ?? null;

            if ($attrId) {
                $stocksAvailable = $product->stocks()->available();
                foreach ($stocksAvailable as $stock) {
                    $available[] = [
                        'attribute_id' => $attrId,
                        'value'        => $stock->variant,
                    ];
                }
            }

            return response()->json(['available' => $available]);
        }

        $attrIndexMap = collect($choiceOptions)->pluck('attribute_id')->toArray();
        $stocksAvailable = $stocksAvailable = $product->stocks()->available($variants);

        $available = [
            'variants'            => [],
            'attribute_index_map' => [],
        ];

        foreach ($stocksAvailable as $stock) {
            $available['variants'][] = $stock->variant;

            foreach (explode(ProductStock::SPLIT_SYMBOL, $stock->variant) as $index => $value) {
                if (isset($attrIndexMap[$index])) {
                    $available['attribute_index_map'][$index] = $attrIndexMap[$index];
                }
            }
        }

        return response()->json(['available' => $available]);
    }
}

