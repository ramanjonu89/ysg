<?php
namespace App\Http\Controllers\Api;

use App\Http\Resources\BannerCollection;
use App\Services\BannerService;

class BannerController extends Controller
{
    protected $service;

    public function __construct(BannerService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return new BannerCollection($this->service->getPublishedList());
    }
}

