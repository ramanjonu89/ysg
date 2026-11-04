<?php
namespace App\Http\Controllers\Frontend;

use App\Attribute;
use App\Models\Category;
use App\Seller;
use App\Services\BrandService;
use App\Services\CategoryService;
use App\Services\ProductService;
use App\Services\SellerService;
use App\Shop;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CSSProductController extends Controller
{
    protected $service;
    protected $categoryService;
    protected $brandService;
    protected $sellerService;

    protected $categories;
    protected $CASellerList;
    protected $CABrandList;

    protected $currentCategory;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        ProductService $service,
        CategoryService $categoryService,
        BrandService $brandService,
        SellerService $sellerService
    )
    {
        $this->service = $service;
        $this->categoryService = $categoryService;
        $this->brandService = $brandService;
        $this->sellerService = $sellerService;
    }

    protected function initData($request) {
        $this->categories = $this->categoryService->getTree();
        $this->CASellerList = $this->sellerService->getCAList();
        $this->CABrandList = $this->brandService->getSellersBaseList($this->CASellerList);

        if ($request->category) {
            if ($request->level == 1) {
                $this->currentCategory = $this->categoryService->get($request->category);
            } elseif ($request->level == 2) {
                $this->currentCategory = $this->categoryService->getSub($request->category);
            } elseif ($request->level == 3) {
                $this->currentCategory = $this->categoryService->getSubSub($request->category);
            }
        }
    }

    public function index(Request $request)
    {

        $this->initData($request);

        $attributes             = [];
        $selected_attributes    = [];
        $all_colors             = [];
        $selected_color         = [];

        $categoryId             = $request->category??null;
        if ($categoryId) {
            $categoryLevel      = $request->level??1;
            if ($categoryLevel  == 1) {
                $categoryIds    = [$categoryId];
            } elseif ($categoryLevel == 2) {
                $subCategoryIds = [$categoryId];
            } elseif ($categoryLevel == 3) {
                $subSubCategoryIds = [$categoryId];
            }
        }


        $requestQuery   = request()->query();
        $breadCrumb     = $this->getBreadCrumbData($requestQuery);

        $params = [
            'categoryIds'       => $categoryIds??null,
            'subCategoryIds'    => $subCategoryIds??null,
            'subSubCategoryIds' => $subSubCategoryIds??null,
            'sellerIds'         => $request->seller_ids ?? [],
            'brandIds'          => $request->brand_ids ?? [],
            'finalMinPrice'     => $request->min_price ?? null,
            'finalMaxPrice'     => $request->max_price ?? null,
            'search'            => $request->q ?? null,
            'sortBy'            => $request->sort_by ?? null,
        ];
        $products   = $this->service->getCAList($params);
        $products->appends($requestQuery);

        $existing_attributes    = Attribute::all()->pluck('name', 'id')->toArray();
        $categories             = $this->categories;
        $sellers                = $this->CASellerList;
        $brands                 = $this->CABrandList;
        $meta                   = $this->getMeta();
        $sortTypes              = $this->service->getSortTypes();

        return view(
            'frontend.css_product_list',
            compact(
                'meta',
                'breadCrumb',
                'requestQuery',
                'sortTypes',
                'products',
                'categories',
                'brands',
                'sellers',
                'selected_attributes',
                'attributes',
                'selected_color',
                'all_colors',
                'existing_attributes',
            )
        );
    }

    /**
     * @param $requestQuery
     * @return array
     */
    private function getBreadCrumbData($requestQuery): array
    {
        $result = [];
        $result[] = [
            'link' => route('home'),
            'title' => __('Home'),
            'active' => false,
        ];
        if ($this->currentCategory) {
            $result[] = [
                'link' => route('products.css'),
                'title' => __('All Category'),
                'active' => false,
            ];
            $temp = $this->getCategoryBreadCrumbData($this->currentCategory, $requestQuery);
            $result = array_merge($result, $temp);
        } else {
            $result[] = [
                'link' => route('products.css'),
                'title' => __('All Category'),
                'active' => true,
            ];
        }
        return $result;
    }

    /**
     * @param $cate
     * @param $requestQuery
     * @return array
     */
    private function getCategoryBreadCrumbData($cate, $requestQuery): array
    {
        $result = [];
        $parent = $cate->parent;
        if ($parent) {
            $temp = $this->getCategoryBreadCrumbData($parent, $requestQuery);
            $result = array_merge($result, $temp);
        }
        $requestQuery['category'] = $cate->id;
        $requestQuery['level'] = $cate->level;
        $result[] = [
            'link' => route('products.css', $requestQuery),
            'title' => $cate->name,
            'active' => ($this->currentCategory->id == $cate->id),
        ];
        return $result;
    }

    /**
     * @return array
     */
    private function getMeta(): array
    {
        if ($this->currentCategory) {
            $result = [
                'metaTitle' => $this->currentCategory->meta_title,
                'metaDescription' => $this->currentCategory->meta_description,
            ];
        } else {
            $result = [
                'metaTitle' => env('APP_NAME'),
                'metaDescription' => \App\SeoSetting::first()->description,
            ];
        }
        return $result;
    }


    public function get_attribute_and_color_list_for_filter($productQuery)
    {
        $non_paginate_products = $productQuery
            ->where('colors', '!=', '[]')
            ->orWhere('attributes', '!=', '[]')
            ->get();
        $attributes = [];
        $all_colors = [];
        foreach ($non_paginate_products as $product) {
            if ($product->attributes != '[]') {
                $currentAttributes = json_decode($product->attributes);
                foreach ($currentAttributes as $attributeId) {
                    if (!array_key_exists($attributeId, $attributes)) {
                        $attributes[$attributeId]['id'] = $attributeId;
                        $attributes[$attributeId]['values'] = [];
                    }
                    $currentOptions = json_decode($product->choice_options);
                    foreach ($currentOptions as $choice_option) {
                        if ($choice_option->attribute_id == $attributeId) {
                            foreach ($choice_option->values as $choice) {
                                if (!in_array($choice, $attributes[$attributeId]['values'])) {
                                    $attributes[$attributeId]['values'][] = $choice;
                                }
                            }
                        }
                    }
                }
            }
            if ($product->colors != '[]') {
                foreach (json_decode($product->colors) as $color) {
                    if (!in_array($color, $all_colors)) {
                        $all_colors[] = $color;
                    }
                }
            }
        }

        return [$attributes, $all_colors];
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
            if ($shop->user->company_type == User::COMPANY_TYPE_PRIVATE_EVENT
                && ($startDate <= $now)
                && ($endDate > $now)
                && ($needAccessCode || $needDomain)
                && !$isValidDomain)
            {
                if (is_null($currentUser)) {
                    flash(__('Please login using a valid account to enter the Private Sales Store!'), 'warning');
                    return redirect()->route('user.login');
                }
            }

            if ($shop->user->enable_corporate_domains === 1 && !$isValidDomain) {
                $text1 = __('This store can only be accessed by companies enrolled in YSG’s');
                $text2 = __('Corporate Sales Store Employee Benefits Programme');
                $text3 = __('Programme');
                $text4 = __('If you are still having difficulty logging in, please contact YSG at cs@ysg.com.sg');
                $popupContent = '<div class="swal-content">'.$text1.' '.
                    '<a href="https://ysg.com.sg/Corporate-Sales---Employee-Benefits-Programme">'.$text2.'</a> '.$text3.' '.
                    '<br>'.$text4.'</div>';
                flash($popupContent, 'warning');
                return redirect()->route('home');
            }
            $category_ids = $request->get('category_ids') ?? [];
            $brand_ids = $request->get('brand_ids') ?? [];
            $sort_by = $request->get('sort_by');
            $keyword = $request->get('q');
            $min_price = $request->get('min_price');
            $max_price = $request->get('max_price');
            $productQuery = DB::table('products')
                ->select('*', DB::raw("CASE
                    WHEN (!isnull(discount) AND discount_type = 'amount') THEN (unit_price - discount)
                    WHEN (!isnull(discount) AND discount_type = 'percent') THEN (unit_price * (100 - discount) / 100)
                    ELSE unit_price END AS final_price"))
                ->where('user_id', $shop->user->id)
                ->where('published', 1)
                // ->where('current_stock', '>', 0)
                ->where(function($query) use ($category_ids, $brand_ids, $min_price, $max_price) {
                    if (!empty($category_ids)) {
                        $query->whereIn('category_id', $category_ids);
                    }
                    if (!empty($brand_ids)) {
                        $query->whereIn('brand_id', $brand_ids);
                    }
                    if ($min_price) {
                        $query->having('final_price', '>=', $min_price);
                    }
                    if ($max_price) {
                        $query->having('final_price', '<=', $max_price);
                    }
                })->where(function($query) use ($keyword) {
                    if (!empty($keyword)) {
                        $query->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('tags', 'like', '%' . $keyword . '%')
                            ->orWhere('unit', 'like', '%' . $keyword . '%');
                    }
                });
            $tmp = clone $productQuery;
            list($attributes, $all_colors) = $this->get_attribute_and_color_list_for_filter($tmp);
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
            } elseif ($sort_by) {
                switch ($sort_by) {
                    case '1':
                        $productQuery->orderBy('created_at', 'desc');
                        break;
                    case '2':
                        $productQuery->orderBy('created_at', 'asc');
                        break;
                    case '3':
                        $productQuery->orderBy('final_price', 'asc');
                        break;
                    case '4':
                        $productQuery->orderBy('final_price', 'desc');
                        break;
                    default:
                        break;
                }
            }
            $products = $productQuery
                ->paginate(24)
                ->appends(request()->query());
            $allBrandsAndCategories = DB::table('view_product_full')
                ->where('published', 1)
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
                return view('frontend.seller_shop', compact(
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
}

