<?php
namespace App\Services;

use App\Repositories\SellerCenterSettingRepository;

class SellerCenterSettingService extends SettingService
{
    public $setting;
    protected $repo;

    public function __construct(SellerCenterSettingRepository $repo)
    {
        return parent::__construct($repo);
    }
}