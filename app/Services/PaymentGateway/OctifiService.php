<?php
namespace App\Services\PaymentGateway;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentTransactionRepository;
use App\Services\OrderService;
use App\Services\PaymentGateway\Octifi\Charge;
use App\Services\PaymentGateway\Octifi\Checkout;
use App\User;

class OctifiService
{
    protected $checkout;
    protected $charge;

    protected $orderService;

    public function __construct()
    {
        $this->checkout = new Checkout();
        $this->charge = new Charge(new PaymentTransactionRepository());

        $this->orderService = app()->make(OrderService::class);
    }

    public function extractOrderId($data) {
        $json = base64_decode($data['data']);
        $data = json_decode($json);
        if ($data) {
            $order = $this->orderService->get($data->merchant_order_id);
            if ($order) {
                return $order->id;
            }
        }
        return false;
    }

    public function getRedirectLink(User $user, Order $order): string
    {
        return $this->checkout->getRedirectLink($user, $order);
    }

    public function successCallback($data): array
    {
        $result = [
            'status' => false,
            'message' => __('Payment process not success'),
            'oderId' => 0,
        ];

        $json = base64_decode($data['data']);
        $data = json_decode($json);
        if ($data) {
            $order = $this->orderService->get($data->merchant_order_id);
            if ($order) {
                $result['oderId'] = $order->id;
                $createResult = $this->charge->create($data->checkout_token, $order);
                if ($createResult['status'] == true) {
                    $captureResult = $this->charge->capture($createResult['chargeId'], $order);
                    if ($captureResult['status'] == true) {
                        $result = [
                            'status' => true,
                            'message' => __('Payment success'),
                            'details' => $captureResult['details'],
                        ];
                    }
                }
            }
        }

        return $result;
    }
}