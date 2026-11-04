<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\Mobile\Shops\SearchRequest;
use App\Http\Resources\Api\Mobile\BrandCollection;
use App\Http\Resources\Api\Mobile\CategoryListCollection;
use App\Http\Resources\Mobile\BlockResource;
use App\Http\Resources\Mobile\ProductResource;
use App\Services\SellerService;
use App\Http\Resources\Mobile\SellerResource;
use App\Models\Seller;
use App\Services\Api\Mobile\BrandService;
use App\Services\Api\Mobile\CategoryService;
use App\Services\Api\Mobile\ProductSearchService;
use App\Services\ShopService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Seller Store",
 *     description="Mobile API endpoints for sellers"
 * )
 */
class SellerShopController extends Controller
{
    protected $brandService;
    protected $categoryService;
    protected $sellerService;
    protected $shopService;
    protected $searchService;

    public function __construct(
        BrandService         $brandService,
        CategoryService      $categoryService,
        SellerService        $sellerService,
        ShopService          $shopService,
        ProductSearchService $searchService
    ) {
        $this->brandService  = $brandService;
        $this->categoryService = $categoryService;
        $this->sellerService = $sellerService;
        $this->shopService   = $shopService;
        $this->searchService = $searchService;
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/seller/{seller}/profile",
     *     operationId="getSellerProfile",
     *     tags={"Seller Store"},
     *     summary="Get seller store profile (public or private)",
     *     description="Retrieve detailed information about a seller store by ID.
     *         - If the store type is **Private/Corporate Sales Store**, user authentication (Bearer token) is required.
     *         - If the store type is **Flagship Store**, the information can be accessed publicly without login.",
     *     @OA\Parameter(
     *         name="seller",
     *         in="path",
     *         required=true,
     *         description="ID of the seller store",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="X-Access-Code-Token",
     *         in="header",
     *         required=false,
     *         description="Access code token for Private/Corporate Sales Store",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Parameter(
     *         name="access_code",
     *         in="query",
     *         required=false,
     *         description="Access code for Private/Corporate Sales Store",
     *         @OA\Schema(type="string", example="264570")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Seller retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Seller retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Seller")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Login required for Private/Corporate Sales Store",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Please login using a valid account to access the Private/Corporate Sales Store!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Seller not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Seller not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve seller")
     *         )
     *     )
     * )
     */

