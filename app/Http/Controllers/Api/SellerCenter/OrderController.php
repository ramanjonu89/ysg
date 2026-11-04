<?php
namespace App\Http\Controllers\Api\SellerCenter;

use App\Excel\OrderExport;
use App\Http\Resources\SellerCenter\OrderCollection;
use App\Http\Resources\SellerCenter\Order as OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    /**
     * @param OrderService
     */
    protected $service;

    public function __construct(OrderService $sellerService) {
        $this->service = $sellerService;
    }

    /**
     * Get list order of seller
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $data = new OrderCollection($this->service->getOrders($user, $request));
        return $this->successResponse($data);
    }

    /**
     * @param  Order  $order
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order, Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $data = new OrderResource($order);
        return $this->successResponse($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function exportExcel(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $data = $this->service->buildParamsFromRequest($request);

        $filePath = '/download/order_export_'.$user->id.'.xlsx';
        Excel::store(new OrderExport($user, $data), $filePath);

        return $this->successResponse([
            'filePath' =>  $filePath
        ]);
    }
}