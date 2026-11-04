<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Mobile\OrderDetailFullResource;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Orders",
 *     description="Mobile API endpoints for order management"
 * )
 */
class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/mobile/v1/orders/{orderId}",
     *     operationId="getOrderDetail",
     *     tags={"Orders"},
     *     summary="Get order detail",
     *     description="Retrieve detailed information about a specific order including all products, pricing breakdown, shipping information, and order status. The order must belong to the authenticated user.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="orderId",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer", example=12345)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order detail retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order detail retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/OrderDetailFull")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Order does not belong to user",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You do not have permission to access this order")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Order not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function show($orderId)
    {
        try {
            $order = Order::with(['orderDetails.product', 'orderDetails.pickup_point'])->find($orderId);

            if (!$order) {
                return $this->errorResponse('Order not found', [], 404);
            }

            $user = Auth::user();
            if (!$user || $order->user_id !== $user->id) {
                return $this->errorResponse('You do not have permission to access this order', [], 403);
            }

            return $this->successResponse('Order detail retrieved successfully', new OrderDetailFullResource($order));

        } catch (\Exception $e) {
            return $this->errorResponse('Internal server error: ' . $e->getMessage(), [], 500);
        }
    }
}

