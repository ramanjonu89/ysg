<?php
namespace App\Http\Controllers\Api;

use App\Http\Resources\SliderCollection;
use App\Services\SliderService;

class SliderController extends Controller
{
    protected $service;

    public function __construct(SliderService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return new SliderCollection($this->service->getPublishedList());
    }
}

