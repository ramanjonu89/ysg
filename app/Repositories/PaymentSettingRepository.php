<?php
namespace App\Repositories;

use App\Models\PaymentSetting;

class PaymentSettingRepository extends AbstractRepo
{

    public function getModel(): string
    {
        return PaymentSetting::class;
    }

    public function getAll()
    {
        return $this->model::all();
    }

    public function getByKey($key) {
        $query = $this->model::query();
        return $query->where('key', $key)->first();
    }
}
