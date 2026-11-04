<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Mobile\ReviewResource;
use App\Services\Api\Mobile\ReviewService;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Reviews",
 *     description="Mobile API endpoints for product review management"
 * )
 */
class ReviewController extends Controller
{
    protected $service;

    /**
     * @param ReviewService $service
     */
    public function __construct(ReviewService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/reviews",
     *     tags={"Reviews"},
     *     summary="Get product reviews list",
     *     description="Retrieve a paginated list of product reviews with optional filtering and sorting. At least one of product_id or user_id must be provided.",
     *     operationId="getReviewsList",
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="Filter reviews by product ID (required if user_id is not provided)",
     *         required=false,
     *         @OA\Schema(type="integer", example=42)
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Filter reviews by user ID (required if product_id is not provided)",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort order for reviews",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"newest", "oldest", "high_to_low", "low_to_high"},
     *             default="newest",
     *             example="newest"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="show_option",
     *         in="query",
     *         description="Filter option for displaying reviews",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"all", "only_1", "only_2", "only_3", "only_4", "only_5"},
     *             default="all",
     *             example="all"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10, example=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1, example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reviews retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product review list retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Review")
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="page", type="integer", example=1),
     *                     @OA\Property(property="page_size", type="integer", example=10),
     *                     @OA\Property(property="total", type="integer", example=150),
     *                     @OA\Property(property="last_page", type="integer", example=15),
     *                     @OA\Property(property="has_next", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product ID or User ID is required"),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function list(Request $request)
    {
        $productId = (int)$request->input('product_id', null);
        $userId = (int)$request->input('user_id', null);
        $sortBy = $request->input('sort_by', 'newest');
        $perPage = (int)$request->input('per_page', 10);
        $page = (int)$request->input('page', 1);
        $showOption = $request->input('show_option', 'all');

        if (empty($productId) && empty($userId)) {
            return $this->errorResponse('Product ID or User ID is required', [], 400);
        }

        $list = $this->service->getList($productId, $userId, $sortBy, $showOption, $perPage, $page);

        return $this->successResponse(
            'Product review list retrieved successfully',
            $this->buildResponseFromPagination($list)
        );
    }

    public function buildResponseFromPagination($list): array
    {
        return [
            'items' => ReviewResource::collection($list->items()),
            'pagination' => [
                'page'       => $list->currentPage(),
                'page_size'  => $list->perPage(),
                'total'      => $list->total(),
                'last_page'  => $list->lastPage(),
                'has_next'   => $list->hasMorePages(),
            ],
        ];
    }

    /**
     * @OA\Post(
     *     path="/api/mobile/v1/products/{id}/reviews",
     *     tags={"Reviews"},
     *     summary="Create a product review",
     *     description="Create a new review for a product. User must be authenticated to create a review.",
     *     operationId="createProductReview",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", format="int64", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rating"},
     *             @OA\Property(
     *                 property="rating",
     *                 type="integer",
     *                 format="int32",
     *                 minimum=1,
     *                 maximum=5,
     *                 description="Rating value from 1 to 5",
     *                 example=5
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 nullable=true,
     *                 description="Review message or comment",
     *                 example="Great product! Very satisfied with the quality."
     *             ),
     *             @OA\Property(
     *                 property="is_recomended",
     *                 type="boolean",
     *                 nullable=true,
     *                 description="Whether the reviewer recommends this product",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="review_categories",
     *                 type="array",
     *                 nullable=true,
     *                 description="Array of review category IDs",
     *                 @OA\Items(
     *                     type="integer",
     *                     format="int64",
     *                     example=1
     *                 ),
     *                 example={1, 2, 3}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Review created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Review created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Review"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="rating",
     *                     type="array",
     *                     @OA\Items(type="string", example="The rating field is required.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User must be authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product not found"),
     *             @OA\Property(property="errors", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="rating",
     *                     type="array",
     *                     @OA\Items(type="string", example="The rating must be between 1 and 5.")
     *                 ),
     *                 @OA\Property(
     *                     property="review_categories",
     *                     type="array",
     *                     @OA\Items(type="string", example="The review categories must be an array.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store($id, Request $request)
    {
        $product = Product::with('category.reviewCategories')->find($id);
        
        if (!$product) {
            return $this->errorResponse('Product not found', [], 404);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'nullable|string',
            'is_recomended' => 'nullable|boolean',
            'review_categories' => 'nullable|array',
            'review_categories.*' => 'integer|exists:review_categories,id',
        ]);

        if (!empty($validated['review_categories'])) {
            if (!$product->category) {
                return $this->errorResponse(
                    'Product does not have a category assigned',
                    ['review_categories' => ['Cannot assign review categories to a product without category.']],
                    422
                );
            }

            $allowedReviewCategoryIds = $product->category->reviewCategories->pluck('id')->toArray();
            $invalidCategories = array_diff($validated['review_categories'], $allowedReviewCategoryIds);
            
            if (!empty($invalidCategories)) {
                return $this->errorResponse(
                    'Some review categories are not assigned to this product\'s category',
                    ['review_categories' => ['The selected review categories are not valid for this product category.']],
                    422
                );
            }
        }

        $user = $request->user();

        $review = Review::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => $validated['rating'],
            'comment' => $validated['message'] ?? null,
            'is_recommended' => $validated['is_recomended'] ?? false,
            'status' => 1,
            'viewed' => 0,
        ]);

        if (!empty($validated['review_categories'])) {
            $review->reviewCategories()->sync($validated['review_categories']);
        }

        $review->load('user', 'reviewCategories');

        return $this->successResponse(
            'Review created successfully',
            new ReviewResource($review),
            201
        );
    }
}
