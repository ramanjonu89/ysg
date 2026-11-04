<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\Mobile\Products\SearchRequest;
use App\Http\Resources\Mobile\ProductDetailResource;
use App\Http\Resources\Mobile\ProductResource;
use App\Http\Resources\Mobile\ProductReviewResource;
use App\Models\Product;
use App\Services\Api\Mobile\ProductService;
use App\Services\Api\Mobile\ProductSearchService;
use App\Models\Review;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Product",
 *     description="Mobile API endpoints for product management"
 * )
 */
class ProductController extends Controller
{
    protected $service;
    protected $searchService;

    /**
     * @param ProductService $service
     * @param ProductSearchService $searchService
     */
    public function __construct(ProductService $service, ProductSearchService $searchService)
    {
        $this->service = $service;
        $this->searchService = $searchService;
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/products/search",
     *     tags={"Product"},
     *     summary="Search products",
     *     description="Search for products with filters and pagination",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SearchRequest")
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
     *                         property="sellers",
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
    public function search(SearchRequest $request): \Illuminate\Http\JsonResponse
    {

        $params = $request->all();
        $params['rating'] = $request->get('rate', '');

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

        $data = $this->searchService->process($request);

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
     *     path="/api/mobile/v1/products/{id}",
     *     tags={"Product"},
     *     summary="Get product detail",
     *     description="Retrieve a product's detail by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product detail retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/ProductDetail")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product not found"),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     )
     * )
     */
    public function get($id): \Illuminate\Http\JsonResponse
    {
        $product = $this->service->get($id);
        if (!$product) {
            return $this->errorResponse('Product not found', [], 404);
        }
        
        return $this->successResponse(
            'Product detail retrieved successfully',
            new ProductDetailResource($product)
        );
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/products/{id}/reviews",
     *     tags={"Product"},
     *     summary="Get product reviews",
     *     description="Retrieve a paginated list of product reviews with optional rating filter and sorting",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", format="int64", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", format="int32", example=1, default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", format="int32", example=15, default=15)
     *     ),
     *     @OA\Parameter(
     *         name="rating",
     *         in="query",
     *         required=false,
     *         description="Filter by rating (1-5 stars)",
     *         @OA\Schema(type="integer", format="int32", enum={1, 2, 3, 4, 5}, example=5)
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Sort reviews by rating. Use 'asc' for ascending, 'desc' for descending",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="desc", default="desc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product reviews retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/ProductReview")
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="total", type="integer", example=150),
     *                     @OA\Property(property="per_page", type="integer", example=15),
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="last_page", type="integer", example=10),
     *                     @OA\Property(property="next_page_url", type="string", nullable=true, example="http://example.com/api/mobile/v1/products/1/reviews?page=2"),
     *                     @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
     *                     @OA\Property(property="from", type="integer", nullable=true, example=1),
     *                     @OA\Property(property="to", type="integer", nullable=true, example=15)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product not found"),
     *             @OA\Property(property="errors", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="rating",
     *                     type="array",
     *                     @OA\Items(type="string", example="The rating must be between 1 and 5.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getReviews($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $product = $this->service->get($id);
        if (!$product) {
            return $this->errorResponse('Product not found', [], 404);
        }

        $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'sort_by' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);

        $query = Review::where('product_id', $id)->with('user');

        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        $sortBy = $request->get('sort_by', 'desc');
        $query->orderBy('rating', $sortBy);

        $perPage = $request->get('per_page', 15);
        $reviews = $query->paginate($perPage);

        return $this->successResponse(
            'Product reviews retrieved successfully',
            [
                'items' => ProductReviewResource::collection($reviews->items()),
                'pagination' => [
                    'total' => $reviews->total(),
                    'per_page' => $reviews->perPage(),
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'next_page_url' => $reviews->nextPageUrl(),
                    'prev_page_url' => $reviews->previousPageUrl(),
                    'from' => $reviews->firstItem(),
                    'to' => $reviews->lastItem()
                ]
            ]
        );
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/products/{product}/related-products",
     *     summary="Get related products",
     *     description="Retrieve up to 10 products related to the specified product ID.",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="The ID of the product to find related products for.",
     *         @OA\Schema(type="integer", example=123)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Related products retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Related products retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or missing product ID",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid or missing product ID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to retrieve related products",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve related products")
     *         )
     *     )
     * )
     */
    public function getRelatedProducts(Product $product)
    {
        try {
            if (!$product) {
                return $this->errorResponse('Product not found', [], 404);
            }

            $products = $this->service->getRelatedProducts($product);
            return $this->successResponse(
                'Related products retrieved successfully',
                ProductResource::collection($products)
            );
        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to retrieve related products', [], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/products/{product}/top-selling",
     *     summary="Get top-selling products by the same seller",
     *     description="Retrieve up to 10 top-selling products from the same seller as the specified product.",
     *     tags={"Product"},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="The ID of the product whose seller's top-selling products will be retrieved.",
     *         @OA\Schema(type="integer", example=123)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Top-selling products retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Top selling products retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or missing product ID",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid or missing product ID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to retrieve top-selling products",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve top-selling products")
     *         )
     *     )
     * )
     */
    public function getTopSellingProducts(Product $product)
    {
        try {
            if (!$product) {
                return $this->errorResponse('Product not found', [], 404);
            }

            $products = $this->service->getTopSellingProductBySeller($product);
            return $this->successResponse(
                'Top selling products retrieved successfully',
                ProductResource::collection($products)
            );
        } catch (\Throwable $th) {
            return $this->errorResponse('Failed to retrieve top selling products', [], 500);
        }
    }
}
