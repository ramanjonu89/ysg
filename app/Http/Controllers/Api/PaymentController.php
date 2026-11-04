<?php
namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Services\CheckoutService;
use App\Services\OrderService;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentController extends Controller
{
    protected $checkoutService;
    protected $paymentGatewayService;
    protected $orderService;

    public function __construct(
        CheckoutService $checkoutService,
        OrderService $orderService
    )
    {
        $this->checkoutService = $checkoutService;
        $this->orderService = $orderService;
    }

    public function initGateway(string $gatewayName) {
        $this->paymentGatewayService = new PaymentGatewayService($gatewayName);
    }

    public function cashOnDelivery(Request $request)
    {
        $order = new OrderController;

        return $order->processOrder($request);
    }

    public function eNetsSuccessCallback(Request $request)
    {
//        Log::channel('payment')
//            ->info('App\Http\Controllers\Api\PaymentController@eNetsSuccessCallback',
//                [
//                    'request' => $request->all()
//                ]);

        $result = [
            'status' => false,
            'message' => ''
        ];

        try {
            $this->initGateway(Order::PAYMENT_TYPE_NETS);
            $orderId = $this->paymentGatewayService->extractOrderId($request->all());

            $order = $this->orderService->get($orderId);
            if ($order) {
                if (!$order->isPaid()) {
                    $resultProcess = $this->paymentGatewayService->successCallback($request->all());
                    if ($resultProcess && $resultProcess['status'] == true) {
                        $afterPayment = $this->checkoutService->processAfterPayment($order, $resultProcess['details']);
                        if ($afterPayment) {
                            $result['status'] = true;
                            $result['message'] = 'payment success';
                        } else {
                            throw new \Exception('Error checkoutService->processAfterPayment');
                        }
                    } else {
                        throw new \Exception('Error paymentGatewayService->successCallback');
                    }
                }
            } else {
                throw new \Exception('Payment process can not find order');
            }

        } catch (Throwable $e) {
            Log::channel('payment')
                ->error('Error App\Http\Controllers\Api\PaymentController@eNetsSuccessCallback',
                    [
                        'error' => $e->getMessage(),
                        'request' => $request->all()
                    ]);

            $result['message'] = 'payment failed';
        }

        return $this->successResponse($result);
    }
}

