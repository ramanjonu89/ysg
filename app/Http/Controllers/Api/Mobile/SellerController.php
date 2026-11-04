<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Services\SellerService;
use App\Http\Resources\Mobile\SellerResource;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Sellers",
 *     description="Mobile API endpoints for sellers"
 * )
 */
class SellerController extends Controller
{
    protected $sellerService;

    public function __construct(SellerService $sellerService)
    {
        $this->sellerService = $sellerService;
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/sellers",
     *     operationId="getMobileSellers",
     *     tags={"Sellers"},
     *     summary="Get list of sellers",
     *     description="Returns a list of verified and active sellers for mobile application, similar to flagship-shop page",
     *     @OA\Response(
     *         response=200,
     *         description="Sellers retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Sellers retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Seller")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve sellers")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $sellers = $this->sellerService->getListSeller();

            return $this->successResponse(
                'Sellers retrieved successfully',
                SellerResource::collection($sellers)
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve sellers', 500);
        }
    }
}
