<?php
namespace App\Http\Controllers\Admin\Api\FrontendSetting;

use App\Http\Controllers\Admin\Controller;
use App\Services\BannerService;
use Illuminate\Http\Request;

class HomeBannerController extends Controller
{
    protected $service;

    public function __construct(BannerService $service)
    {
        $this->service = $service;
    }

    /**
     * Update index of model.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateIndex(Request $request)
    {
        $listIndex = $request->listIndex;
        $count = $this->service->updateIndex($listIndex);
        return response()->json([
            'code' => 200,
            'status' => (bool)$count,
        ]);
    }

    public function togglePublish($id)
    {
        $status = (boolean)$this->service->togglePublish($id);
        $message = $status?__('Banner status updated successfully'):__('Have error!');
        return response()->json([
            'code' => 200,
            'status' => $status,
            'message' => $message,
        ]);
    }
}

