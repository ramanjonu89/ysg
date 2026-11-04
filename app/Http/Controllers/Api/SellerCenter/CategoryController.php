<?php
namespace App\Http\Controllers\Api\SellerCenter;

use App\Http\Resources\SellerCenter\CategoryOptionCollection;
use App\Http\Resources\SellerCenter\CategoryTreeOption;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Api\SellerCenter\Coupon\CreateRequest;


class CategoryController extends Controller
{
    /**
     * @param CategoryService
     */
    protected $service;

    public function __construct(CategoryService $categoryService)
    {
        $this->service = $categoryService;
    }

    public function getBySeller(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->isStaff()) {
            $categories = $this->service->getCategoriesByUser();
        } else {
            $categories = $this->service->getCategoriesByUser($user->id);
        }
        $data = new CategoryOptionCollection($categories);
        return $this->successResponse($data);
    }

    public function tree(): JsonResponse
    {
        $data = CategoryTreeOption::collection($this->service->getAllList()->keyBy->id);
        return $this->successResponse($data);
    }


}