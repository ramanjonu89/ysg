<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\{Attribute,
    BusinessSetting,
    Color,
    CustomerPackage,
    PickupPoint,
    User
};
use App\Http\Requests\UserUpdateProfileRequest;
use App\Services\{AttributeService,
    BrandService,
    CartService,
    CategoryService,
    CorporateAccessService,
    CouponService,
    CustomerProductService,
    FlashDealService,
    HomeCategoryService,
    LanguageService,
    UserMailService,
    MicrositeService,
    ProductSearchService,
    ProductService,
    UserService,
    SellerService,
    StoreAccessService,
    SubCategoryService,
    SubSubCategoryService,
    FlashDealProductService,
    OrderService};
use App\Models\{Address,
    ProductStock,
    SubCategory,
    SubSubCategory,
    Product,
    Seller,
    Country,
    Brand,
    Policy,
    Category,
    Order,
    CustomPage\Page,
    Shop};
use App\Support\PasswordPolicy;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use FontLib\Table\Type\name;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use stdClass;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

use ElasticScoutDriverPlus\Support\Query;
use App\Http\Resources\SellerCenter\Product as ProductResource;
class HomeController extends Controller
{
    /**
     * @param AttributeService
     */
    protected $attributeService;

    /**
     * @param CategoryService
     */
    protected $categoryService;

    /**
     * @param BrandService
     */
    protected $brandService;

    /**
     * @param CAService
     */
    protected $CAService;

    /**
     * @param CouponService
     */
    protected $couponService;

    /**
     * @param customerProductService
     */
    protected $customerProductService;

    /**
     * @param flashDealService
     */
    protected $flashDealService;

    /**
     * @param FlashDealProductService
     */
    protected $flashDealProductService;

    /**
     * @param HomeCategoryService
     */
    protected $homeCategoryService;

    /**
     * @param LanguageService
     */
    protected $languageService;

    /**
     * @param OrderService
     */
    protected $orderService;

    /**
     * @param MicrositeService
     */
    protected $micrositeService;

    /**
     * @param UserMailService
     */
    protected $mailService;

    /**
     * @param ProductService
     */
    protected $productService;

    /**
     * @param SearchService
     */
    protected $searchService;

    /**
     * @param SellerService
     */
    protected $sellerService;

    /**
     * @param StoreAccessService
     */
    protected $storeAccessService;

    /**
     * @param SubCategoryService
     */
    protected $subCategoryService;

    /**
     * @param SubSubCategoryService
     */
    protected $subSubCategoryService;

    /**
     * @param UserService
     */
    protected $userService;

    public function __construct(
        // Product-related
        BrandService            $brandService,
        CustomerProductService  $customerProductService,
        ProductService          $productService,
        ProductSearchService    $searchService,
        SellerService           $sellerService,

        // Category & Attribute-related
        AttributeService        $attributeService,
        CategoryService         $categoryService,
        HomeCategoryService     $homeCategoryService,
        SubCategoryService      $subCategoryService,
        SubSubCategoryService   $subSubCategoryService,

        // Order & Promotion-related
        CouponService           $couponService,
        FlashDealService        $flashDealService,
        FlashDealProductService $flashDealProductService,
        OrderService            $orderService,
        StoreAccessService      $storeAccessService,

        // User & Corporate Access-related
        CorporateAccessService  $corporateAccessService,
        UserService             $userService,
        UserMailService         $mailService,

        // System & Miscellaneous
        LanguageService         $languageService,
        MicrositeService        $micrositeService
    ) {
        // Product-related
        $this->brandService            = $brandService;
        $this->customerProductService  = $customerProductService;
        $this->productService          = $productService;
        $this->searchService           = $searchService;
        $this->sellerService           = $sellerService;

        // Category & Attribute-related
        $this->attributeService        = $attributeService;
        $this->categoryService         = $categoryService;
        $this->homeCategoryService     = $homeCategoryService;
        $this->subCategoryService      = $subCategoryService;
        $this->subSubCategoryService   = $subSubCategoryService;


        // Order & Promotion-related
        $this->couponService           = $couponService;
        $this->flashDealService        = $flashDealService;
        $this->flashDealProductService = $flashDealProductService;
        $this->orderService            = $orderService;
        $this->storeAccessService      = $storeAccessService;

        // User & Corporate Access-related
        $this->CAService               = $corporateAccessService;
        $this->mailService             = $mailService;
        $this->userService             = $userService;

        // System & Miscellaneous
        $this->languageService         = $languageService;
        $this->micrositeService        = $micrositeService;
    }

    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $link = session('previous_url');
        if ($link == null) {
            session(['previous_url' => url()->previous()]);
        }

