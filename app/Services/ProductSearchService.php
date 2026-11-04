<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Search;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductSearchService
{
    /** @var CorporateAccessService */
    protected $corporateAccessService;

    /** @var CouponService */
    protected $couponService;

    /** @var CategoryService */
    protected $categoryService;

    /** @var SubCategoryService */
    protected $subCategoryService;

    /** @var SubSubCategoryService */
    protected $subSubCategoryService;

    /** @var ProductService */
    protected $productService;

    /** @var UserService */
    protected $userService;

    /** @var BrandService */
    protected $brandService;

    /** @var MicrositeService */
    protected $micrositeService;

    /** @var AttributeService */
    protected $attributeService;

    /** @var SellerService */
    protected $sellerService;

    public function __construct(
        CorporateAccessService $corporateAccessService,
        CouponService $couponService,
        CategoryService $categoryService,
        SubCategoryService $subCategoryService,
        SubSubCategoryService $subSubCategoryService,
        ProductService $productService,
        UserService $userService,
        BrandService $brandService,
        MicrositeService $micrositeService,
        AttributeService $attributeService,
        SellerService $sellerService
    ) {
        $this->corporateAccessService = $corporateAccessService;
        $this->couponService = $couponService;
        $this->categoryService = $categoryService;
        $this->subCategoryService = $subCategoryService;
        $this->subSubCategoryService = $subSubCategoryService;
        $this->productService = $productService;
        $this->userService = $userService;
        $this->brandService = $brandService;
        $this->micrositeService = $micrositeService;
        $this->attributeService = $attributeService;
        $this->sellerService = $sellerService;
    }

    /**
     * Process product search and return data for the listing view.
     */
    public function process(Request $request): array
    {
        $canViewCSSPage = Auth::user() ? $this->corporateAccessService->canViewCSSPage(Auth::user()) : false;

        $inputs = $this->extractRequestParams($request);
        $query = $inputs['query'];
        $pageSizeOptions = [12, 24, 36, 48];

        list($coupon, $filters) = $this->buildCouponAndFilters($inputs['coupon_id']);

        list($category, $subCategory, $subSubCategory, $categoryIds, $subCategoryIds, $subSubCategoryIds) =
            $this->resolveCategoryEntities($inputs['category_ids'], $inputs['subcategory_ids'], $inputs['subsubcategory_ids']);

        $finalMinPrice = $inputs['min_price'] ?? 0;
        $finalMaxPrice = $inputs['max_price'] ?? 99999;
        $conditions = $this->formatConditionInputs(
            $request,
            $inputs['brand_ids'],
            $category,
            $subCategory,
            $subSubCategory,
            $finalMinPrice,
            $finalMaxPrice,
            $filters
        );

        $companyTypes = $this->determineCompanyTypes($canViewCSSPage, $inputs['storeTypes'], $coupon);
        $userIds = $this->buildUserIds($inputs['seller_ids'], $companyTypes, $filters);
        $productQuery = $this->productService->getListProductFilter($query, $userIds, $conditions);

        if (!empty($query)) {
            $this->logQuery($query);
        }

        list($attributes, $all_colors, $selected_attributes, $selectedAttributeValues) =
            $this->collectAttributeSelections($request, $productQuery);

        $productQuery = $this->applyAttributeFilters($productQuery, $selectedAttributeValues);
        $queryResults = $this->applySubConditions(
            $productQuery,
            $inputs['color'],
            $inputs['rate'],
            $inputs['discount'],
            $inputs['sort_by']
        );

        $products = $queryResults->paginate($inputs['selectedPageSize'])->appends($request->query());

        $suggestions = $this->getSuggestions($query);

        list($brands, $sellers, $categories, $subCategories, $subSubCategories, $sellerIds) =
            $this->computeSidebarFilters($query, $filters);

        $existing_attributes = $this->attributeService->getAttribute();

        $rate = $inputs['rate'];
        $sort_by = $inputs['sort_by'];
        $discount = $inputs['discount'];
        $storeTypes = $inputs['storeTypes'];
        $min_price = $inputs['min_price'];
        $max_price = $inputs['max_price'];
        $brandIds = $inputs['brand_ids'];
        $selectedPageSize = $inputs['selectedPageSize'];
        $selected_color = $inputs['color'];

        return compact(
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
            'attributes',
            'all_colors',
            'selected_color',
            'canViewCSSPage',
            'pageSizeOptions',
            'selectedPageSize',
            'selected_attributes',
            'existing_attributes',
            'category',
            'categories',
            'categoryIds',
            'subCategory',
            'subCategories',
            'subCategoryIds',
            'subSubCategory',
            'subSubCategories',
            'subSubCategoryIds',
            'suggestions'
        );
    }

    private function getSuggestions($query): array
    {
        $suggestions = [];
        if (!empty($query)) {
            // Tìm những sản phẩm có tên tương tự
            $similarProducts = Product::withPublished()
                ->where('name', 'like', '%' . $query . '%')
                ->orWhere('tags', 'like', '%' . $query . '%')
                ->select('name', 'tags')
                ->distinct()
                ->limit(10)
                ->get();

            $suggestionTexts = [];
            foreach ($similarProducts as $product) {
                // Tách từ khóa từ name và tags
                $words = array_merge(
                    explode(' ', strtolower($product->name)),
                    explode(',', strtolower($product->tags))
                );

                foreach ($words as $word) {
                    $word = trim($word);
                    if (strlen($word) > 2 &&
                        stripos($word, $query) !== false &&
                        strtolower($word) !== strtolower($query) &&
                        !in_array($word, $suggestionTexts)) {
                        $suggestionTexts[] = $word;
                    }
                }
            }

            // Tạo suggestions array
            foreach (array_slice($suggestionTexts, 0, 3) as $text) {
                $suggestions[] = [
                    'text' => $text,
                    'highlighted' => $text,
                    'score' => 1.0
                ];
            }
        }
        return $suggestions;
    }

    private function buildCouponAndFilters(?string $couponId): array
    {
        $coupon = null;
        $filters = [];
        if (!empty($couponId)) {
            $coupon = $this->couponService->getFirstWithConditions(['id' => decrypt($couponId)]);
            if (isset($coupon)) {
                $filters = $this->couponService->buildProductsFilterByCoupon($coupon);
            }
        }
        return [$coupon, $filters];
    }

    private function resolveCategoryEntities($category_ids, $subcategory_ids, $subsubcategory_ids): array
    {
        $category = $this->categoryService->findId($category_ids);
        $subCategory = $this->subCategoryService->findId($subcategory_ids);
        $subSubCategory = $this->subSubCategoryService->findId($subsubcategory_ids);

        $categoryIds = isset($category) ? $category_ids : [];
        $subCategoryIds = isset($subCategory) ? $subcategory_ids : [];
        $subSubCategoryIds = isset($subSubCategory) ? $subsubcategory_ids : [];

        return [$category, $subCategory, $subSubCategory, $categoryIds, $subCategoryIds, $subSubCategoryIds];
    }

    private function determineCompanyTypes(bool $canViewCSSPage, $storeTypes, $coupon): array
    {
        $companyTypes = [];
        $companyTypes[] = User::COMPANY_TYPE_FLAG_SHIP;
        if ($canViewCSSPage === true && isset($storeTypes) && !empty($storeTypes)) {
            $companyTypes = [];
            if (isset($storeTypes[2]) && $storeTypes[2] == User::COMPANY_TYPE_PRIVATE_EVENT) {
                $companyTypes[] = 2;
            }
            if (isset($storeTypes[1]) && $storeTypes[1] == User::COMPANY_TYPE_FLAG_SHIP) {
                $companyTypes[] = 1;
            }
        }
        if (isset($coupon)) {
            $companyTypes = [];
        }
        return $companyTypes;
    }

    private function buildUserIds($sellerIds, $companyTypes, $filters)
    {
        $micrositeUserIds = $this->micrositeService->getMicrositeUserIds();
        return $this->sellerService->getUserIdQuery($sellerIds, $companyTypes, $micrositeUserIds, $filters);
    }

    private function collectAttributeSelections(Request $request, $productQuery): array
    {
        $tmp = clone $productQuery;
        list($attributes, $all_colors) = $this->productService->getAttributeAndColorListForFilter($tmp);

        $selected_attributes = [];
        $selectedAttributeValues = [];
        foreach ($attributes as $attribute) {
            if ($request->has('attribute_' . $attribute['id'])) {
                foreach ($request->get('attribute_' . $attribute['id']) as $value) {
                    $selectedAttributeValues[] = '"' . $value . '"';
                }
                $selected_attributes[$attribute['id']] = $request->get('attribute_' . $attribute['id']);
            }
        }

        return [$attributes, $all_colors, $selected_attributes, $selectedAttributeValues];
    }

    private function applyAttributeFilters($productQuery, array $selectedAttributeValues)
    {
        if (!empty($selectedAttributeValues)) {
            $productQuery = $this->productService->filterByAttributeValues($productQuery, $selectedAttributeValues);
        }
        return $productQuery;
    }

    private function applySubConditions($productQuery, $selected_color, $rate, $discount, $sort_by)
    {
        return $this->productService->filterBySubConditions($productQuery, $selected_color, $rate, $discount, $sort_by);
    }

    private function computeSidebarFilters($query, $filters): array
    {
        $validProducts = $this->productService->getValidProducts(['keyword' => $query, 'filters' => $filters]);
        $brands = $this->brandService->getBrandsFilter($validProducts);
        $tpmSellers = $this->userService->getSellerFilter($validProducts);
        $sellerIds = $tpmSellers->pluck('user_id')->toArray();
        $sellers = $this->userService->getByIdsInOrder($sellerIds);
        $categories = $this->categoryService->getCategoryFilter($validProducts);
        $subCategories = $this->subCategoryService->getSubCategoryFilter($validProducts);
        $subSubCategories = $this->subSubCategoryService->getSubSubCategoryFilter($validProducts);

        return [$brands, $sellers, $categories, $subCategories, $subSubCategories, $sellerIds];
    }

    private function extractRequestParams(Request $request): array
    {
        return [
            'query' => $request->get('q', ''),
            'rate' => $request->get('rate', ''),
            'sort_by' => $request->get('sort_by', ''),
            'discount' => $request->get('discount', ''),
            'storeTypes' => $request->get('store_types', null),
            'min_price' => $request->get('min_price'),
            'max_price' => $request->get('max_price'),
            'coupon_id' => $request->get('coupon_id', ''),
            'selectedPageSize' => $request->get('pageSize', 12),
            'brand_ids' => $request->get('brand_ids', []),
            'seller_ids' => $request->get('seller_ids', []),
            'category_ids' => $request->get('category_ids'),
            'subcategory_ids' => $request->get('subcategory_ids'),
            'subsubcategory_ids' => $request->get('subsubcategory_ids'),
            'color' => $request->get('color', null),
        ];
    }

    public function logQuery(string $query): void
    {
        $search = Search::where('query', $query)->first();
        if ($search !== null) {
            $search->count = $search->count + 1;
            $search->save();
        } else {
            $search = new Search();
            $search->query = $query;
            $search->save();
        }
    }

    private function formatConditionInputs($request, $brandIds, $category, $subCategory, $subSubCategory, $finalMinPrice, $finalMaxPrice, $filters)
    {
        $conditions = [];
        if (!empty($brandIds)) {
            $conditions = array_merge($conditions, ['brand_id' => $request->brand_ids]);
        }

        if (!empty($category)) {
            $conditions = array_merge($conditions, ['category_id' => $request->category_ids]);
        }

        if (!empty($subCategory)) {
            $conditions = array_merge($conditions, ['sub_category_id' => $request->subcategory_ids]);
        }

        if (!empty($subSubCategory)) {
            $conditions = array_merge($conditions, ['sub_sub_category_id' => $request->subsubcategory_ids]);
        }

        if (isset($finalMinPrice)) {
            $conditions = array_merge($conditions, ['finalMinPrice' => $finalMinPrice]);
        }

        if (isset($finalMaxPrice)) {
            $conditions = array_merge($conditions, ['finalMaxPrice' => $finalMaxPrice]);
        }

        if (!empty($filters)) {
            if (!empty($filters['subSubCateIds']) && empty($conditions['sub_sub_category_id'])) {
                $conditions = array_merge($conditions, ['sub_sub_category_id' => $filters['subSubCateIds']]);
            }

            if (!empty($filters['subCateIds']) && empty($conditions['sub_category_id'])) {
                $conditions = array_merge($conditions, ['sub_category_id' => $filters['subCateIds']]);
            }

            if (!empty($filters['cateIds']) && empty($conditions['category_id'])) {
                $conditions = array_merge($conditions, ['category_id' => $filters['cateIds']]);
            }

            if (!empty($filters['productIds'])) {
                $conditions = array_merge($conditions, ['product_id' => $filters['productIds']]);
            }
        }

        return $conditions;
    }
}


