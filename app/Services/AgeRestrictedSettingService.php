<?php

namespace App\Services;

use App\Repositories\AgeRestrictedSettingRepository;
use App\Services\AbstractService;

class AgeRestrictedSettingService extends AbstractService
{
    public function __construct(AgeRestrictedSettingRepository $repo)
    {
        parent::__construct($repo);
    }
}
