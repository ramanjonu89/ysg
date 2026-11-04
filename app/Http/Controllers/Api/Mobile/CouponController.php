<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Mobile\CouponResource;
use App\Models\Coupon;
use App\ObjectModels\Coupon as CouponModel;
use App\Services\CouponService;
use App\Services\CartService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Coupon",
 *     description="Mobile API endpoints for coupon management"
 * )
 */
class CouponController extends Controller
{
    protected $couponService;
    protected $cartService;

    public function __construct(CouponService $couponService, CartService $cartService)
    {
        $this->couponService = $couponService;
        $this->cartService = $cartService;
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/coupon/{code}",
     *     operationId="mobileCouponGetByCode",
     *     tags={"Coupon"},
     *     summary="Get coupon by code",
     *     description="Returns coupon information based on coupon code. No authentication required.",
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         required=true,
     *         description="Coupon code",
     *         @OA\Schema(type="string", example="SUMMER20")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Coupon retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Coupon retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Coupon"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Invalid coupon code or coupon is not valid",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The coupon code is invalid!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Coupon not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Coupon not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Can not get coupon information")
     *         )
     *     )
     * )
     */
    public function getByCode(Request $request, $code)
    {
        try {
            $coupon = $this->couponService->getFirstWithConditions([
                'code' => $code,
                'is_active' => Coupon::STATUS_ACTIVE
            ]);

            if ($coupon === null) {
                return $this->errorResponse('The coupon code is invalid!', [], 404);
            }

            if ($coupon->isExpired()) {
                return $this->errorResponse('The coupon code has expired.', [], 400);
            }

            if ($coupon->inValid()) {
                return $this->errorResponse('The coupon code is not valid yet.', [], 400);
            }

            $coupon->is_can_apply = false;
            if (auth()->check()) {
                $cart = $this->cartService->getTempCart();
                $couponModel = new CouponModel($coupon);
                $validation = $cart->simpleValidateCoupon($couponModel);
                if ($validation['valid']) {
                    $coupon->is_can_apply = true;
                }
            }

            return $this->successResponse('Coupon retrieved successfully', new CouponResource($coupon));
        } catch (\Exception $e) {
            return $this->errorResponse('Can not get coupon information === ' . $e->getMessage(), [], 500);
        }
    }
}