    public function getStoreProfile(Seller $seller)
    {
        try {
            if (!$seller) {
                return $this->errorResponse('Shop not found', 404);
            }

            return $this->successResponse(
                'Seller retrieved successfully',
                new SellerResource($seller)
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve seller', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/seller/{seller}/blocks",
     *     operationId="getShopBlocks",
     *     tags={"Seller Store"},
     *     summary="Get all active homepage blocks for a seller",
     *     description="Retrieve all active and non-expired homepage blocks (sections) for a seller's shop, including their enabled contents. 
     *         - If the store type is **Private/Corporate Sales Store**, user authentication (Bearer token) is required.
     *         - If the store type is **Flagship Store**, the information can be accessed publicly without login.",
     *     @OA\Parameter(
     *         name="seller",
     *         in="path",
     *         required=true,
     *         description="Unique ID of the seller",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="X-Access-Code-Token",
     *         in="header",
     *         required=false,
     *         description="Access code token for Private/Corporate Sales Store",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Parameter(
     *         name="access_code",
     *         in="query",
     *         required=false,
     *         description="Access code for Private/Corporate Sales Store",
     *         @OA\Schema(type="string", example="264570")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shop blocks retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Shop blocks retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Block")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Login required for Private/Corporate Sales Store",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Please login using a valid account to access the Private/Corporate Sales Store!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Seller or shop not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Seller or shop not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve shop blocks")
     *         )
     *     )
     * )
     */


    public function getShopBlocks(Seller $seller)
    {
        try {
            $user = $seller->user;

            if (!$user || !$user->shop) {
                return $this->errorResponse('Seller or shop not found', [], 404);
            }

            $shop = $user->shop;
            $blocks = $this->shopService->getActivatedBlocks($shop);

            $blocks = $blocks->filter(function ($block) {
                $blockContents = $block->blockContents->filter(function ($blockContent) {
                    return $blockContent->is_enabled && !$blockContent->isExpired();
                });

                return !$block->isExpired() && (!$block->needsContent() || $blockContents->isNotEmpty());
            });

            return $this->successResponse(
                'Shop blocks retrieved successfully',
                BlockResource::collection($blocks)
            );
        } catch (Exception $e) {
            Log::error('Failed to retrieve shop blocks', [
                'seller_id' => $seller->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Failed to retrieve shop blocks', [], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/seller/{seller}/featured-products",
     *     operationId="getFeaturedProducts",
     *     tags={"Seller Store"},
     *     summary="Get featured products of a seller",
     *     description="Retrieve the list of featured (highlighted or promoted) products from a seller's shop. 
     *         - If the store type is **Private/Corporate Sales Store**, user authentication (Bearer token) is required.
     *         - If the store type is **Flagship Store**, the information can be accessed publicly without login.",
     *     @OA\Parameter(
     *         name="seller",
     *         in="path",
     *         required=true,
     *         description="Unique ID of the seller",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="X-Access-Code-Token",
     *         in="header",
     *         required=false,
     *         description="Access code token for Private/Corporate Sales Store",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Parameter(
     *         name="access_code",
     *         in="query",
     *         required=false,
     *         description="Access code for Private/Corporate Sales Store",
     *         @OA\Schema(type="string", example="264570")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Featured products retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Featured products retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Seller or shop not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Seller or shop not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve featured products")
     *         )
     *     )
     * )
     */


    public function getFeaturedProducts(Seller $seller)
    {
        try {
            $user = $seller->user;

            if (!$user || !$user->shop) {
                return $this->errorResponse('Seller or shop not found', [], 404);
            }

            $shop = $user->shop;
            $data = $this->shopService->getFeaturedProducts($shop);

            return $this->successResponse(
                'Feature products retrieved successfully',
                ProductResource::collection($data)
            );
        } catch (Exception $e) {
            Log::error('Failed to retrieve featured products', [
                'seller_id' => $seller->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Failed to retrieve featured products', [], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/seller/{seller}/products/search",
     *     tags={"Seller Store"},
     *     summary="Search products by filters and pagination",
     *     description="Search for products in a seller's shop using filters (e.g., category, brand, attributes) with pagination support.
     *         - If the store type is **Private/Corporate Sales Store**, user authentication (Bearer token) is required.
     *         - If the store type is **Flagship Store**, the information can be accessed publicly without login.",
     *     @OA\Parameter(
     *         name="seller",
     *         in="path",
     *         required=true,
     *         description="Unique ID of the seller",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/searchProductForShopRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful search",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product list retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Product")
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="page", type="integer", example=1),
     *                     @OA\Property(property="page_size", type="integer", example=15),
     *                     @OA\Property(property="total", type="integer", example=120),
     *                     @OA\Property(property="last_page", type="integer", example=8),
     *                     @OA\Property(property="has_next", type="boolean", example=true)
     *                 ),
     *                 @OA\Property(
     *                     property="filter",
     *                     type="object",
     *                     @OA\Property(
     *                         property="categories",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="name", type="string"),
     *                             @OA\Property(
     *                                 property="translation",
     *                                 type="array",
     *                                 @OA\Items(type="string")
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="subCategories",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="name", type="string"),
     *                             @OA\Property(
     *                                 property="translation",
     *                                 type="array",
     *                                 @OA\Items(type="string")
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="subSubCategories",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="name", type="string"),
     *                             @OA\Property(
     *                                 property="translation",
     *                                 type="array",
     *                                 @OA\Items(type="string")
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="brands",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="name", type="string")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="attributes",
     *                         type="object",
     *                         additionalProperties={
     *                             "type": "object",
     *                             "properties": {
     *                                 "id": { "type": "integer" },
     *                                 "values": {
     *                                     "type": "array",
     *                                     "items": { "type": "string" }
     *                                 }
     *                             }
     *                         }
     *                     ),
     *                     @OA\Property(
     *                         property="existing_attributes",
     *                         type="object",
     *                         additionalProperties={ "type": "string" }
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function search(SearchRequest $request, Seller $seller): \Illuminate\Http\JsonResponse
    {
        $params = $request->all();

        $shopId = $seller->user->id;

        $attributes = $request->get('attributes', []);

        if ($attributes) {
            $temp = [];
            foreach ($attributes as $attribute) {
                $key = 'attribute_' . $attribute['id'];
                $value = $attribute['value'];
                if (isset($temp[$key])) {
                    $temp[$key] = array_merge($temp[$key], [$value]);
                } else {
                    $temp[$key] = [$value];
                }
            }
            $params = array_merge($params, $temp);
        }

        if (isset($params['sort_by'])) {
            $sortByOptions = $this->getSortByOptions();
            $sortBy = $params['sort_by'];
            if (isset($sortByOptions[$sortBy])) {
                $params['sort_by'] = $sortByOptions[$sortBy];
            } else {
                unset($params['sort_by']);
            }
        }

        $request = new Request($params);

        $data = $this->searchService->process($request, $shopId);

        return $this->successResponse(
            'Product list retrieved successfully',
            $this->buildResponseFromPagination($data)
        );
    }

    private function getSortByOptions(): array
    {
        return [
            'top_sales' => 1,
            'latest' => 2,
            'low_to_high' => 3,
            'high_to_low' => 4,
            'oldest' => 5,
        ];
    }

    public function buildResponseFromPagination($data): array
    {
        $productList = $data['products'];
        return [
            'items' => ProductResource::collection($productList->items()),
            'pagination' => [
                'page'       => $productList->currentPage(),
                'page_size'  => $productList->perPage(),
                'total'      => $productList->total(),
                'last_page'  => $productList->lastPage(),
                'has_next'   => $productList->hasMorePages(),
            ],
            'filter' => [
                'categories'       => $data['categories'],
                'subCategories'       => $data['subCategories'],
                'subSubCategories'       => $data['subSubCategories'],
                'brands'       => $data['brands'],
                'sellers'       => $data['sellers'],
                'attributes'       => $data['attributes'],
                'existing_attributes'       => $data['existing_attributes'],
            ]
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/seller/{seller}/brands",
     *     operationId="getBrands",
     *     tags={"Seller Store"},
     *     summary="Get list of brands",
     *     description="Get list brand of a seller's store
     *          - If the store type is **Private/Corporate Sales Store**, user authentication (Bearer token) is required.
     *          - If the store type is **Flagship Store**, the information can be accessed publicly without login.",
     *     @OA\Parameter(
     *         name="seller",
     *         in="path",
     *         required=true,
     *         description="Unique ID of the seller",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="X-Access-Code-Token",
     *         in="header",
     *         required=false,
     *         description="Access code token for Private/Corporate Sales Store",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Parameter(
     *         name="access_code",
     *         in="query",
     *         required=false,
     *         description="Access code for Private/Corporate Sales Store",
     *         @OA\Schema(type="string", example="264570")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Brand list retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     @OA\Items(
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="name", type="string", example="Nike"),
     *                          @OA\Property(property="logo", type="string", example="https://example.com/uploads/brands/apple-logo.png"),
     *                          @OA\Property(property="top", type="integer", example=1),
     *                          @OA\Property(property="slug", type="string", example="abc"),
     *                          @OA\Property(
     *                              property="meta_title",
     *                              oneOf={
     *                                  @OA\Schema(type="string", example="Brand meta title"),
     *                                  @OA\Schema(type="null", example=null)
     *                              }
     *                          ),
     *                          @OA\Property(
     *                               property="meta_description",
     *                               oneOf={
     *                                   @OA\Schema(type="string", example="Brand meta description"),
     *                                   @OA\Schema(type="null", example=null)
     *                               }
     *                           ),
     *                     )
     *                 ),
     *             )
     *         )
     *     ),
     * )
     */
    public function getBrandsByShop(Seller $seller)
    {
        try {
            $shop = $seller->user->shop;
            $data = $this->brandService->getBrandsByShop($shop);

            return $this->successResponse('Brands retrieved successfully', BrandCollection::collection($data));
        } catch (\Exception $e) {
            Log::error('Failed to retrieve brands', ['message' => $e->getMessage()]);
            return $this->errorResponse('Failed to retrieve brands', [], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/seller/{seller}/categories",
     *     operationId="getCategories",
     *     tags={"Seller Store"},
     *     summary="Get list of categories",
     *     description="Get list category of a seller's store
     *          - If the store type is **Private/Corporate Sales Store**, user authentication (Bearer token) is required.
     *          - If the store type is **Flagship Store**, the information can be accessed publicly without login.",
     *     @OA\Parameter(
     *         name="seller",
     *         in="path",
     *         required=true,
     *         description="Unique ID of the seller",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="show_level",
     *         in="query",
     *         required=false,
     *         description="Levels of subcategories to show (1-3)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="X-Access-Code-Token",
     *         in="header",
     *         required=false,
     *         description="Access code token for Private/Corporate Sales Store",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Parameter(
     *         name="access_code",
     *         in="query",
     *         required=false,
     *         description="Access code for Private/Corporate Sales Store",
     *         @OA\Schema(type="string", example="264570")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category list retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Shoes"),
     *                         @OA\Property(property="is_age_restricted", type="boolean", example=false),
     *                         @OA\Property(property="slug", type="string", example="shoes"),
     *                         @OA\Property(property="banner", type="string", example="https://example.com/uploads/categories/banner.png"),
     *                         @OA\Property(property="icon", type="string", example="https://example.com/uploads/categories/icon.png"),
     *                         @OA\Property(
     *                             property="meta_title",
     *                             oneOf={
     *                                 @OA\Schema(type="string", example="Category meta title"),
     *                                 @OA\Schema(type="null", example=null)
     *                             }
     *                         ),
     *                         @OA\Property(
     *                             property="meta_description",
     *                             oneOf={
     *                                 @OA\Schema(type="string", example="Category meta description"),
     *                                 @OA\Schema(type="null", example=null)
     *                             }
     *                         ),
     *                         @OA\Property(property="image", type="string", example="https://example.com/uploads/categories/image.png"),
     *                         @OA\Property(property="top", type="integer", example=1),
     *                         @OA\Property(property="level", type="integer", example=1),
     *                         @OA\Property(property="parent_id", type="integer", example=0),
     *                         @OA\Property(
     *                             property="children",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=2),
     *                                 @OA\Property(property="name", type="string", example="Sneakers"),
     *                                 @OA\Property(property="slug", type="string", example="sneakers"),
     *                                 @OA\Property(
     *                                     property="meta_title",
     *                                     oneOf={
     *                                         @OA\Schema(type="string", example="Subcategory meta title"),
     *                                         @OA\Schema(type="null", example=null)
     *                                     }
     *                                 ),
     *                                 @OA\Property(
     *                                     property="meta_description",
     *                                     oneOf={
     *                                         @OA\Schema(type="string", example="Subcategory meta description"),
     *                                         @OA\Schema(type="null", example=null)
     *                                     }
     *                                 ),
     *                                 @OA\Property(property="level", type="integer", example=2),
     *                                 @OA\Property(property="parent_id", type="integer", example=1),
     *                                 @OA\Property(
     *                                     property="children",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         @OA\Property(property="id", type="integer", example=3),
     *                                         @OA\Property(property="name", type="string", example="Running Shoes"),
     *                                         @OA\Property(property="slug", type="string", example="running-shoes"),
     *                                         @OA\Property(
     *                                             property="meta_title",
     *                                             oneOf={
     *                                                 @OA\Schema(type="string", example="Subsubcategory meta title"),
     *                                                 @OA\Schema(type="null", example=null)
     *                                             }
     *                                         ),
     *                                         @OA\Property(
     *                                             property="meta_description",
     *                                             oneOf={
     *                                                 @OA\Schema(type="string", example="Subsubcategory meta description"),
     *                                                 @OA\Schema(type="null", example=null)
     *                                             }
     *                                         ),
     *                                         @OA\Property(property="level", type="integer", example=3),
     *                                         @OA\Property(property="parent_id", type="integer", example=2)
     *                                     )
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 ),
     *             )
     *         )
     *     ),
     * )
     */

    public function getCategoriesByShop(Seller $seller)
    {
        try {
            $shop = $seller->user->shop;
            $categories = $this->categoryService->getCategoriesByShop($shop);
            return $this->successResponse('Categories retrieved successfully', CategoryListCollection::collection($categories));
        } catch (\Exception $e) {
            Log::error('Failed to retrieve categories', ['message' => $e->getMessage()]);
            return $this->errorResponse('Failed to retrieve categories', [], 500);
        }
    }

}
