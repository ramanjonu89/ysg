<?php

namespace App\Services;

use App\Models\BusinessSetting;
use App\Repositories\BusinessSettingRepository;
use App\User;

class BusinessSettingService
{
    public $setting;
    protected $repo;

    public function __construct(BusinessSettingRepository $repo)
    {
        $this->repo = $repo;
        $this->setting = $this->all();
    }

    public function all(): array
    {
        return $this->repo->getAll()->pluck('value', 'type')->toArray();
    }

    public function get($key)
    {
        return $this->setting[$key] ?? null;
    }

    public function set($key, $value): bool
    {
        $this->setting[$key] = $value;

        if (is_array($value)) {
            $value = json_encode($value);
        }
        $model = $this->repo->getByKey($key);
        if ($model) {
            $this->repo->update($model->id, ['value' => $value]);
        } else {
            $this->repo->create([
                'type' => $key,
                'value' => $value,
            ]);
        }

        return true;
    }

    public function isCaptchaEnabled():bool
    {
        return $this->repo->isCaptchaEnabled();
    }

    public function cashPaymentEnabled($user_type) :bool
    {
        $type = BusinessSetting::CASH_PAYMENT_TYPE;

        if ($user_type === User::TYPE_ADMIN || $user_type === User::TYPE_STAFF || config('app.env') !== 'production') {
            return $this->repo->paymentSettingEnabled($type);
        }

        return false;
    }
}