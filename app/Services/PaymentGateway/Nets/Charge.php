<?php
namespace App\Services\PaymentGateway\Nets;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Repositories\PaymentTransactionRepository;

class Charge
{
    protected $paymentTransactionRepo;

    public function __construct(PaymentTransactionRepository $paymentTransactionRepository)
    {
        $this->paymentTransactionRepo = $paymentTransactionRepository;
    }


    public function capture($details, Order $order): array
    {

        $data = [
            'order_id' => $order->id,
            'amount' => $order->grand_total,
            'method' => Order::PAYMENT_TYPE_NETS,
            'status' => PaymentTransaction::STATUS_CONFIRM,
            'details' => json_encode($details),
        ];

        $this->paymentTransactionRepo->create($data);

        $result = [
            'status' => true,
            'details' => $details,
        ];

        return $result;
    }
}