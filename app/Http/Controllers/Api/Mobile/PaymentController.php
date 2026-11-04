<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Mobile\PaymentMethodResource;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Payment",
 *     description="Mobile API endpoints for payment methods"
 * )
 */
class PaymentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/mobile/v1/payments",
     *     operationId="mobilePaymentMethods",
     *     tags={"Payment"},
     *     summary="Get payment methods list",
     *     description="Returns a list of available payment methods for mobile application",
     *     @OA\Response(
     *         response=200,
     *         description="Payment methods retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Payment methods retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/PaymentMethod")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Can not get payment methods")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $paymentMethods = [
                (object) [
                    'code' => 'cybersource',
                    'name' => 'Cybersource',
                    'logo' => static_asset('frontend/images/icons/cards/visa_master.png')
                ]
            ];

            return $this->successResponse('Payment methods retrieved successfully', PaymentMethodResource::collection($paymentMethods));
        } catch (\Exception $e) {
            return $this->errorResponse('Can not get payment methods', [], 500);
        }
    }
}

