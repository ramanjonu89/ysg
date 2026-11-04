<?php
namespace App\Http\Controllers\Api\SellerCenter;

use App\Models\Brand;
use App\Http\Resources\SellerCenter\BrandCollection;
use App\Services\BrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use DB;
use Auth;

class BrandController extends Controller
{
    /**
     * @param BrandService
     */
    protected $service;

    public function __construct(BrandService $brandService)
    {
        $this->service = $brandService;
    }

    public function getBySeller(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->isStaff()) {
            $brands = $this->service->getBrandsByUser();
        } else {
            $brands = $this->service->getBrandsByUser($user->id);
        }
        $data = new BrandCollection($brands);
        return $this->successResponse($data);
    }

    public function getSelectOptions(): JsonResponse
    {
        $data = new BrandCollection($this->service->getAll());
        return $this->successResponse($data);
    }
}