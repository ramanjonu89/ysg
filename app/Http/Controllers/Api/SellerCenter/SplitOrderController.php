<?php
namespace App\Http\Controllers\Api\SellerCenter;

use App\Http\Resources\SellerCenter\OrderCollection;
use App\Http\Resources\SellerCenter\OrderDetailCollection;
use App\Http\Resources\SellerCenter\SplitOrder as SplitOrderResource;
use App\Models\Order;
use App\Models\SplitOrder;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use PDF;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SplitOrderController extends Controller
{
    /**
     * @param OrderService
     */
    protected $service;

    public function __construct(OrderService $orderService) {
        $this->service = $orderService;
    }

    /**
     * Get list order of seller
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        return $this->successResponse($this->buildResponseFromPagination($this->service->getSplitOrders($user, $request)));
    }

    /**
     * @param  Order  $order
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(SplitOrder $order, Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if ($order->seller_id !== $user->id) {
            return $this->errorResponse(__('Order not found'));
        }
        $data = new SplitOrderResource($order);
        return $this->successResponse($data);
    }

    public function items(SplitOrder $order, Request $request)
    {
        $user = $request->user();
        if ($order->seller_id !== $user->id) {
            return $this->errorResponse(__('Order not found'), ResponseAlias::HTTP_FORBIDDEN);
        }
        $data = new OrderDetailCollection($this->service->getOrderItems($order));
        return $this->successResponse($data);
    }

    /**
     * @param  SplitOrder  $order
     * @param  Request  $request
     * @return mixed
     */
    public function invoice(SplitOrder $order, Request $request)
    {
        $parentOrder = $this->service->get($order->order_id);
        $shippingAddress = json_decode($parentOrder->shipping_address);
        $orderItems = $this->service->getOrderItems($order);
        $isSelfCollection = false;
        foreach($orderItems as $item) {
            if ($item->shipping_type == 'self_collection') {
                $isSelfCollection = true;
                break;
            }
        }
        $seller = $order->seller;

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
        ])->loadView('invoices.split_order_invoice', [
            'order' => $parentOrder,
            'shippingAddress' => $shippingAddress,
            'splitOrder' => $order,
            'orderItems' => $orderItems,
            'seller' => $seller,
            'isSelfCollection' => $isSelfCollection,
        ]);

        return $pdf->download('order-' . $parentOrder->code . '.pdf');
    }

    public function update(SplitOrder $order, Request $request): JsonResponse
    {
        $data = $request->all();

        $order->fill($data);
        if ($order->isDirty()) {

            try {
                $order->save();
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return $this->errorResponse(__('Has error when update order'));
            }
        }

        return $this->successResponse($order);
    }
}