<?php
namespace App\Http\Controllers\Admin\Api\FrontendSetting;

use App\Http\Controllers\Admin\Controller;
use App\Services\SliderService;
use Illuminate\Http\Request;

class HomeSliderController extends Controller
{
    protected $service;

    public function __construct(SliderService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
    }

    /**
     * Update index of model.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateIndex(Request $request): \Illuminate\Http\JsonResponse
    {
        $listIndex = $request->listIndex;
        $count = $this->service->updateIndex($listIndex);
        return response()->json([
            'code' => 200,
            'status' => (boolean)$count,
        ]);
    }

    public function togglePublish($id)
    {
        $status = (boolean)$this->service->togglePublish($id);
        $message = $status?__('Slider status updated successfully'):__('Have error');
        return response()->json([
            'code' => 200,
            'status' => $status,
            'message' => $message,
        ]);
    }
}

