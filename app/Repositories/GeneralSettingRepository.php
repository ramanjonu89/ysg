<?php
namespace App\Repositories;

use App\Models\GeneralSetting;

class GeneralSettingRepository extends AbstractRepo
{

    const PER_PAGE = 15;

    public function getModel(): string
    {
        return GeneralSetting::class;
    }
}
