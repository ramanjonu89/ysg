<?php
namespace App\Http\Controllers\Admin\Api;

use App\Repositories\SellerRepository;
use App\Services\SellerService;
use Illuminate\Http\Request;

class SellerListController extends Controller
{
    protected $service;

    public function __construct(SellerService $service)
    {
        $this->service = $service;
    }

    public function updateIndex(Request $request): \Illuminate\Http\JsonResponse
    {
        $listIndex = $request->listIndex;
        $this->service->updateIndex($listIndex);
        return response()->json([
            'code' => 200,
            'message' => __('Update Index Successfully!')
        ]);
    }

    public function toggleDisplay(Request $request): \Illuminate\Http\JsonResponse
    {
        $sellerId = $request->sellerId;
        $this->service->toggleDisplay($sellerId);
        return response()->json([
            'code' => 200,
            'message' => __('Toggle Display Successfully!')
        ]);
    }
}

