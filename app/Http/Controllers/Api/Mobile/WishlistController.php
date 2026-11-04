<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\Mobile\Products\WishlistRequest;
use App\Http\Resources\Mobile\ProductResource;
use App\Services\Api\Mobile\WishlistService;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    protected $service;

    public function __construct(WishlistService $service)
    {
        $this->service = $service;
    }
    /**
     * @OA\Post(
     *     path="/api/mobile/v1/auth/product/wishlist",
     *     tags={"Product"},
     *     summary="Toggle product in wishlist",
     *     description="Add or remove a product from the user's wishlist depending on current state (requires login).",
     *     operationId="toggleProductWishlist",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id"},
     *             @OA\Property(property="product_id", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Wishlist updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product has been added to your wishlist"),
     *             @OA\Property(
     *                 property="data",
     *                 oneOf={
     *                     @OA\Schema(ref="#/components/schemas/ProductDetail"),
     *                     @OA\Schema(type="object", example={})
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Something went wrong. Please try again."),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     )
     * )
     */

    public function store(WishlistRequest $request)
    {
        try {
            $data = [
                'user_id' => Auth::id(),
                'product_id' => $request->get('product_id', '')
            ];

            $wishlist = $this->service->getFirstWithConditions($data);

            if ($wishlist) {
                $this->service->destroy($wishlist->id);
                return $this->successResponse('Product has been removed from your wishlist');
            }

            $wishlist = $this->service->create($data);
            return $this->successResponse('Product has been added to your wishlist', new ProductResource($wishlist->product));

        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong. Please try again.', [], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/auth/product/wishlists",
     *     summary="Get Wishlist of Authenticated User",
     *     description="Retrieve all wishlist products of the authenticated user.",
     *     operationId="getWishlist",
     *     tags={"Product"},
     *     security={{"bearerAuth": {}}},
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Wishlist retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Wishlist retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ProductDetail")
     *             )
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function getWishlistItems()
    {
        $user = Auth::user();
        $wishlists = $this->service->getListByUser($user->id);
        $products = $wishlists->pluck('product')->filter();

        return $this->successResponse(
            'Wishlist retrieved successfully', 
            ProductResource::collection($products)
        );
    }
} 