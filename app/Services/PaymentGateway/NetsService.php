<?php
namespace App\Services\PaymentGateway;

use App\Models\Order;
use App\Repositories\PaymentTransactionRepository;
use App\Services\OrderService;
use App\Services\PaymentGateway\Nets\Charge;
use App\Services\PaymentGateway\Nets\Checkout;
use App\Services\PaymentGateway\Nets\Response;
use App\User;

class NetsService
{
    protected $checkout;
    protected $charge;
    protected $response;

    public function __construct()
    {
        $this->checkout = new Checkout();
        $this->charge = new Charge(new PaymentTransactionRepository());
        $this->response = new Response();

        $this->orderService = app()->make(OrderService::class);
    }

    public function getFormData(User $user, Order $order): array
    {
        return $this->checkout->getFormData($user, $order);
    }

    public function extractOrderId($data) {
        $this->response->analysis($data);
        return $this->response->getOrderId();
    }

    public function successCallback($data): array
    {
        $result = [
            'status' => false,
            'message' => __('Payment process not success'),
            'oderId' => 0,
        ];

        $this->response->analysis($data);

        if ($this->response->getStatus() === Response::ORDER_SUCCESS) {
            $order = $this->orderService->get($this->response->getOrderId());
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