<?php
namespace App\Http\Controllers\Admin\Api;

use App\Services\PaymentSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    protected $service;

    public function __construct(PaymentSettingService $service)
    {
        $this->service = $service;
    }

    public function toggleActive(Request $request): JsonResponse
    {
        $this->service->toggleActive($request->input('method'));
        return response()->json([
            'code' => 200,
            'message' => __('Toggle Active Successfully!')
        ]);
    }
}

