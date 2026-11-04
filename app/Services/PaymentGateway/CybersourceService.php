<?php
namespace App\Services\PaymentGateway;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentTransactionRepository;
use App\Services\OrderService;
use App\Services\PaymentGateway\Cybersource\Charge;
use App\Services\PaymentGateway\Cybersource\Checkout;
use App\User;

class CybersourceService
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
        $orderCode = $data['req_reference_number'];
        $order = $this->orderService->getByCode($orderCode);
        return $order?$order->id:null;
    }

    public function getFormData(User $user, Order $order): array
    {
        return $this->checkout->getFormData($user, $order);
    }

    public function successCallback($data): array
    {
        $result = [
            'status' => false,
            'message' => __('Payment process not success'),
            'oderId' => 0,
        ];

        if ($data) {
            $order = $this->orderService->getByCode($data['req_reference_number']);
            if ($order) {
                $result['oderId'] = $order->id;
                $createResult = $this->charge->capture($data, $order);
                if ($createResult['status'] == true) {
                    $result = [
                        'status' => true,
                        'message' => __('Payment success'),
                        'details' => $data,
                    ];
                }
            }
        }

        return $result;
    }
}