<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Mobile\SplitOrderResource;
use App\Services\Api\Mobile\SplitOrderService;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Split Orders",
 *     description="Mobile API endpoints for split order management"
 * )
 */
class SplitOrderController extends Controller
{
    protected $service;
    protected $orderService;

    public function __construct(SplitOrderService $service, OrderService $orderService)
    {
        $this->service = $service;
        $this->orderService = $orderService;
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/split-orders",
     *     operationId="getUserSplitOrders",
     *     tags={"Split Orders"},
     *     summary="Get user's split orders",
     *     description="Retrieve a paginated list of split orders for the authenticated user. Each split order contains product details, seller information, payment status, and pricing breakdown. Supports filtering by delivery status and product name.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", example=15, default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer", example=1, default=1)
     *     ),
     *     @OA\Parameter(
     *         name="delivery_status",
     *         in="query",
     *         required=false,
     *         description="Filter by delivery status (order_placed: pending + on_review, pending: Order Placed, on_review: Ready to Ship, on_delivery: In Transit, delivered: Delivered, cancelled: Cancelled)",
     *         @OA\Schema(
     *             type="string",
     *             enum={"order_placed", "pending", "on_review", "on_delivery", "delivered", "cancelled"},
     *             example="pending"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         required=false,
     *         description="Search by keyword in product name, seller name, or order code (starts with match)",
     *         @OA\Schema(type="string", example="")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Split orders retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Split orders retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/SplitOrder")
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="total", type="integer", example=50),
     *                     @OA\Property(property="count", type="integer", example=15),
     *                     @OA\Property(property="per_page", type="integer", example=15),
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="total_pages", type="integer", example=4)
     *                 )
     *             )
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
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $perPage = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            
            $params = [
                'delivery_status' => $request->input('delivery_status'),
                'keyword' => $request->input('keyword'),
            ];
            
            $splitOrders = $this->service->getUserSplitOrders($user->id, $perPage, $page, $params);
            
            return $this->successResponse('Split orders retrieved successfully', [
                'items' => SplitOrderResource::collection($splitOrders->items()),
                'pagination' => [
                    'total' => $splitOrders->total(),
                    'count' => $splitOrders->count(),
                    'per_page' => $splitOrders->perPage(),
                    'current_page' => $splitOrders->currentPage(),
                    'total_pages' => $splitOrders->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Internal server error', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/track-your-orders",
     *     operationId="trackYourOrders",
     *     tags={"Split Orders"},
     *     summary="Track orders by order code",
     *     description="Track orders using the order code. Finds the order by code, validates ownership if user is authenticated, and returns all split orders with product details, seller information, payment status, and delivery status. This endpoint does not require authentication, but if a user is authenticated, it will verify that the order belongs to that user.",
     *     @OA\Parameter(
     *         name="order_code",
     *         in="query",
     *         required=true,
     *         description="Order code to track",
     *         @OA\Schema(type="string", example="20250923-18555730")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Split orders retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Split orders retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     description="List of split orders for the given order code",
     *                     @OA\Items(ref="#/components/schemas/SplitOrder")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Order code is required",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Order code is required")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Order does not belong to authenticated user",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Forbidden")
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

    public function trackOrder(Request $request)
    {
        try {
            $orderCode = $request->input('order_code');
            
            if (!$orderCode) {
                return $this->errorResponse('Order code is required', 400);
            }

            $order = $this->orderService->getByCode($orderCode);

            if (!$order) {
                return $this->errorResponse('Order not found', 404);
            }

            $user = $request->user();
            
            if ($user && $order->user_id !== $user->id) {
                return $this->errorResponse('Forbidden', 403);
            }

            $splitOrders = $order->splitOrders()
                ->with([
                    'seller.shop',
                    'orderDetails.product',
                    'order'
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            $data = [
                'items' => SplitOrderResource::collection($splitOrders),
            ];

            return $this->successResponse('Split orders retrieved successfully', $data);
        } catch (\Throwable $th) {
            return $this->errorResponse('Internal server error', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/mobile/v1/split-orders/details",
     *     operationId="getUserSplitOrdersDetails",
     *     tags={"Split Orders"},
     *     summary="Get user's split orders",
     *     description="Retrieve details of a specific split order for the authenticated user.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=false,
     *         description="Split order ID",
     *         @OA\Schema(type="integer", example="")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Split orders retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Split orders retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/SplitOrder")
     *                 )
     *             )
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
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function purchaseHistoryDetails(Request $request)
    {
        try {
            $user = $request->user();
            $id = $request->input('id');
            $splitOrder = $this->service->get($id);

            if (!$splitOrder) {
                return $this->errorResponse('Split order not found', 404);
            }
            
            $splitOrder->load('order');
            
            if ($splitOrder->order->user_id !== $user->id) {
                return $this->errorResponse('Forbidden', 403);
            }
            
            $data = [
                'delivery_viewed' => 1,
                'payment_status_viewed' => 1    
            ];

            $status = $this->orderService->update($splitOrder->order_id, $data);

            return $this->successResponse('Split orders retrieved successfully', new SplitOrderResource($splitOrder));
        } catch (Exception $e) {
            return $this->errorResponse('Internal server error', 500);
        }
    }
}

