<?php
namespace App\Repositories;

use App\Models\AgeRestrictedSetting;

class AgeRestrictedSettingRepository extends AbstractRepo
{

    public function getModel(): string
    {
        return AgeRestrictedSetting::class;
    }

}
