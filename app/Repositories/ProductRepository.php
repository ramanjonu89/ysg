<?php
namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductStock;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductRepository extends AbstractRepo
{
    use SortableTrait;

    const PER_PAGE = 16;
    const UNLIMITED_NUMBER = 9999;

    public function getModel(): string
    {
        return Product::class;
    }

    public function getBySlug($slug) {
        return $this->model->where('slug', $slug)->first();
    }

    public function getList($params)
    {
        $userIds = $params['userIds'] ?? null;
        $categoryId = $params['categoryId'] ?? null;
        $categoryIds = $params['categoryIds'] ?? null;
        $subCategoryIds = $params['subCategoryIds'] ?? null;
        $subSubCategoryIds = $params['subSubCategoryIds'] ?? null;
        $brandIds = $params['brandIds'] ?? null;
        $finalMinPrice = $params['finalMinPrice'] ?? 0;
        $finalMaxPrice = $params['finalMaxPrice'] ?? 99999;
        $search = $params['search'] ?? null;
        $orderBy = $params['orderBy'] ?? null;
        $isCa = $params['isCA'] ?? null;

        $query = $this->model->query();
        $query->leftJoin('users', 'products.user_id', 'users.id');
        $query->leftJoin('sellers', 'sellers.user_id', 'users.id');
        $query->leftJoin('brands', 'products.brand_id', 'brands.id');
        $query->where('products.published', 1);
        $query->categoryEnabled();
        $query->brandEnabled();
        $query->subCategoryEnabled();
        $query->subSubcategoryEnabled();
        // $query->where('products.current_stock', '>', 0);
        $query->where('sellers.verification_status', 1);
        $query->select(
            'products.*',
            DB::raw(
                "(CASE ".
                    "WHEN (!isnull(products.discount) AND products.discount_type = 'amount') THEN (products.unit_price - products.discount) ".
                    "WHEN (!isnull(products.discount) AND products.discount_type = 'percent') THEN (products.unit_price * (100 - products.discount) / 100) ".
                    "ELSE products.unit_price ".
                "END) as final_price"
            ),
            'brands.name as brand_name'
        );

        if ($userIds) {
            $query->whereIn('products.user_id', $userIds);
        }

        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }

        if ($categoryIds) {
            $query->whereIn('products.category_id', $categoryIds);
        }

        if ($subCategoryIds) {
            $query->whereIn('products.subcategory_id', $subCategoryIds);
        }

        if ($subSubCategoryIds) {
            $query->whereIn('products.subsubcategory_id', $subSubCategoryIds);
        }

        if ($brandIds) {
            $query->whereIn('products.brand_id', $brandIds);
        }

        if ($search) {
            $query->where('products.name', 'like', '%' . $search . '%');
        }

        if ($isCa) {
            $query->where('users.company_type', '=', User::COMPANY_TYPE_PRIVATE_EVENT);
        }

        if ($finalMinPrice || $finalMaxPrice) {
            $query
                ->whereRaw(
                    "(CASE ".
		                "WHEN (!isnull(products.discount) AND products.discount_type = 'amount') THEN (products.unit_price - products.discount) ".
                        "WHEN (!isnull(products.discount) AND products.discount_type = 'percent') THEN (products.unit_price * (100 - products.discount) / 100) ".
                        "ELSE products.unit_price ".
                    "END) BETWEEN ? AND ?",
                    [$finalMinPrice, $finalMaxPrice]
                );
        }

        if (isset($params['enable_corporate_domains'])) {
            $query->where('sellers.verification_status', 1)
                    ->where('users.enable_corporate_domains', $params['enable_corporate_domains']);
        }

        if (isset($params['company_type'])) {
            $query->where('users.company_type', $params['company_type']);
        }

        if (isset($params['featured'])) {
            $query->where('products.featured', $params['featured']);
        }

        if ($orderBy) {
            $orderDirection = $params['orderDirection'] ?? 'asc';
            $query->orderBy($orderBy, $orderDirection);
        }

        if (isset($params['order_rand'])) {
            $query->orderByRaw('RAND()');
        } else {
            $query->orderBy('products.created_at', 'desc');
        }

        if (isset($params['limit'])) {
            $query->limit($params['limit']);
        }

        if (!isMicrosite()) {
            $query->where('products.only_microsite', false);
        }

        if (!empty($params['paginate'])) {
            $perPage = $params['perPage'] ?? self::PER_PAGE;
            if ($perPage != 0) {
                return $query->paginate($perPage);
            }
        }

        return $query->get();
    }

    public function findByUnit($code)
    {
        return $this->model::where('unit', '=', strtoupper($code))->first();
    }

    public function getRelatedProducts($product)
    {
        $query = $this->model->where('subcategory_id', $product->subcategory_id)
            // ->where('current_stock','>',0)
            ->where('id', '!=', $product->id)
            ->where('published', '1')
            ->whereExists(function($q) {
                $q->select(DB::raw(1))
                    ->from('users')
                    ->whereRaw('users.id = products.user_id')->whereRaw('users.company_type = 1')
                    ->whereExists(function($q) {
                        $q->select(DB::raw(1))
                            ->from('sellers')
                            ->whereRaw('users.id = sellers.user_id')
                            ->whereRaw('sellers.verification_status = 1');
                    })
                ;
            })
        ;

        if (!isMicrosite()) {
            $query->where('only_microsite', false);
        } else {
            $query->where('user_id', $product->user_id);
        }

        return $query->limit(10)->get();
    }

    public function getTopSellingProducts($userId)
    {
        $query = $this->model->where('user_id', $userId)
            // ->where('current_stock','>',0)
            ->where('published', '1')
            ->orderBy('num_of_sale', 'desc')
        ;

        if (!isMicrosite()) {
            $query->where('only_microsite', false);
        }

        return $query->limit(6)->get();
    }

    public function getTopSellingProductBySeller($product)
    {
        $query = $this->model->where('id', '!=', $product->id)
            ->where('published', '1')
            ->where('user_id', $product->user_id)
            ->orderBy('num_of_sale', 'desc');

        return $query->limit(6)->get();
    }

    public function getProductCurrentStock($id, string $variation)
    {
        $product = $this->find($id);

        return $product->getStock($variation);
    }

    public function getProductsWithVariant(User $user = null, array $params = [])
    {
        $query = $this->model->query();
        $query->select([
            'products.id',
            'products.name',
            'products.unit',
            'products.current_stock',
            'products.thumbnail_img',
            'products.unit_price',
            'products.user_id',
            'products.category_id',
            'products.brand_id',
            'products.published',
            'product_stocks.id as variant_id',
            'product_stocks.variant',
            'product_stocks.sku',
            'product_stocks.image',
            'product_stocks.price as variant_price',
            'product_stocks.product_id',
        ]);

        $query->leftJoin('product_stocks', 'products.id', 'product_stocks.product_id');

        if ($user) {
            $query->where('products.user_id', $user->id);
        }

        if (isset($params['order_by'])) {
            $query->orderBy($params['order_by'], $params['order_by_direction'] ?? 'desc');
        }

        $query->orderBy('products.id', 'desc');

        if (!empty($params['out_of_stock']) && $params['out_of_stock'] == 1) {
            $query->where('current_stock', 0);
        }

        if (isset($params['published'])) {
            $query->where('published', $params['published']);
        }

        if (!empty($params['category'])) {
            $query->where('category_id', $params['category']);
        }

        if (!empty($params['brand'])) {
            $query->where('brand_id', $params['brand']);
        }

        if (!empty($params['keyword'])) {
            $keyword = $params['keyword'];
            $query->where(function($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('unit', 'like', '%' . $keyword . '%');
            });
        }

        if (!empty($params['sku'])) {
            $query->where('unit', 'like', '%' . $params['sku'] . '%')
                ->orWhere('sku', 'like', '%' . $params['sku'] . '%')
            ;
        }

        if (!empty($params['ids'])) {
            $productIds = [];
            $variantIds = [];
            foreach($params['ids'] as $id) {
                if (isVariantID($id)) {
                    $variantIds[] = getVariantID($id);
                } else {
                    $productIds[] = $id;
                }
            }

            $isBoth = !empty($productIds) && !empty($variantIds);
            if ($isBoth) {
                $query->where(function ($query) use ($productIds, $variantIds) {
                    $query->whereIn('products.id', $productIds)
                    ->orWhereIn('product_stocks.id', $variantIds);
                });
            } else {
                if (!empty($productIds)) {
                    $query->whereIn('products.id', $productIds);
                }
                if (!empty($variantIds)) {
                    $query->whereIn('product_stocks.id', $variantIds);
                }
            }
        }

        if (!empty($params['excluded'])) {
            $query->whereNotIn('products.id', $params['excluded']);
        }

        $limit = $this->getLimit($params);

        if ($limit) {
            return $query->paginate($limit);
        }

        return $query->paginate(10000);
    }

    public function getProducts(User $user = null, array $params = [])
    {
        $productStocks = DB::table('product_stocks')
            ->select(
                'product_id',
                DB::raw('MIN(price) as min_price'),
                DB::raw('MAX(price) as max_price'),
                'sku as sku_variant'
            )
            ->groupBy('product_id', 'sku_variant');

        $query = $this->model->query();

        $query->leftJoinSub($productStocks, 'product_stocks', function ($join) {
            $join->on('products.id', '=', 'product_stocks.product_id');
        });

        if ($user) {
            $query->where('user_id', $user->id);
        }
        $query->where(function ($query) {
            $query->where('unit', 'NOT LIKE', 'deleted%')
                ->orWhereNull('unit');
        });

        if (isset($params['order_by'])) {
            $query->orderBy($params['order_by'], $params['order_by_direction'] ?? 'desc');
        }

        $query->orderBy('id', 'desc');

        if (!empty($params['out_of_stock']) && $params['out_of_stock'] == 1) {
            $query->where('current_stock', 0);
        }

        if (isset($params['published'])) {
            $query->where('published', $params['published']);
        }

        if (!empty($params['category'])) {
            $query->where('category_id', $params['category']);
        }

        if (!empty($params['brand'])) {
            $query->where('brand_id', $params['brand']);
        }

        if (!empty($params['keyword'])) {
            $keyword = $params['keyword'];
            $query->where(function($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('unit', 'like', '%' . $keyword . '%')
                    ->orWhere('sku_variant', 'like', '%' . $keyword . '%');
            });
        }

        if (!empty($params['sku'])) {
            $query->where('unit', 'like', '%' . $params['sku'] . '%');
        }

        if (!empty($params['ids'])) {
            $query->whereIn('id', $params['ids']);
        }

        if (!empty($params['excluded'])) {
            $query->whereNotIn('id', $params['excluded']);
        }

        $query->groupBy('id');
        $limit = $this->getLimit($params);

        if ($limit) {
            return $query->paginate($limit);
        }

        return $query->paginate(10000);
    }

    public function getSellerProducts($request){
        $search     = null;
        $products   = $this->model->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $search = $request->search;
            $products = $products->where('name', 'like', '%' . $search . '%')->orwhere('unit', 'like', '%' . $search . '%');
        }
        return $products->get();
    }

    public function getTotalPublishedProducts() {
        return DB::table('products')->where('published', 1)->count();
    }
    public function getTotalSellerPublishedProducts() {
        return DB::table('products')->where('published', 1)->where('added_by', 'seller')->count();
    }
    public function getTotalAdminPublishedProducts() {
        return DB::table('products')->where('published', 1)->where('added_by', 'admin')->count();
    }

    public function getStockReport($params)
    {
        if ($params['category_id'] === '') {
            return [];
        }

        $query = DB::table('products')
                ->leftJoin('users', 'products.user_id', 'users.id')
                ->select(['products.id', 'products.name', 'products.unit', 'products.current_stock', 'users.name as shopName'])
                ->addSelect(['stocks' => ProductStock::selectRaw('sum(product_stocks.qty)')->whereRaw('product_stocks.product_id = products.id')])
                ->where('products.category_id', $params['category_id'])
                ;

        if (!empty($params['user_id'])) {
            $query->where('products.user_id', $params['user_id']);
        }

        return $query->get();

    }

    public function getListProductFilter($params, $conditions)
    {
        
        $query = Product::microsite()->onlyUnexpiredSellers()
                            ->categoryEnabled()->brandEnabled()
                            ->subCategoryEnabled()->subSubCategoryEnabled()
                            ->where($conditions);

        $query->where(function ($query) {
            $query->where('unit', 'NOT LIKE', 'deleted%');
        });
        if (!empty($params['userIds'])) {
            $query->whereIn('user_id', $params['userIds']);
        }

        if  (!empty($params['shopId'])) {
            $query->where('user_id', $params['shopId']);
        }

        if (!empty($params['categoryIds'])) {
            $query->whereIn('category_id', $params['categoryIds']); 
        }

        if (!empty($params['subCategoryIds'])) {
            $query->whereIn('subcategory_id', $params['subCategoryIds']);
        }

        if (!empty($params['subSubCategoryIds'])) {
            $query->whereIn('subsubcategory_id', $params['subSubCategoryIds']);
        }

        if (!empty($params['brandIds'])) {
            $query->whereIn('products.brand_id', $params['brandIds']);
        }

        if (!empty($params['productIds'])) {
            $query->whereIn('products.id', $params['productIds']);
        }

        if (!empty($params['keyword'])) {
            $keyword = $params['keyword'];
            $query->where(function($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('tags', 'like', '%' . $keyword . '%')
                    ->orWhere('unit', 'like', '%' . $keyword . '%');
            });
        }

        $finalMinPrice = $params['finalMinPrice']??0;
        $finalMaxPrice = $params['finalMaxPrice']??99999;
        if ($finalMinPrice || $finalMaxPrice) {
            $query->where('max_final_price', '>=', $finalMinPrice);
            $query->where('min_final_price', '<=', $finalMaxPrice);
        }

        return $query;
    }

    public function filterBySubConditions($params)
    {
        $rate           = $params['rate'];
        $sort_by        = $params['sort_by'];
        $discount       = $params['discount'];
        $productQuery   = $params['productQuery'];
        $selected_color = $params['selected_color'];

        if ($selected_color) {
            $str = '"' . $selected_color . '"';
            $productQuery->where('colors', 'like', '%' . $str . '%');
        }
        if(!empty($rate) && $rate != ''){
            $productQuery->where('rating','>=', $rate);
        }
        if(!empty($discount) && $discount != ''){
            $productQuery->where('discount','<>', NULL)->where('discount','<>',0);
        }
        if(!empty($sort_by)) {
            $productQuery = $this->applySortBy($sort_by, $productQuery);
        }
        return $productQuery;
    }

    public function applySortBy($sort_by, $productQuery)
    {
        switch ($sort_by) {
            case '1':
                $productQuery->select(array_merge(
                    ['products.*'],
                    [DB::raw("COALESCE(
                        (SELECT MAX(
                            CASE
                                WHEN ps.price > 0 AND ps.discount IS NOT NULL AND ps.discount_type = 'amount' THEN (ps.discount * 100 / ps.price)
                                WHEN ps.discount IS NOT NULL AND ps.discount_type = 'percent' THEN ps.discount
                                ELSE 0
                            END
                        ) FROM product_stocks ps WHERE ps.product_id = products.id),
                        CASE
                            WHEN products.unit_price > 0 AND products.discount IS NOT NULL AND products.discount_type = 'amount' THEN (products.discount * 100 / products.unit_price)
                            WHEN products.discount IS NOT NULL AND products.discount_type = 'percent' THEN products.discount
                            ELSE 0
                        END
                    ) AS discount_percentage")]
                ))->orderBy('discount_percentage', 'desc');
                break;
            case '2':
                $productQuery->orderBy('created_at', 'desc');
                break;
            case '3':
                $productQuery->orderBy('min_final_price', 'asc');
                break;
            case '4':
                $productQuery->orderBy('max_final_price', 'desc');
                break;
            case '5':
                $productQuery->orderBy('created_at', 'asc');
                break;
            default:
                $productQuery->orderBy('products.frontend_sorting', 'desc');
                break;
        }

        return $productQuery;
    }

    public function filterByAttributeValues($productQuery, $selectedAttributeValues){
        $query = $productQuery->where(function ($q) use ($selectedAttributeValues) {
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

        return $query;
    }

    public function getValidProducts($params)
    {
        $validProducts = Product::microsite()->onlyUnexpiredSellers()
                                ->categoryEnabled()->brandEnabled()->subCategoryEnabled()
                                ->subSubcategoryEnabled()->where('published', 1)
                                ->leftJoin('users', 'products.user_id', 'users.id')
                                ->leftJoin('sellers', 'users.id', 'sellers.user_id')
                                ->where('sellers.verification_status', 1);

        if (!empty($params['shopId'])) {
            $validProducts->where('products.user_id', $params['shopId']);
        }

        if (isset($params['productIds'])) {
            $validProducts->whereIn('products.id', $params['productIds']);
        }

        if (!empty($params['cateIds'])) {
            $validProducts->whereIn('products.category_id', $params['cateIds']); 
        }

        if (!empty($params['subCateIds'])) {
            $validProducts->whereIn('products.subcategory_id', $params['subCateIds']);
        }

        if (!empty($params['subSubCateIds'])) {
            $validProducts->whereIn('products.subsubcategory_id', $params['subSubCateIds']);
        }

        if (isset($params['shopIds'])) {
            $validProducts->whereIn('products.user_id', $params['shopIds']);
        }

        if (!empty($params['keyword'])) {
            $keyword = $params['keyword'];
            $validProducts->where(function($query) use ($keyword) {
                $query->where('products.name', 'like', '%' . $keyword . '%')
                        ->orWhere('products.tags', 'like', '%' . $keyword . '%')
                        ->orWhere('products.unit', 'like', '%' . $keyword . '%');
            });
        }

        return $validProducts;
    }

    public function getAttributeAndColorListForFilter($products)
    {
        $nonPaginateProducts = collect($products)
                ->filter(function($product) {
                    return $product->colors != '[]' || $product->choice_options != '[]';
                });
        $attributes = [];
        $allColors = [];
        foreach ($nonPaginateProducts as $product) {
            if ($product->choice_options != '[]') {
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
                    if (!in_array($color, $allColors)) {
                        $allColors[] = $color;
                    }
                }
            }
        }

        return [$attributes, $allColors];
    }


    public function getMaxProductSortingBySeller($user)
    {
        $maxSorting = DB::table('products')
                        ->where('user_id', $user->id)
                        ->max('frontend_sorting');

        return $maxSorting;
    }
}
