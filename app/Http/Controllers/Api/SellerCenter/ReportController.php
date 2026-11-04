<?php
namespace App\Http\Controllers\Api\SellerCenter;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * @param ReportService
     */
    protected $service;

    public function __construct(ReportService $reportService) {
        $this->service = $reportService;
    }

    public function summary(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->successResponse($this->service->getSummaryReport());
    }

    public function business(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->successResponse($this->service->getBusinessReport());
    }

    public function ordersChartData(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->successResponse($this->service->getOrdersChartData($request->all()));
    }
}