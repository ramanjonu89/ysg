<?php
namespace App\Repositories;

use App\Models\SellerCenterSetting;

class SellerCenterSettingRepository extends SettingRepo
{
    public function getModel(): string
    {
        return SellerCenterSetting::class;
    }
}
