<?php
namespace App\Http\Controllers\Frontend;

use App\Models\AgeRestrictedSetting;
use App\Services\{AttributeService,
    BrandService,
    CategoryService,
    ProductService,
    ShopService,
    StoreAccessService};
use Illuminate\Http\Request;

class SellerShopController extends Controller
{
    /**
     * @param BrandService
     */
    protected $brandService;

    /**
     * @param ShopService
     */
    protected $shopService;

    /**
     * @param ProductService
     */
    protected $productService;

    /**
     * @param CategoryService
     */
    protected $categoryService;

    /**
     * @param StoreAccessService
     */
    protected $storeAccessService;

    /**
     * @param AttributeService
     */
    protected $attributeService;

    public function __construct (
        ShopService $shopService,
        BrandService $brandService,
        ProductService $productService,
        CategoryService $categoryService,
        AttributeService $attributeService,
        StoreAccessService $storeAccessService
    ) {
        $this->shopService = $shopService;
        $this->brandService = $brandService;
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->attributeService = $attributeService;
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
        $shop = $this->shopService->getBySlug($slug);
        if ($this->storeAccessService->isNeedAccessCode($shop)) {
            $title = $shop->access_code_message;
        } else {
            $title = sprintf('You are trying to access %s, this shop required an access code, please fill below', $shop->name);
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

    public function visit(Request $request, $slug, $type = null)
    {
        $shop = $this->shopService->getBySlug($slug);
        $keyword = $request->get('q');

        $validProducts = $this->productService->getValidProducts(['shop' => $shop, 'keyword' => $keyword]);
        $categories    = $this->categoryService->getCategoryFilter($validProducts);

        $isShopAdult = $this->shopService->isShopAdult($categories);
        $isConfirmAge = session()->get('is_confirm_age');

        if ($isShopAdult && $isConfirmAge !== AgeRestrictedSetting::TWENTYONE_AND_ABOVE) {
            return redirect()->route('age_restricted.get_form_confirm', ['value' => AgeRestrictedSetting::TWENTYONE_AND_ABOVE, 'slug' => $slug, 'shop' => $shop]);
        }

        return $this->viewShop($request, $shop, $type);
    }

    private function viewShop(Request $request, $shop, $type = null)
    {
        $user = $shop->user;
        if (isset($user) && $user->isExpired()) {
            return abort(404);
        }
        $seller = $shop->seller;
        $currentUser = $request->user();

        $categoryIds = $request->get('category_ids') ?? [];
        $brandIds = $request->get('brand_ids') ?? [];
        $sort_by = $request->get('sort_by');
        $keyword = $request->get('q');
        $min_price = $request->get('min_price');
        $max_price = $request->get('max_price');
        $finalMinPrice = $min_price ?? 0;
        $finalMaxPrice = $max_price ?? 99999;
        $selectedPageSize = isset($request->pageSize) ? $request->pageSize : 12;
        $pageSizeOptions = [12, 24, 36, 48];
        $productQuery = $this->productService->getShopProductsList($categoryIds, $brandIds, $keyword, $shop->user->id, $finalMinPrice, $finalMaxPrice);
        $tmp = clone $productQuery;
        list($attributes, $all_colors) = $this->productService->getAttributeAndColorListForFilter($tmp);
        $existing_attributes = $this->attributeService->getAttribute();
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
            $productQuery->where(function($q) use ($selectedAttributeValues) {
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
        } else {
            $productQuery = $this->productService->applySortBy($sort_by, $productQuery);
        }

        $products = $productQuery
            ->paginate($selectedPageSize)
            ->appends(request()->query());

        $blocks = $this->shopService->getActivatedBlocks($shop);
        $featuredProducts = $this->shopService->getFeaturedProducts($shop);

        $validProducts = $this->productService->getValidProducts(['shop' => $shop, 'keyword' => $keyword]);
        $brands        = $this->brandService->getBrandsFilter($validProducts);
        $categories    = $this->categoryService->getCategoryFilter($validProducts);
        
        $isShopAdult = $this->shopService->isShopAdult($categories);
        $is_include_filter = ($type === 'all_products');
        if ($seller->verification_status != 0) {
            return view('frontend.seller_shop.index', compact(
                    'shop',
                    'currentUser',
                    'type',
                    'is_include_filter',
                    'categories',
                    'brands',
                    'products',
                    'blocks',
                    'featuredProducts',
                    'categoryIds',
                    'brandIds',
                    'keyword',
                    'sort_by',
                    'min_price',
                    'max_price',
                    'attributes',
                    'selected_attributes',
                    'all_colors',
                    'selected_color',
                    'existing_attributes',
                    'pageSizeOptions',
                    'selectedPageSize',
                    'isShopAdult'
                )
            );
        }

        return view('frontend.seller_shop_without_verification', compact('shop', 'seller'));
    }
}

