<?php
namespace App\Services;

use App\Models\Order;
use App\Services\PaymentGateway\CybersourceService;
use App\Services\PaymentGateway\NetsService;
use App\Services\PaymentGateway\OctifiService;
use App\User;

class PaymentGatewayService
{
    protected $gateway;

    public function __construct(string $paymentMethod)
    {
        if ($paymentMethod == Order::PAYMENT_TYPE_OCTIFI) {
            $this->gateway = new OctifiService();
        } else if ($paymentMethod == Order::PAYMENT_TYPE_CYBERSOURCE) {
            $this->gateway = new CybersourceService();
        } else if ($paymentMethod == Order::PAYMENT_TYPE_NETS) {
            $this->gateway = new NetsService();
        }
    }

    public function getFormData(User $user, Order $order): array
    {
        return $this->gateway->getFormData($user, $order);
    }

    public function getRedirectLink(User $user, Order $order): string
    {
        return $this->gateway->getRedirectLink($user, $order);
    }

    public function successCallback($data): array
    {
        return $this->gateway->successCallback($data);
    }

    public function extractOrderId($data) {
        return $this->gateway->extractOrderId($data);
    }
}