        return view('frontend.user_login');
    }


    public function registration(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        if ($request->has('referral_code')) {
            Cookie::queue('referral_code', $request->referral_code, 43200);
        }

        $referal_code = $request->get('referral_register_code', null);
        $ref = $request->query->get('ref', '');
        $ref_error_message = '';
        if ($ref) {
            if ($this->CAService->isCAToken($ref)) {
                $validateResult = $this->CAService->checkTokenKey($ref);
                if ($validateResult['status'] == false) {
                    $ref_error_message = $validateResult['message'];
                }
            }
        }

        return view('frontend.user_registration', [
            'ref' => $ref,
            'referal_code' => $referal_code,
            'ref_error_message' => $ref_error_message,
        ]);
    }

    public function cart_login(Request $request)
    {
        $user = User::where('email', $request->email)->orWhere('phone', $request->email)->first();
        if ($user != null) {
            updateCartSetup();
            if (Hash::check($request->password, $user->password)) {
                if ($request->has('remember')) {
                    auth()->login($user, true);
                } else {
                    auth()->login($user, false);
                }
            }
        }

        return back();
    }

    /**
     * Show the admin dashboard.
     *
     * @return Response
     */
    public function admin_dashboard()
    {
        $categories = $this->categoryService->getAll();
        $monthtodatetotalorders = \App\Models\OrderDetail::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->distinct('order_id')->count();
        $monthtodateitemsold = \App\Models\OrderDetail::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('payment_status', 'paid')->where('delivery_status', '!=', 'cancelled')->sum('quantity');
        $monthtodatetotalsales = \App\Models\Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('payment_status', 'paid')->sum('grand_total');

        $productService = app()->make(ProductService::class);
        $totalPublishedProducts = $productService->getTotalPublishedProducts();
        $totalSellerPublishedProducts = $productService->getTotalSellerPublishedProducts();
        $totalAdminPublishedProducts = $productService->getTotalAdminPublishedProducts();

        return view('dashboard', compact([
            'monthtodatetotalorders',
            'monthtodateitemsold',
            'monthtodatetotalsales',
            'totalPublishedProducts',
            'totalSellerPublishedProducts',
            'totalAdminPublishedProducts',
            'categories'
        ]));
    }

    /**
     * Show the customer/seller dashboard.
     *
     * @return Application|Factory|Response|View
     */
    public function dashboard()
    {
        $currentUser = Auth::user();
        if ($currentUser->user_type == User::TYPE_SELLER) {
            $categories = Category::whereHas('products',function($product) use($currentUser){
                $product->where(['user_id' => $currentUser->id]);
            })->get();

            return view('frontend.seller.dashboard', [
                'categories' => $categories
            ]);
        }
        if ($currentUser->user_type == User::TYPE_CUSTOMER) {
            $referralEnabled = BusinessSetting::where('type', 'referral_enabled')->first();
            $referralData = [
                'referrer' => $currentUser->id,
                'type' => BusinessSetting::REFERRAL_TYPE_NORMAL
            ];
            $encryptedData = base64_encode(json_encode($referralData));
            $referralLink = route('user.registration', [
                'ref' => $encryptedData
            ]);

            $corporateRefEnabled = $this->CAService->isSettingEnable();
            $corporateRefLimit = 0;
            $corporateRefLeft = 0;

            $canShareCA = $this->CAService->canShare($currentUser);
            if ($canShareCA) {
                $corporateRefLimit = $this->CAService->getUserShareLimit($currentUser);
                $corporateRefLeft = $this->CAService->getUserShareTimesLeft($currentUser);
            }
            $cart = app()->make(CartService::class)->getCart();
            $totalItems = $cart ? $cart->getTotalItems() : 0;
            $totalWishlists = count($currentUser->wishlists);
            $totalOrders = $this->orderService->getTotalOrdersByUser($currentUser->id);

            return view('frontend.customer.dashboard', compact(
                'referralEnabled',
                'corporateRefEnabled',
                'referralLink',
                'corporateRefLeft',
                'corporateRefLimit',
                'currentUser',
                'canShareCA',
                'totalItems',
                'totalWishlists',
                'totalOrders'
            ));
        }
        abort(404);
    }

    public function profile(Request $request)
    {
        if (Auth::user()->user_type == 'customer') {
            return view('frontend.customer.profile');
        } elseif (Auth::user()->user_type == 'seller') {
            return view('frontend.seller.profile');
        }
    }

    public function customer_update_profile(UserUpdateProfileRequest $request)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(__('Sorry! the action is not permitted in demo'))->error();

            return back();
        }
        try {

            $data = [
                'name' => $request->name,
                'postal_code' => $request->postal_code,
                'country_code' => $request->country_code,
                'phone' => $request->phone,
            ];

            if ($request->new_password != null) {
                $data['password'] = Hash::make($request->new_password);
            }

            $user = Auth::user();
            $user = $this->userService->update($user->id, $data);
            flash(__('Your Profile has been updated successfully!'))->success();
            return back();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            flash(__('Sorry! Something went wrong.'))->error();
            return back();
        }
    }

    public function customer_delete_account($id)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(__('Sorry! the action is not permitted in demo'))->error();
            return back();
        }

        try {
            $user = $this->userService->getFirstWithConditions(['id' => $id]);

            if (!$user) {
                flash(__('User not found.'))->error();
                return back();
            }

            if ($user->id !== Auth::user()->id) {
                flash(__('You are not authorized to delete this user.'))->error();
                return back();
            }

            $emailNameOld = explode('@', $user->email);
            $newEmail = reset($emailNameOld) . '_deleted_' . time() . '@' . end($emailNameOld);

            $this->userService->update($user->id, ['email' => $newEmail]);
            $this->userService->destroy($user->id);

            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            flash(__('User deleted successfully.'))->success();
            return redirect()->route('home');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            flash(__('Sorry! Something went wrong.'))->error();
            return back();
        }
    }

    public function seller_update_profile(UserUpdateProfileRequest $request)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(__('Sorry! the action is not permitted in demo'))->error();
            return back();
        }

        try {
            DB::beginTransaction();

            $user = $request->user();
            $dataUser = [
                'name' => $request->name,
                'address' => $request->address,
                'country' => $request->country,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'phone' => $request->phone,
                'password' => Hash::make($request->new_password)
            ];

            if ($request->hasFile('photo')) {
                $dataUser['avatar_original'] = $request->photo->store('uploads');
            }

            $dataSeller = [
                'cash_on_delivery_status' => $request->cash_on_delivery_status,
                'bank_payment_status' => $request->bank_payment_status,
                'bank_name' => $request->bank_name,
                'bank_acc_name' => $request->bank_acc_name,
                'bank_acc_no' => $request->bank_acc_no,
                'bank_routing_no' => $request->bank_routing_no
            ];

            $user = $this->userService->update($user->id, $dataUser);
            $seller = $user->seller;
            $seller->update($dataSeller);
            DB::commit();

            flash(__('Your Profile has been updated successfully!'))->success();
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            flash(__('Sorry! Something went wrong.'))->error();
            return back();
        }
    }

    /**
     * Show the application frontend home.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $numTodaysDeal          = count(filter_products(\App\Product::where('published', 1)->where('todays_deal', 1 ))->get());
        // $numTodaysDeal        = count(filter_products($this->productService->getAllWithConditions(['published' => 1, 'todays_deal' => 1])));
        $featuredCategories     = $this->categoryService->getAllWithConditions(['featured' => 1]);
        $flashDeal              = $this->flashDealService->getFirstWithConditions(['status' => 1, 'featured' => 1]);
        $customerProducts       = $this->customerProductService->getDataLimitWithConditions(['status' => 1, 'published' => 1], '*', 10);
        $request->session()->forget('isMicrosite');
        $request->session()->forget('shop');

        $page = Page::where('type', '=', Page::TYPE_HOMEPAGE)->first();
        $topBrands     = $this->brandService->getTopBrands();
        $topCategories = $this->categoryService->getTopCategories();

        return view('frontend.index',
                compact('page',
                'topBrands',
                'flashDeal',
                'numTodaysDeal',
                'topCategories',
                'customerProducts',
                'featuredCategories'
            )
        );
    }

    public function flash_deal_details($slug)
    {
        $flash_deal = $this->flashDealService->getFirstWithConditions(['slug' => $slug]);
        if ($flash_deal != null)
            return view('frontend.flash_deal_details', compact('flash_deal'));
        else {
            abort(404);
        }
    }

    public function load_featured_section()
    {
        $brands = Brand::all()->pluck('name', 'id')->toArray();
        $products = $this->productService->getHomeFeaturedProducts();

        return view('frontend.partials.featured_products_section', compact(['brands', 'products']));
    }

    public function load_best_selling_section()
    {
        $brands = Brand::all()->pluck('name', 'id')->toArray();
        $products = $this->productService->getHomeBestSellingProducts();

        return view('frontend.partials.best_selling_section', compact(['brands', 'products']));
    }

    public function load_home_categories_section()
    {
        $homeCategories = $this->homeCategoryService->getList();
        $products = [];
        $cacheKey = 'home_categories_section';
        if (Cache::has($cacheKey)) {
            $products = Cache::get($cacheKey);
        } else {
            foreach ($homeCategories as $key => $homeCategory) {
                $products[$homeCategory->category->id] = $this->productService->getHomeCategoryProducts($homeCategory->category->id);
            }
            Cache::put($cacheKey, $products, 7200);
        }

        return view('frontend.partials.home_categories_section', compact(['products', 'homeCategories']));
    }

    public function load_best_sellers_section()
    {
        $bestSellers = $this->sellerService->getBestSellers();

        return view('frontend.partials.best_sellers_section', compact('bestSellers'));
    }

    public function trackOrder(Request $request)
    {
        if ($request->has('order_code')) {
            $order = Order::where('code', $request->order_code)->first();
            if ($order != null) {
                return view('frontend.track_order', compact('order'));
            }
        }

        return view('frontend.track_order');
    }

    public function micrositeProduct(Request $request, $slug_seller, $slug)
    {
        return $this->product($request, $slug);
    }

    public function product(Request $request, $slug)
    {
        $detailedProduct = $this->productService->findBy('slug', $slug);
        // dd($detailedProduct->user->product_reviews_count);
        // $detailedProduct->user->productReviews
        if ($detailedProduct != null && $detailedProduct->published) {

            if ($detailedProduct->only_microsite && !isMicrosite()) {
                session(['isMicrosite' => true]);
                session(['shop' => $detailedProduct->user->shop]);
                session(['microsite' => $detailedProduct->user->seller->microsite]);
            }

            $sellerUser = $this->sellerService->getByUser($detailedProduct->user_id);
            if ($sellerUser->isExpired()) {
                abort(404);
            }

            $category = $this->categoryService->get($detailedProduct->category_id);
            $isAgeRestricted = $category ? $category->is_age_restricted : 0;
            $reviewCategories = $category ? $category->reviewCategories()->where('enabled', true)->orderBy('title')->get() : collect();

            $domainSeller = $this->sellerService->findBy('user_id', $detailedProduct->user_id);
            $isValidDomain = $this->validateCorporateDomain($domainSeller);
            $currentUser = Auth::user();
            updateCartSetup();
            if ($request->has('product_referral_code')) {
                Cookie::queue('product_referral_code', $request->product_referral_code, 43200);
                Cookie::queue('referred_product_id', $detailedProduct->id, 43200);
            }
            $businessSettings = BusinessSetting::all()->pluck('value', 'type')->toArray();
            if ($detailedProduct->digital == 1) {
                return view(
                    'frontend.digital_product_details',
                    compact('detailedProduct', 'isValidDomain', 'currentUser', 'businessSettings', 'isAgeRestricted', 'reviewCategories')
                );
            }

            $relatedProducts = $this->productService->getRelatedProducts($detailedProduct);
            $topSellingProducts = $this->productService->getTopSellingProducts($detailedProduct->user_id);

            return view(
                'frontend.product_details',
                compact('detailedProduct', 'isValidDomain', 'currentUser', 'businessSettings', 'isAgeRestricted', 'relatedProducts', 'topSellingProducts', 'reviewCategories')
            );
            // return view('frontend.product_details', compact('detailedProduct'));
        }
        abort(404);
    }

    private function validateCorporateDomain(Seller $seller)
    {
        if ($seller->user->company_type !== 2 || $seller->user->enable_corporate_domains !== 1) {
            return false;
        }
        if ($seller->corporate_domains()->count() === 0) {
            return true;
        }
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        $emailDomain = substr($user->email, strpos($user->email, "@"));
        $activeCorporateAccess = $user->getActiveCorporateAccess();
        if (
            $activeCorporateAccess &&
            $activeCorporateAccess->expire_at &&
            $activeCorporateAccess->expire_at->greaterThan(Carbon::now())
        ) {
            $emailDomain = $activeCorporateAccess->domain;
        }
        $corporateDomains = $seller->corporate_domains()->select('domain')->pluck('domain');
        if (!$corporateDomains->contains($emailDomain)) {
            return false;
        }

        return true;
    }

    public function shop(Request $request, $slug)
    {
        return $this->visit_shop($request, $slug);
    }

    public function visit_shop(Request $request, $slug, $type = null)
    {
        $shop = Shop::where('slug', $slug)->first();

        if ($shop != null) {
            $seller = Seller::where('user_id', $shop->user_id)->first();
            $needAccessCode = $this->storeAccessService->isNeedAccessCode($shop);
            $needDomain = $this->storeAccessService->isNeedDomain($shop);
            $isValidDomain = $this->storeAccessService->validateCorporateDomain($shop);
            $currentUser = Auth::user();
            $startDate = Carbon::parse($shop->user->company_start_date)->format('Y-m-d 00:00:00');
            $endDate = Carbon::parse($shop->user->company_end_date)->format('Y-m-d 23:59:59');
            $now = Carbon::now();
            if (
                $shop->user->company_type == User::COMPANY_TYPE_PRIVATE_EVENT
                && ($startDate <= $now)
                && ($endDate > $now)
                && ($needAccessCode || $needDomain)
                && !$isValidDomain
            ) {
                if (is_null($currentUser)) {
                    flash(__('Please login using a valid account to enter the Private Sales Store!'), 'warning');
                    return redirect()->route('user.login');
                }
            }

            if ($shop->user->enable_corporate_domains === 1 && !$isValidDomain) {

                $text1 = __('This store can only be accessed by companies enrolled in YSG\'s');
                $text2 = __('Corporate Sales Store Employee Benefits Programme');
                $text3 = __('Programme.');
                $text4 = __('If you are still having difficulty logging in, please contact YSG at cs@ysg.com.sg');
                $popupContent = '<div class="swal-content">'.$text1.' '.
                    '<a href="https://ysg.com.sg/Corporate-Sales---Employee-Benefits-Programme">'.$text2.'</a> '.$text3.'' .
                    '<br>'.$text4.' '.' </div>';

                flash( $popupContent, 'warning');
                return redirect()->route('home');
            }
            $category_ids  = $request->get('category_ids') ?? [];
            $brand_ids     = $request->get('brand_ids') ?? [];
            $sort_by       = $request->get('sort_by');
            $keyword       = $request->get('q');
            $min_price     = $request->get('min_price');
            $max_price     = $request->get('max_price');
            $finalMinPrice = $min_price ?? 0;
            $finalMaxPrice = $max_price ?? 99999;
            $productQuery  = Product::microsite()->categoryEnabled()->brandEnabled()
                ->select('*', DB::raw("CASE
                    WHEN (!isnull(discount) AND discount_type = 'amount') THEN (unit_price - discount)
                    WHEN (!isnull(discount) AND discount_type = 'percent') THEN (unit_price * (100 - discount) / 100)
                    ELSE unit_price END AS final_price"))
                ->where('user_id', $shop->user->id)
                ->where('published', 1)
                // ->where('current_stock', '>', 0)
                ->where(function ($query) use ($category_ids, $brand_ids) {
                    if (!empty($category_ids)) {
                        $query->whereIn('category_id', $category_ids);
                    }
                    if (!empty($brand_ids)) {
                        $query->whereIn('brand_id', $brand_ids);
                    }
                })->where(function ($query) use ($keyword) {
                    if (!empty($keyword)) {
                        $query->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('tags', 'like', '%' . $keyword . '%')
                            ->orWhere('unit', 'like', '%' . $keyword . '%');
                    }
                })
                ->whereRaw(
                    "(CASE " .
                        "WHEN (!isnull(discount) AND discount_type = 'amount') THEN (unit_price - discount) " .
                        "WHEN (!isnull(discount) AND discount_type = 'percent') THEN (unit_price * (100 - discount) / 100) " .
                        "ELSE unit_price " .
                        "END) BETWEEN ? AND ?",
                    [$finalMinPrice, $finalMaxPrice]
                );

            $tmp = clone $productQuery;
            list($attributes, $all_colors) = $this->productService->getAttributeAndColorListForFilter($tmp);
            $existing_attributes = Attribute::all()->pluck('name', 'id')->toArray();
            $selected_attributes = [];
            $selectedAttributeValues = [];
            foreach ($attributes as $attribute) {
                if ($request->has('attribute_' . $attribute['id'])) {
                    foreach ($request['attribute_' . $attribute['id']] as $value) {
                        $selectedAttributeValues[] = '"' . $value . '"';
                    }
                    $selected_attributes[$attribute['id']] = $request['attribute_' . $attribute['id']];
                }
            }
            if (!empty($selectedAttributeValues)) {
                $productQuery->where(function ($q) use ($selectedAttributeValues) {
                    $count = 1;
                    foreach ($selectedAttributeValues as $str) {
                        if ($count === 1) {
                            $q->where('choice_options', 'like', '%' . $str . '%');
                        } else {
                            $q->orWhere('choice_options', 'like', '%' . $str . '%');
                        }
                        $count++;
                    }
                });
            }
            $selected_color = $request->get('color');
            if ($selected_color) {
                $str = '"' . $selected_color . '"';
                $productQuery->where('colors', 'like', '%' . $str . '%');
            }
            if ($type && $type == 'top_selling') {
                $productQuery = $productQuery->orderBy('num_of_sale', 'desc');
            } elseif ($sort_by) {
                $productQuery = $this->productService->applySortBy($sort_by, $productQuery);
            }
            $products = $productQuery
                ->paginate(24)
                ->appends(request()->query());


            // $products = $this->productService->
            $allBrandsAndCategories = Product::microsite()
                ->where('published', 1)
                // ->where('current_stock', '>', 0)
                ->where('user_id', $shop->user->id)
                ->select(['category_id', 'brand_id'])
                ->distinct()
                ->get()
                ->toArray();
            $categories = Category::where('status', '=', 1)
                ->whereIn('id', array_column($allBrandsAndCategories, 'category_id'))
                ->get();
            $brands = DB::table('brands')
                ->whereIn('id', array_column($allBrandsAndCategories, 'brand_id'))
                ->select(['brands.id', 'brands.name'])
                ->get()
                ->toArray();
            $is_include_filter = ($type === 'all_products');
            if ($seller->verification_status != 0) {
                return view(
                    'frontend.seller_shop.index',
                    compact(
                        'shop',
                        'isValidDomain',
                        'currentUser',
                        'type',
                        'is_include_filter',
                        'categories',
                        'brands',
                        'products',
                        'category_ids',
                        'brand_ids',
                        'keyword',
                        'sort_by',
                        'min_price',
                        'max_price',
                        'attributes',
                        'selected_attributes',
                        'all_colors',
                        'selected_color',
                        'existing_attributes'
                    )
                );
            }

            return view('frontend.seller_shop_without_verification', compact('shop', 'seller'));
        }
        abort(404);
    }

    public function listing(Request $request)
    {
        // $products = filter_products(Product::orderBy('created_at', 'desc'))->paginate(12);
        // return view('frontend.product_listing', compact('products'));
        return $this->search($request);
    }

    public function micrositeSearch(Request $request, $seller_slug)
    {
        return $this->search($request);
    }

    public function search(Request $request, $isView = true)
    {
        if (env('ELASTICSEARCH_ENABLED', false)) {
            return $this->search_by_es($request);
        }

        $returnData = $this->searchService->process($request);

        return view('frontend.product_listing', $returnData);
    }

    public function search_by_es(Request $request)
    {
        $canViewCSSPage = auth()->user() ? $this->CAService->canViewCSSPage(auth()->user()) : false;
        $query = $request->q;
        $rate = $request->get('rate', '');
        $sort_by = $request->get('sort_by', '');
        $discount = $request->get('discount', '');
        $storeTypes = $request->get('store_types', null);
        $min_price = $request->get('min_price');
        $max_price = $request->get('max_price');

        $couponId = $request->get('coupon_id', '');
        $coupon = $this->couponService->getFirstWithConditions(['id' => !empty($couponId) ? decrypt($couponId) : '']);
        $filters = [];
        if (isset($coupon)) {
            $filters = $this->couponService->buildProductsFilterByCoupon($coupon);
        }

        $selectedPageSize = isset($request->pageSize) ? $request->pageSize : 12;
        $pageSizeOptions = [12, 24, 36, 48];

        $queryBuilder = [
            'bool' => [
                'must' => [
                    ['term' => ['published' => true]]
                ],
                'filter' => []
            ]
        ];

        if (!empty($query)) {
            $queryBuilder['bool']['should'] = $this->buildSearchQueries($query);
            $queryBuilder['bool']['minimum_should_match'] = 1;
        }

        if ($min_price !== null || $max_price !== null) {
            $priceRange = [];
            if ($min_price !== null) {
                $priceRange['gte'] = $min_price;
            }
            if ($max_price !== null) {
                $priceRange['lte'] = $max_price;
            }
            $queryBuilder['bool']['filter'][] = [
                'range' => [
                    'min_final_price' => $priceRange
                ]
            ];
        }

        if ($request->has('category_ids')) {
            $queryBuilder['bool']['filter'][] = [
                'terms' => [
                    'category.id' => $request->category_ids
                ]
            ];
        }

        if ($request->has('subcategory_ids')) {
            $queryBuilder['bool']['filter'][] = [
                'terms' => [
                    'subcategory.id' => $request->subcategory_ids
                ]
            ];
        }

        if ($request->has('brand_ids')) {
            $queryBuilder['bool']['filter'][] = [
                'terms' => [
                    'brand_id' => $request->brand_ids
                ]
            ];
        }

        if ($request->has('seller_ids')) {
            $queryBuilder['bool']['filter'][] = [
                'terms' => [
                    'user_id' => $request->seller_ids
                ]
            ];
        }

        if ($request->has('color')) {
            $queryBuilder['bool']['filter'][] = [
                'term' => [
                    'colors' => $request->color
                ]
            ];
        }

        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'attribute_') === 0) {
                $attributeId = substr($key, 11);
                $queryBuilder['bool']['filter'][] = [
                    'terms' => [
                        'choice_options.' . $attributeId => (array)$value
                    ]
                ];
            }
        }

        if ($request->has('rate')) {
            $queryBuilder['bool']['filter'][] = [
                'range' => [
                    'rating' => [
                        'gte' => $request->rate
                    ]
                ]
            ];
        }

        if ($request->has('discount')) {
            $queryBuilder['bool']['filter'][] = [
                'range' => [
                    'discount' => [
                        'gt' => 0
                    ]
                ]
            ];
        }

        if (!empty($filters)) {
            if (!empty($filters['productIds'])) {
                $queryBuilder['bool']['filter'][] = [
                    'terms' => [
                        '_id' => $filters['productIds']
                    ]
                ];
            }
        }

        $sort = '';
        if ($sort_by) {
            switch ($sort_by) {
                case Product::SORT_BY_LATEST:
                    $sort = 'created_at';
                    break;
                case Product::SORT_BY_TOP_SALES:
                    $sort = 'num_of_sale';
                    break;
                case Product::SORT_BY_PRICE_LOW_TO_HIGH:
                    $sort = 'min_final_price';
                    break;
                case Product::SORT_BY_PRICE_HIGH_TO_LOW:
                    $sort = 'max_final_price';
                    break;
            }
        }

        $direction = '';
        if ($sort_by) {
            switch ($sort_by) {
                case Product::SORT_BY_LATEST:
                case Product::SORT_BY_PRICE_HIGH_TO_LOW:
                case Product::SORT_BY_TOP_SALES:
                    $direction = 'desc';
                    break;
                case Product::SORT_BY_PRICE_LOW_TO_HIGH:
                    $direction = 'asc';
                    break;
            }
        } else {
            $direction = 'desc';
        }

        $allProducts = Product::searchQuery($queryBuilder)->paginate(9999, 'page', 1);
        $allProducts = $allProducts->documents();

        $categoryIds = [];
        $subCategoryIds = [];
        $subSubCategoryIds = [];
        $brandIds = [];
        $sellerIds = [];
        foreach ($allProducts as $product) {
            $categoryIds[] = $product->content('category_id');
            $subCategoryIds[] = $product->content('subcategory_id');
            $subSubCategoryIds[] = $product->content('subsubcategory_id');
            $brandIds[] = $product->content('brand_id');
            $sellerIds[] = $product->content('user_id');
        }
        $categoryIds = array_unique($categoryIds);
        $categories = $this->categoryService->getByIds($categoryIds);
        $subCategories = $this->subCategoryService->getByIds($subCategoryIds);
        $subSubCategories = $this->subSubCategoryService->getByIds($subSubCategoryIds);
        $brands = $this->brandService->getByIds($brandIds);
        $sellers = $this->userService->getByIdsInOrder($sellerIds);

        if ($sort !== '') {
            $products = Product::searchQuery($queryBuilder)
                ->sort($sort, $direction)
                ->paginate($selectedPageSize)
                ->appends(request()->query());;
        } else {
            $products = Product::searchQuery($queryBuilder)
                ->paginate($selectedPageSize)
                ->appends(request()->query());
        }


        $existing_attributes = $this->attributeService->getAttribute();

        $category = $this->categoryService->findId($request->category_ids);
        $subCategory = $this->subCategoryService->findId($request->subcategory_ids);
        $subSubCategory = $this->subSubCategoryService->findId($request->subsubcategory_ids);

        $categoryIds = isset($category) ? $request->category_ids : [];
        $subCategoryIds = isset($subCategory) ? $request->subcategory_ids : [];
        $subSubCategoryIds = isset($subSubCategory) ? $request->subsubcategory_ids : [];
        $brandIds = isset($request->brand_ids) ? $request->brand_ids : [];
        $sellerIds = isset($request->seller_ids) ? $request->seller_ids : [];

        list($attributes, $all_colors) = [[], []];

        $products = ProductResource::collection($products->setCollection($products->models()));

        return view('frontend.product_listing', compact(
            'rate',
            'query',
            'brands',
            'coupon',
            'sort_by',
            'sellers',
            'discount',
            'products',
            'min_price',
            'max_price',
            'brandIds',
            'storeTypes',
            'sellerIds',
            'canViewCSSPage',
            'pageSizeOptions',
            'selectedPageSize',
            'category',
            'categories',
            'categoryIds',
            'subCategory',
            'subCategories',
            'subCategoryIds',
            'subSubCategory',
            'subSubCategories',
            'subSubCategoryIds',
            'existing_attributes',
            'all_colors',
            'attributes',
        ));
    }

    public function all_categories(Request $request)
    {
        $categories = $this->categoryService->getMainCategories();
        $topBrands  = $this->brandService->getTopBrands();
        return view('frontend.all_category', compact('categories', 'topBrands'));
    }

    public function all_brands(Request $request)
    {
        $brands        = $this->brandService->getMainBrands();
        $topCategories = $this->categoryService->getTopCategories();
        return view('frontend.all_brand', compact('brands','topCategories'));
    }

    public function show_product_upload_form(Request $request)
    {
        if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
            if (Auth::user()->seller->remaining_uploads > 0) {
                $categories = Category::all();

                return view('frontend.seller.product_upload', compact('categories'));
            } else {
                flash(__('Upload limit has been reached. Please upgrade your package.'))->warning();

                return back();
            }
        }
        $categories = Category::all();

        return view('frontend.seller.product_upload', compact('categories'));
    }

    public function show_product_edit_form(Request $request, $id)
    {
        $categories = Category::all();
        $product = Product::find(decrypt($id));

        return view('frontend.seller.product_edit', compact('categories', 'product'));
    }

    public function seller_product_list(Request $request)
    {
        $user = $request->user();
        $shop = $user->shop;

        $search = isset($request->search) ? $request->search : null;
        $products = $this->productService->getSellerProducts($request);
        $featuredProductIds = explode(',', $shop->featured_products);

        return view('frontend.seller.products', compact(
            'products', 'search',
            'featuredProductIds',
        ));
    }

    public function ajax_search(Request $request)
    {
        $keywords = array();

        if (env('ELASTICSEARCH_ENABLED', false)) {
            $queryBuilder = [
                'bool' => [
                    'must' => [
                        ['term' => ['published' => true]]
                    ],
                    'filter' => []
                ]
            ];

            if (!empty($request->search)) {
                $query = $request->search;
                $queryBuilder['bool']['should'] = $this->buildSearchQueries($query);
                $queryBuilder['bool']['minimum_should_match'] = 1;
            }

            $products = Product::searchQuery($queryBuilder)->source(['id'])->execute()->models();

            $sellerIds = [];
            foreach ($products as $product) {
                $sellerIds[] = $product->user_id;
            }
            $uniqueIds = array_values(array_unique($sellerIds));
            // get 3 first items of uniqueIds
            $uniqueIds = array_slice($uniqueIds, 0, 3);
            $placeholders = str_repeat('?,', count($uniqueIds) - 1) . '?';
            $fieldExpression = "FIELD(user_id, {$placeholders})";
            $shops = Shop::whereIn('user_id', $uniqueIds)
                ->orderByRaw($fieldExpression, $uniqueIds)
                ->get();

        } else {
            $products = Product::withPublished()->where('tags', 'like', '%' . $request->search . '%')->get();
            foreach ($products as $key => $product) {
                foreach (explode(',', $product->tags) as $key => $tag) {
                    if (stripos($tag, $request->search) !== false) {
                        if (sizeof($keywords) > 5) {
                            break;
                        } else {
                            if (!in_array(strtolower($tag), $keywords)) {
                                array_push($keywords, strtolower($tag));
                            }
                        }
                    }
                }
            }

            $filterConditions = Product::onlyUnexpiredSellers()->withPublished()
                                    ->brandEnabled()->categoryEnabled()
                                    ->subCategoryEnabled()
                                    ->subSubCategoryEnabled()
                                    ->where('name', 'like', '%' . $request->search . '%');
            $productQuery = filter_products($filterConditions)->where('current_stock', '>', 0);
            if (isMicrosite()) {
                $productQuery->where('user_id', getMicrosite()->seller->user->id);
            }
            $products = $productQuery->get()->take(10);

            $shopUserIds = $products->pluck('user_id')->unique()->take(3);
            $shops = Shop::onlyUnexpiredSellers()
                ->whereIn('user_id', $shopUserIds)
                ->whereIn('user_id', verified_sellers_id())
                ->get();
        }

        $subsubcategories = SubSubCategory::where('name', 'like', '%' . $request->search . '%')->get()->take(3);

        if (sizeof($subsubcategories) > 0 || sizeof($products) > 0 || sizeof($shops) > 0) {
            return view('frontend.partials.search_content', compact('products', 'subsubcategories', 'keywords', 'shops'));
        }

        return '0';
    }

    public function product_content(Request $request)
    {
        $connector = $request->connector;
        $selector = $request->selector;
        $select = $request->select;
        $type = $request->type;
        productDescCache($connector, $selector, $select, $type);
    }

    public function top_10_settings(Request $request)
    {
        foreach (Category::all() as $key => $category) {
            if (is_array($request->top_categories) && in_array($category->id, $request->top_categories)) {
                $category->top = 1;
                $category->save();
            } else {
                $category->top = 0;
                $category->save();
            }
        }
        foreach (Brand::all() as $key => $brand) {
            if (is_array($request->top_brands) && in_array($brand->id, $request->top_brands)) {
                $brand->top = 1;
                $brand->save();
            } else {
                $brand->top = 0;
                $brand->save();
            }
        }
        flash(__('Top 10 categories and brands have been updated successfully'))->success();

        return redirect()->route('frontend_settings.home.index');
    }

    public function variant_price(Request $request)
    {
        $product = Product::find($request->id);
        $str = '';
        $quantity = 0;
        $discount_badge = 0;
        $productStock = false;
        $related_unit_price = $product->unit_price;
        if ($request->has('color')) {
            $data['color'] = $request['color'];
            $str = Color::where('code', $request['color'])->first()->name;
        }
        if (json_decode($product->choice_options) != null) {
            foreach (json_decode($product->choice_options) as $key => $choice) {
                if ($str != null) {
                    $str .= ProductStock::SPLIT_SYMBOL . $request['attribute_id_' . $choice->attribute_id];
                } else {
                    $str .= $request['attribute_id_' . $choice->attribute_id];
                }
            }
        }

        if ($str != null && '[]' !== $product->choice_options) {
            $productStock = true;
            $product_stock = $product->stocks->where('variant', $str)->first();
            if ($product_stock->discount_type == Product::DISCOUNT_TYPE_PERCENT) {
                $price = $product_stock->price - ($product_stock->price * $product_stock->discount) / 100;
            } else if ($product_stock->discount_type == Product::DISCOUNT_TYPE_AMOUNT) {
                $price = $product_stock->price - $product_stock->discount;
            } else {
                $price = $product_stock->price;
            }
            $quantity = $product_stock->qty;
            $related_unit_price = $product_stock->price;
        } else {
            $price = $product->unit_price;
            $quantity = $product->current_stock;
        }

        //discount calculation for flash deals
        $flash_deals = $this->flashDealService->getAllWithConditions(['status' => 1]);
        $inFlashDeal = false;
        foreach ($flash_deals as $key => $flash_deal) {
            $flash_deal_product = $this->flashDealProductService->getFirstWithConditions(['flash_deal_id' => $flash_deal->id, 'product_id' => $product->id]);
            if (isset($flash_deal_product) && !$flash_deal->isExpired()) {
                $price = $product->unit_price;
                if ($flash_deal_product->discount_type == Product::DISCOUNT_TYPE_PERCENT) {
                    $price -= ($price * $flash_deal_product->discount) / 100;
                } else if ($flash_deal_product->discount_type == Product::DISCOUNT_TYPE_AMOUNT) {
                    $price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }

        if (!$inFlashDeal && !$productStock) {
            if ($product->discount_type == Product::DISCOUNT_TYPE_PERCENT) {
                $price -= ($price * $product->discount) / 100;
            } else if ($product->discount_type == Product::DISCOUNT_TYPE_AMOUNT) {
                $price -= $product->discount;
            }
        }

        if ($product->tax_type == Product::DISCOUNT_TYPE_PERCENT) {
            $price += ($price * $product->tax) / 100;
        } else if ($product->tax_type == Product::DISCOUNT_TYPE_AMOUNT) {
            $price += $product->tax;
        }

        $releated_discount = (($related_unit_price - $price) * 100) / $related_unit_price;

        if ($releated_discount > 1) {
            $discount_badge = "-" . number_format((float)$releated_discount, 0, '.', '') . "%";
        } else {
            $discount_badge = "-" . number_format((float)$releated_discount, 1, '.', '') . "%";
        }
        return array(
            'price' => single_price($price * $request->quantity), 
            'quantity' => $quantity,
            'digital' => $product->digital, 
            'related_unit_price' => single_price($related_unit_price * $request->quantity),
            'discount_badge' => $discount_badge,
        );
    }

    public function sellerpolicy()
    {
        $term = Policy::where('name', 'seller_policy')->first();
        if(!$term) return abort(404);
        return view("frontend.policies.sellerpolicy",compact('term'));
    }

    public function returnpolicy()
    {
        $term = Policy::where('name', 'return_policy')->first();
        if(!$term) return abort(404);
        return view("frontend.policies.returnpolicy",compact('term'));
    }

    public function supportpolicy()
    {
        $term = Policy::where('name', 'support_policy')->first();
        if(!$term) return abort(404);
        return view("frontend.policies.supportpolicy",compact('term'));
    }

    public function terms()
    {
        $term = Policy::where('name', 'terms')->first();
        if(!$term) return abort(404);
        return view("frontend.policies.terms", compact('term'));
    }

    public function privacypolicy()
    {
        $term = Policy::where('name', 'privacy_policy')->first();
        if(!$term) return abort(404);
        return view("frontend.policies.privacypolicy",compact('term'));
    }

    public function get_pick_ip_points(Request $request)
    {
        $pick_up_points = PickupPoint::all();
        return view('frontend.partials.pick_up_points', compact('pick_up_points'));
    }

    public function get_category_items(Request $request)
    {
        if (isMicrosite()) {
            $cats = $this->categoryService->getSubCategories($request->id);
        } else {
            $cats = $this->categoryService->getSubCategoriesFlagShip($request->id);
        }

        return view('frontend.partials.category_elements', compact('cats'));
    }

    public function premium_package_index()
    {
        $customer_packages = CustomerPackage::all();

        return view('frontend.customer_packages_lists', compact('customer_packages'));
    }

    public function seller_digital_product_list(Request $request)
    {
        $user = $request->user();
        $shop = $user->shop;
        $featuredProductIds = explode(',', $shop->featured_products);

        $products = Product::where('user_id', $user->id)->where('digital', 1)->orderBy('created_at', 'desc')->paginate(10);

        return view('frontend.seller.digitalproducts.products', compact('products', 'featuredProductIds'));
    }

    public function show_digital_product_upload_form(Request $request)
    {
        if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
            if (Auth::user()->seller->remaining_digital_uploads > 0) {
                $business_settings = BusinessSetting::where('type', 'digital_product_upload')->first();
                $categories = Category::where('digital', 1)->get();

                return view('frontend.seller.digitalproducts.product_upload', compact('categories'));
            } else {
                flash(__('Upload limit has been reached. Please upgrade your package.'))->warning();

                return back();
            }
        }
        $business_settings = BusinessSetting::where('type', 'digital_product_upload')->first();
        $categories = Category::where('digital', 1)->get();

        return view('frontend.seller.digitalproducts.product_upload', compact('categories'));
    }

    public function show_digital_product_edit_form(Request $request, $id)
    {
        $categories = Category::where('digital', 1)->get();
        $product = Product::find(decrypt($id));

        return view('frontend.seller.digitalproducts.product_edit', compact('categories', 'product'));
    }

    // Ajax call
    public function new_verify(Request $request)
    {
        $email = $request->email;
        $regex_email = "/^[a-z0-9][a-z0-9\.]{1,28}[a-z0-9]@[a-z0-9]{2,}(\.[a-z0-9]{2,4}){1,2}$/";

        $response = [
            'status'  => 1,
            'message' => 'Can use this Email!'
        ];

        if (!preg_match($regex_email, $email)) {
            $response = [
                'status'  => 2,
                'message' => 'Invalid email format, please try again!'
            ];

        } else if (isUnique($email) === '0') {
            $response = [
                'status'  => 2,
                'message' => 'Email already exists!'
            ];
        }
        return json_encode($response);
    }

    // Form request
    public function update_email(Request $request)
    {
        $email = $request->email;
        $user = Auth::user();
        if (isUnique($email)) {
            $this->mailService->sendEmailChangeVerificationMail($user, $email);
            flash(__('A verification mail has been sent to the mail you provided us with.'))->success();

            return back();
        }
        flash(__('Email already exists!'))->warning();

        return back();
    }

    public function email_change_callback(Request $request)
    {
        if ($request->has('new_email_verificiation_code') && $request->has('email')) {
            $verification_code_of_url_param = $request->input('new_email_verificiation_code');
            $user = User::where('new_email_verificiation_code', $verification_code_of_url_param)->first();
            if ($user != null) {
                $user->email = $request->input('email');
                $user->new_email_verificiation_code = null;
                $user->save();
                auth()->login($user, true);
                flash(__('Email Changed successfully'))->success();

                return redirect()->route('dashboard');
            }
        }
        flash(__('Email was not verified. Please resend your mail!'))->error();

        return redirect()->route('dashboard');
    }

    public function email_change_callback_mobile(Request $request)
    {
        if ($request->has('new_email_verificiation_code') && $request->has('email')) {
            $verification_code_of_url_param = $request->input('new_email_verificiation_code');
            $user = User::where('new_email_verificiation_code', $verification_code_of_url_param)->first();
            if ($user != null) {
                $user->new_email_verificiation_code = null;
                $user->save();
                flash(__('Email Changed successfully'))->success();

                return redirect()->route('home');
            }
        }
        flash(__('Email was not verified. Please resend your mail!'))->error();

        return redirect()->route('home');
    }


    /**
     *  change language session function
     *  @param $request
     */
    public function changeLanguage(Request $request)
    {
        session(['locale' => $this->languageService->getLanguageCode($request->code)]);
        $sessionLanguageId = @$this->languageService->findBy('code',session('locale'))->id;
        session(['sessionLanguageId'=> $sessionLanguageId]);
    }

    public function getCities(Request $request){
        $country            = Country::find($request->country_id);
        if (!$country) return response()->json(['status' => 500, 'data' => []]);
        $cities             = $country->childrens;
        $data               = new stdClass();
        $html               = "<option value=''>Select Your City</option>";
        if (isset($cities) && count($cities) > 0) {
            foreach ($cities as $city) {
                $html .= "<option value='" . $city->id . "'>" . $city->name . "</option>";
            }
        }
        return response()->json([
            'status' => 200,
            'html'   => $html
        ]);
    }

    private function buildSearchQueries($query)
    {
        $searchConfigs = $this->getSearchConfigurations();
        $queries = [];

        foreach ($searchConfigs as $config) {
            if ($config['type'] === 'match_phrase') {
                $queries[] = $this->buildMatchPhraseQuery($query, $config['field'], $config['boost']);
            } elseif ($config['type'] === 'multi_match') {
                $queries[] = $this->buildMultiMatchQuery($query, $config['fields'], $config['match_type'], $config['operator'], $config['boost']);
            }
        }

        return $queries;
    }

    private function getSearchConfigurations()
    {
        return [
            [
                'type' => 'match_phrase',
                'field' => 'name',
                'boost' => 10
            ],
            [
                'type' => 'match_phrase',
                'field' => 'tags',
                'boost' => 8
            ],
            [
                'type' => 'multi_match',
                'fields' => ['name^5', 'tags^3'],
                'match_type' => 'best_fields',
                'operator' => 'or',
                'boost' => 3
            ],
            [
                'type' => 'multi_match',
                'fields' => ['name^4', 'tags^2'],
                'match_type' => 'most_fields',
                'operator' => 'and',
                'boost' => 2
            ],
            [
                'type' => 'match_phrase',
                'field' => 'description',
                'boost' => 1
            ],
            [
                'type' => 'multi_match',
                'fields' => ['category.name^5', 'subcategory.name^3', 'subsubcategory.name^3'],
                'match_type' => 'best_fields',
                'operator' => 'or',
                'boost' => 3
            ],
            [
                'type' => 'multi_match',
                'fields' => ['category.name^4', 'subcategory.name^2', 'subsubcategory.name^2'],
                'match_type' => 'most_fields',
                'operator' => 'and',
                'boost' => 2
            ]
        ];
    }

    private function buildMatchPhraseQuery($query, $field, $boost)
    {
        return [
            'match_phrase' => [
                $field => [
                    'query' => $query,
                    'boost' => $boost
                ]
            ]
        ];
    }

    private function buildMultiMatchQuery($query, $fields, $type, $operator, $boost)
    {
        return [
            'multi_match' => [
                'query' => $query,
                'fields' => $fields,
                'type' => $type,
                'operator' => $operator,
                'boost' => $boost
            ]
        ];
    }

    public function update_password(Request $request) {
        $validator = Validator::make(
            $request->all(),
            [
                'current_password' => 'required',
                'new_password' => [
                    'required',
                    'confirmed',
                    'regex:' . PasswordPolicy::regex(),
                ],
            ],
            [
                'new_password.regex' => __(
                    'Password must be at least 8 characters and contain at least one uppercase letter, one lowercase letter, and one number.'
                ),
                'new_password.confirmed' => __('Password confirmation does not match.'),
                'current_password.required' => __('Current password is required.'),
            ]
        );
        $validator->after(function ($validator) use ($request) {
            if (!Hash::check($request->current_password, auth()->user()->password)) {
                $validator->errors()->add(
                    'current_password',
                    __('Current password is incorrect')
                );
            }
        });

        $validator->validate();

        $user = Auth::user();
        $this->userService->update($user->id, [
            'password' => Hash::make($request->new_password),
        ]);
        session()->forget('force_change_password');

        flash(__('Password has been updated successfully'))->success();
        return redirect()->route('home');
    }
}
