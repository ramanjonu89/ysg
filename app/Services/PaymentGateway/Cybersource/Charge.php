<?php
namespace App\Services\PaymentGateway\Cybersource;

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
        $result = [
            'status' => false,
            'details' => [],
        ];

        $data = [
            'order_id' => $order->id,
            'amount' => $order->grand_total,
            'method' => Order::PAYMENT_TYPE_CYBERSOURCE,
            'status' => PaymentTransaction::STATUS_PENDING,
            'details' => json_encode($details),
        ];

        if ($details['reason_code'] == 100) {
            $data['status'] = PaymentTransaction::STATUS_CONFIRM;
            $result = [
                'status' => true,
                'details' => $details,
            ];
        }
        $this->paymentTransactionRepo->create($data);

        return $result;
    }
}