<?php
namespace App\Http\Controllers\Api\SellerCenter;

use App\Http\Resources\SellerCenter\ProductCollection;
use App\Http\Resources\SellerCenter\Product as ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController2 extends Controller
{
    /**
     * @param ProductService
     */
    protected $service;

    public function __construct(ProductService $productService) {
        $this->service = $productService;
    }

    /**
     * Get list order of seller
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $data = new ProductCollection($this->service->getProducts($user, $request));
        return $this->successResponse($data);
    }

    /**
     * @param  Product  $order
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $order, Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $data = new ProductResource($order);
        return $this->successResponse($data);
    }

    /**
     * @param  Product  $order
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Product $product, Request $request): \Illuminate\Http\JsonResponse
    {
        dd($product);

        $user = $request->user();
        $data = new ProductResource($product);
        return $this->successResponse($data);
    }
}