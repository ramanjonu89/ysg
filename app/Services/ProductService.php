<?php
namespace App\Services;

use App\Repositories\ProductRepository;
use App\User;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class ProductService extends AbstractService
{
    protected $repo;
    protected $sellerService;
    protected $productStockService;

    public function __construct(ProductRepository $repo, SellerService $sellerService, ProductStockService $productStockService)
    {
        $this->repo = $repo;
        $this->sellerService = $sellerService;
        $this->productStockService = $productStockService;
        parent::__construct($repo);
    }

    /**
     * @return array
     */
    public function getSortTypes(): array
    {
        return [
            'newest' => __('Newest'),
            'oldest' => __('Oldest'),
            'lowToHigh' => __('Price low to high'),
            'highToLow' => __('Price high to low'),
        ];
    }

    /**
     * @param $sortBy
     * @return array|string[]
     */
    private function getSort($sortBy): array
    {
        $result = [null, null];
        if ($sortBy) {
            switch ($sortBy) {
                case 'newest':
                    $result = ['created_at', 'desc'];
                    break;
                case 'oldest':
                    $result = ['created_at', 'asc'];
                    break;
                case 'lowToHigh':
                    $result = ['final_price', 'asc'];
                    break;
                case 'highToLow':
                    $result = ['final_price', 'desc'];
                    break;
                default:
                    break;
            }
        }
        return $result;
    }

    /**
     * get list of CA seller for display at front end
     * @param  array  $params
     * @return mixed
     */
    public function getCAList(array $params)
    {
        $sellerIds = $params['sellerIds'] ?? null;
        $search = $params['search'] ?? null;
        $sortBy = $params['sortBy'] ?? null;

        list($orderBy, $orderDirection) = $this->getSort($sortBy);

        if ($sellerIds) {
            $sellers = $this->sellerService->getList([
                'ids' => $sellerIds,
                'perPage' => 0,
            ]);
            $userIds = $sellers->pluck('user_id');
        } else $userIds = null;

        $params = [
            'userIds' => $userIds,
            'categoryIds' => $params['categoryIds'] ?? null,
            'subCategoryIds' => $params['subCategoryIds'] ?? null,
            'subSubCategoryIds' => $params['subSubCategoryIds'] ?? null,
            'brandIds' => $params['brandIds'] ?? null,
            'finalMinPrice' => $params['finalMinPrice'] ?? null,
            'finalMaxPrice' => $params['finalMaxPrice'] ?? null,
            'search' => trim($search),
            'orderBy' => $orderBy,
            'orderDirection' => $orderDirection,
            'paginate' => true,
            'isCA' => true,
        ];

        return $this->repo->getList($params);
    }

    public function getHomeFeaturedProducts()
    {
        $params = [
            'company_type' => User::COMPANY_TYPE_FLAG_SHIP,
            'featured' => 1,
            // 'orderBy' => 'featured_sorting',
            // 'orderDirection' => 'asc',
            'order_rand' => true,
            'limit' => 12,
            'paginate' => false,
        ];

        return $this->repo->getList($params);
    }

    public function getHomeBestSellingProducts()
    {
        $params = [
            'company_type' => User::COMPANY_TYPE_FLAG_SHIP,
            'orderBy' => 'num_of_sale',
            'orderDirection' => 'desc',
            'limit' => 10,
            'paginate' => false,
        ];

        if (isMicrosite()) {
            $shop = getMicrositeShop();
            $params['userIds'] = [$shop->user_id];
        }

        return $this->repo->getList($params);
    }

    public function getHomeCategoryProducts($categoryId)
    {
        $params = [
            'company_type' => User::COMPANY_TYPE_FLAG_SHIP,
            'categoryId' => $categoryId,
            'order_rand' => 1,
            'limit' => 12,
            'paginate' => false,
        ];

        return $this->repo->getList($params);
    }

    public function getShopProductsList($category_ids, $brand_ids, $keyword, $shopId, $finalMinPrice, $finalMaxPrice)
    {
        $params = [
            'shopId'         => $shopId ?? null,
            'keyword'        => $keyword ?? null,
            'brandIds'       => $brand_ids ?? null,
            'categoryIds'    => $category_ids ?? null,
            'finalMinPrice'  => $finalMinPrice ?? 0,
            'finalMaxPrice'  => $finalMaxPrice ?? 9999,
            
        ];
        $conditions = ['published' => 1];

        return $this->repo->getListProductFilter($params, $conditions);
    }

    public function getRelatedProducts($product)
    {
        return $this->repo->getRelatedProducts($product);
    }

    public function getTopSellingProductBySeller($product)
    {
        return $this->repo->getTopSellingProductBySeller($product);
    }

    public function getTopSellingProducts($userId)
    {
        return $this->repo->getTopSellingProducts($userId);
    }

    public function getProductCurrentStock($id, string $variation)
    {
        return $this->repo->getProductCurrentStock($id, $variation);
    }

    public function getBySlug(string $slug) {
        return $this->repo->getBySlug($slug);
    }

    public function getProducts(User $user = null, Request $request) {
        return $this->repo->getProducts($user, $this->buildParamsFromRequest($request));
    }

    public function getProductsWithVariant(User $user = null, Request $request) {
        return $this->repo->getProductsWithVariant($user, $this->buildParamsFromRequest($request));
    }
 
    public function isBelongTo($product, $user)
    {
        if($user->id === $product->user_id || $user->user_type === User::TYPE_STAFF) {
            return true;
        }
        return false;
    }

    public function getBaseBuilder() {
        $queryBuilder = [
            "bool" => [
                "filter" => [
                    [
                        "match" => [
                            "published" => true
                        ]
                    ],
                    [
                        "range" => [
                            "current_stock" => [
                                "gt" => 0
                            ]
                        ]
                    ],
                    [
                        "term" => ["brand.status" => true]
                    ],
                    [
                        "term" => ["category.status" => true]
                    ],
                    [
                        "bool" => [
                            "should" => [
                                [
                                    "bool" => [
                                        "must_not" => [
                                            ["exists" => ["field" => "subcategory_id"]] // Handles `null` values
                                        ]
                                    ]
                                ],
                                [
                                    "term" => ["subcategory_id" => 0] // Handles `0`
                                ],
                                [
                                    "term" => ["subcategory.status" => true] // Matches status = true
                                ]
                            ],
                            "minimum_should_match" => 1
                        ]
                    ],
                    [
                        "bool" => [
                            "should" => [
                                [
                                    "bool" => [
                                        "must_not" => [
                                            ["exists" => ["field" => "subsubcategory_id"]] // Handles `null` values
                                        ]
                                    ]
                                ],
                                [
                                    "term" => ["subsubcategory_id" => 0] // Handles `0`
                                ],
                                [
                                    "term" => ["subsubcategory.status" => true] // Matches status = true
                                ]
                            ],
                            "minimum_should_match" => 1
                        ]
                    ],
                    [
                        "bool" => [
                            "must" => [
                                [
                                    "bool" => [
                                        "must_not" => [
                                            ["exists" => ["field" => "user.deleted_at"]] // Equivalent to `IS NULL`
                                        ]
                                    ]
                                ],
                                [
                                    "bool" => [
                                        "should" => [
                                            [
                                                "term" => ["user.company_type" => 1] // Matches `company_type = 1`
                                            ],
                                            [
                                                "bool" => [
                                                    "filter" => [
                                                        ["term" => ["user.company_type" => 2]], // Matches `company_type = 2`
                                                        [
                                                            "range" => [
                                                                "user.company_start_date" => [
                                                                    "lte" => "now"
                                                                ]
                                                            ]
                                                        ],
                                                        [
                                                            "range" => [
                                                                "user.company_end_date" => [
                                                                    "gt" => "now"
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ],
                                        "minimum_should_match" => 1 // At least one condition in `should` must match
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'bool' => [
                            "filter" => [
                                ["term" => ["seller.verification_status" => 1]], // Matches `company_type = 2`
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $queryBuilder;
    }

    public function buildParamsFromRequest(Request $request): array
    {
        $params = [];

        $params['page'] = $request->input('page');
        $params['per_page'] = $request->input('per_page');

        if ($request->has('out_of_stock')) {
            $params['out_of_stock'] = $request->input('out_of_stock');
        }

        if ($request->has('published')) {
            $params['published'] = $request->input('published');
        }

        if ($request->exists('name')) {
            $params['keyword'] = $request->input('name');
        }
        if ($request->exists('sku')) {
            $params['sku'] = $request->input('sku');
        }
        if ($request->exists('ids')) {
            $params['ids'] = $request->input('ids');
        }
        if ($request->exists('excluded')) {
            $params['excluded'] = $request->input('excluded');
        }

        if ($request->exists('category') ) {
            $params['category'] = $request->input('category');
        }

        if ($request->exists('brand') ) {
            $params['brand'] = $request->input('brand');
        }

        if ($request->exists('order_by') ) {
            $params['order_by'] = $request->input('order_by');
            $params['order_by_direction'] = $request->input('order_by_direction', 'desc');
        }

        return $params;
    }

    public function hasVariants($product)
    {
        return $product->choice_options !== '[]';
    }

    public function removeStock($product)
    {
        $product->stocks()->delete();
    }

    public function addStock($product, $data)
    {
        $data['product_id'] = $product->id;
        $this->productStockService->create($data);
    }

    public function formatDataInputs(object $request, $product){

        $refund_request_addon           = \App\Addon::where('unique_identifier', 'refund_request')->first();

        $inputs['name']                 = isset($request->name[0]) ? $request->name[0] : '';
        $inputs['description']          = isset($request->description[0]) ? $request->description[0] : '';
        $inputs['meta_title']           = !isset($request->meta_title[0]) && isset($request->name[0]) ? $request->name[0] : (isset($request->meta_title[0]) ? $request->meta_title[0] : '');
        $inputs['meta_description']     = !isset($request->meta_description[0]) && isset($request->description[0]) ? $request->description[0] : (isset($request->meta_description[0]) ? $request->meta_description[0] : '');

        $inputs['brand_id']             = $request->brand_id;
        $inputs['category_id']          = $request->category_id;
        $inputs['subcategory_id']       = $request->subcategory_id;
        $inputs['subsubcategory_id']    = $request->subsubcategory_id;
        $inputs['current_stock']        = $request->current_stock;
        $inputs['barcode']              = $request->barcode;

        // refundable
        $refundable = null;
        if ($refund_request_addon   != null && $refund_request_addon->activated == 1) {
            if ($request->refundable != null) {
                $refundable    = 1;
            } else {
                $refundable    = 0;
            }
        }

        $inputs['refundable']         = $refundable;

        // photo
        $photos = $request->has('previous_photos') != null ? $request->previous_photos : array();

        if ($request->hasFile('photos')) {
            foreach ($request->photos as $key => $photo) {
                $path       = $photo->store('uploads/products/photos');
                array_push($photos, $path);
            }
        }
        $inputs['photos']   = json_encode($photos);

        // thumbnail_img
        $thumbnail_img = $request->previous_thumbnail_img;
        if ($request->hasFile('thumbnail_img')) {
            $thumbnail_img = $request->thumbnail_img->store('uploads/products/thumbnail');
        }

        $inputs['thumbnail_img']        = $thumbnail_img;

        $inputs['unit']                 = $request->unit;
        $inputs['min_qty']              = $request->min_qty;
        $inputs['tags']                 = implode('|', $request->tags);
        $inputs['video_provider']       = $request->video_provider;
        $inputs['video_link']           = $request->video_link;
        $inputs['unit_price']           = $request->unit_price;
        $inputs['purchase_price']       = $request->purchase_price;
        $inputs['tax']                  = $request->tax;
        $inputs['tax_type']             = $request->tax_type;
        $inputs['discount']             = $request->discount;
        $inputs['discount_type']        = $request->discount_type;

        $meta_img   = $request->hasFile('meta_img') != null ? $request->meta_img->store('uploads/products/meta') : $request->previous_meta_img;
        $inputs['meta_img']             = $meta_img;

        $pdf = $request->hasFile('pdf') != null ? $request->pdf->store('uploads/products/pdf') : null;
        $inputs['pdf']             = $pdf;

        $inputs['slug']      = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name[0] . '-' . $request->unit)));

//        $colors             = array();
//        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
//            $product->colors = json_encode($request->colors);
//        } else {
//            $product->colors    = json_encode($colors);
//        }
//        $inputs['colors']       = $colors;

//        $choice_options = array();
//        if ($request->has('choice_no')) {
//            foreach ($request->choice_no as $key => $no) {
//                $str = 'choice_options_' . $no;
//                $item['attribute_id'] = $no;
//                $item['values'] = explode(',', implode('|', $request[$str]));
//                array_push($choice_options, $item);
//            }
//        }
//
//        $inputs['choice_options'] = json_encode($choice_options);
//
//
//        // delete stock
//        if ($product->attributes != json_encode($request->choice_attributes)) {
//            foreach ($product->stocks as $key => $stock) {
//                $stock->delete();
//            }
//        }
//
//        $inputs['attributes'] = $request->choice_no != null && count($request->choice_no) > 0 ? json_encode($request->choice_no) : json_encode(array());


//        $options = array();
//        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
//            array_push($options, $request->colors);
//        }
//        if ($request->has('choice_no')) {
//            foreach ($request->choice_no as $key => $no) {
//                $name = 'choice_options_' . $no;
//                $my_str = implode('|', $request[$name]);
//                array_push($options, explode(',', $my_str));
//            }
//        }
//        $combinations = combinations($options);
//
//        if (count($combinations[0]) > 0) {
//            $inputs['variant_product'] = 1;
//        } else {
//            $inputs['variant_product'] = 0;
//        }

        return $inputs;
    }

    public function getSellerProducts($request){
        return $this->repo->getSellerProducts($request);
    }

    public function getTotalPublishedProducts() {
        return $this->repo->getTotalPublishedProducts();
    }

    public function getTotalSellerPublishedProducts() {
        return $this->repo->getTotalSellerPublishedProducts();
    }

    public function getTotalAdminPublishedProducts() {
        return $this->repo->getTotalAdminPublishedProducts();
    }

    public function getStockReport($params)
    {
        return $this->repo->getStockReport($params);
    }

    public function applySortBy($sort_by, $productQuery)
    {
        return $this->repo->applySortBy($sort_by, $productQuery);
    }

    public function filterBySubConditions($productQuery, $selected_color, $rate, $discount, $sort_by)
    {
        $params = [
            'rate'           => $rate ?? null,
            'sort_by'        => $sort_by ?? null,
            'discount'       => $discount ?? '',
            'productQuery'   => $productQuery ?? [],
            'selected_color' => $selected_color ?? null,

        ];
        return $this->repo->filterBySubConditions($params);
    }

    public function filterByAttributeValues($productQuery, $selectedAttributeValues)
    {
        return $this->repo->filterByAttributeValues($productQuery, $selectedAttributeValues);
    }

    public function getValidProducts($data = [])
    {
        $params = [
            'shopId'  => $data['shop']->user->id ?? null,
            'keyword' => $data['keyword'] ?? null,
        ];

        if (!empty($data['filters'])) {
            $filters = $data['filters'];
            if (!empty($filters['subSubCateIds'])) {
                $params['subSubCateIds'] = $filters['subSubCateIds'];
            }

            if (!empty($filters['subCateIds'])) {
                $params['subCateIds'] = $filters['subCateIds'];
            }

            if (!empty($filters['cateIds'])) {
                $params['cateIds'] = $filters['cateIds'];
            }

            if (!empty($filters['productIds'])) {
                $params['productIds'] = $filters['productIds'];
            }

            if (!empty($filters['shopIds'])) {
                foreach ($filters['shopIds'] as $shop) {
                    $userIds[] = $shop->user->id;
                }
                $params['shopIds'] = $userIds;
            }
        }

        return $this->repo->getValidProducts($params);
    }

    public function getListProductFilter($query, $userIds, $conditions)
    {
        $params = [
            'keyword'           => $query ?? null,
            'userIds'           => $userIds ?? null,
            'finalMinPrice'     => $conditions['finalMinPrice'],
            'finalMaxPrice'     => $conditions['finalMaxPrice'],
            'brandIds'          => $conditions['brand_id'] ?? null,
            'categoryIds'       => $conditions['category_id'] ?? null,
            'subCategoryIds'    => $conditions['sub_category_id'] ?? null,
            'subSubCategoryIds' => $conditions['sub_sub_category_id'] ?? null,
            'productIds'        => $conditions['product_id'] ?? null,
            'shopId'            => $conditions['shop_id'] ?? null
        ];
        $conditions             = ['published' => 1];

        return $this->repo->getListProductFilter($params, $conditions);
    }

    public function getListProductByCategory($query, $userIds, $conditions, $finalMinPrice, $finalMaxPrice)
    {
        $params = [
            'keyword'           => $query ?? null,
            'userIds'           => $userIds ?? null,
            'finalMinPrice'     => $finalMinPrice,
            'finalMaxPrice'     => $finalMaxPrice,
        ];

        return $this->repo->getListProductFilter($params, $conditions);
    }

    public function getAttributeAndColorListForFilter($productQuery)
    {
        $products = $productQuery->get();

        return $this->repo->getAttributeAndColorListForFilter($products);
    }

    public function dontHaveFrontSortingValue($products) {
        $count = 0;
        foreach ($products as $product) {
            if ($product['frontend_sorting'] == 0) {
                $count++;
            }
        }
        return $count == count($products);
    }

    public function initFrontSortingValue($products) {
        $total = count($products);
        // because products order by id desc => frontend_sorting will add bigger number first
        foreach ($products as $key => $product) {
            $product->frontend_sorting = $total;
            $product->save();
            $total--;
        }
    }

    public function updateMinMaxPrice(string $productId)
    {
        $updatedProduct = $this->repo->find($productId);
        if ($updatedProduct->variant_product) {
            $stocks = $updatedProduct->stocks;
            $data = [
                'min_price' => $stocks->min('price'),
                'min_final_price' => $stocks->min('final_price'),
                'max_price' => $stocks->max('price'),
                'max_final_price' => $stocks->max('final_price'),
            ];
        } else {
            $data = [
                'min_price' => $updatedProduct->unit_price,
                'min_final_price' => $updatedProduct->final_price,
                'max_price' => $updatedProduct->unit_price,
                'max_final_price' => $updatedProduct->final_price,
            ];
        }
        $this->repo->update($updatedProduct->id, $data);
    }

    public function getMaxProductSortingBySeller($user)
    {
        return $this->repo->getMaxProductSortingBySeller($user);
    }

    public function uploadImage($product, $image) {
        $originalPath = getProductImagePath($product->id);
        $thumbnailPath = getProductImageThumbnailPath($product->id);

        if (!File::isDirectory($originalPath)) {
            File::makeDirectory($originalPath, 0775, true);
        }

        if (!File::isDirectory($thumbnailPath)) {
            File::makeDirectory($thumbnailPath, 0775, true);
        }

        $name = $image->getClientOriginalName();

        $filename = time() . rand(11111, 99999) . '_' . $name;
        Image::make($image)->resize(1000, null, function ($constraint) {
            $constraint->aspectRatio();
        })->save($originalPath . $filename);


        Image::make($image)
            ->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->save($thumbnailPath . $filename, 75); // 75% quality
        return $filename;
    }

    public function isEnableProduct($productId): bool
    {
        $product = $this->repo->find($productId);
        if ($product && $product->published) {
            return true;
        }

        return false;
    }
}
