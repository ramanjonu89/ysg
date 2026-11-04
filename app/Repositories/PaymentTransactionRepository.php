<?php
namespace App\Repositories;

use App\Models\PaymentTransaction;

class PaymentTransactionRepository extends AbstractRepo
{
    public function getModel(): string
    {
        return PaymentTransaction::class;
    }

    public function getByOrderId($id) {
        $query = $this->model::query();
        return $query->where('order_id', $id)->first();
    }
}
