<?php
namespace App\Repositories;

use App\Models\BusinessSetting;

class BusinessSettingRepository extends AbstractRepo
{

    public function getModel(): string
    {
        return BusinessSetting::class;
    }

    public function getAll()
    {
        return $this->model::all();
    }

    public function isEnableCA() {
        $model = $this->getByKey('corporate_referral_enabled');
        if ($model && $model->value == 1) {
            return true;
        }
        return false;
    }

    public function getByKey($key) {
        $query = $this->model::query();
        return $query->where('type', $key)->first();
    }

    public function isCaptchaEnabled(): bool 
    {
        $model = $this->getByKey('google_recaptcha');
        if ($model && $model->value === "1") {
            return true;
        }
        else {
            return false;
        }
    }

    public function paymentSettingEnabled($key): bool
    {
        $model = $this->getByKey($key);
        if ($model && $model->value === "1") {
            return true;
        }

        return false;
    }
}
