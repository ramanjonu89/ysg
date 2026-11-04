<?php
namespace App\Http\Controllers\Api\SellerCenter\Setting;

use App\Http\Controllers\Api\SellerCenter\Controller;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\ProductStockService;
use App\Services\ShopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeatureProductController extends Controller
{
    /**
     * @param ShopService
     */
    protected $service;

    /**
     * @param ProductService
     */
    protected $productService;

    /**
     * @param ProductStockService
     */
    protected $productStockService;

    public function __construct(
        ShopService $shopService, 
        ProductService $productService, 
        ProductStockService $productStockService) {
        $this->service = $shopService;
        $this->productService = $productService;
        $this->productStockService = $productStockService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return JsonResponse|void
     */
    public function index(Request $request)
    {
        $data = [
            'items' => []
        ];
        $user = $request->user();
        $shop = $user->shop;

        if ($featuredProducts = $shop->featured_products) {
            foreach (explode(',',$featuredProducts) as $id) {
                try {
                    $product = $this->productService->get($id);
                    if ($product) {
                        $data['items'][] = [
                            'id' => $product->id,
                            'name' => $product->name,
                            'image' => '/' . ltrim($product->thumbnail_img, '/')??'',
                        ];
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return $this->successResponse($data);
    }

    /**
     * Add feature product.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $shop = $user->shop;
        $ids = $this->getProductIds($request->get('ids'));
        $status = $this->service->addFeaturedProduct($shop, $ids);
        if ($status == false) {
            return $this->errorResponse(__('Has error when add product'));
        }
        return $this->successResponse([]);
    }

    /**
     * Update sort of feature product.
     *
     * @param  Request  $request
     * @param  Product  $product
     * @return JsonResponse
     */
    public function updateSort(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();
        $shop = $user->shop;

        if ($featuredProducts = $shop->featured_products) {
            $ids = explode(',',$featuredProducts);
            $newIndex = $request->newIndex;
            $oldIndex = $request->oldIndex;
            if ($newIndex != $oldIndex) {
                array_splice($ids,max($newIndex,0),0,array_splice($ids,max($oldIndex,0),1));
                $data['featured_products'] = implode(',', $ids);
                $this->service->update($shop->id, $data);
            }
            return $this->successResponse([]);
        }

        return $this->errorResponse(__('Has error when update sort')) ;
    }

    /**
     * Remove feature product.
     *
     * @param  Request  $request
     * @param  Product  $product
     * @return JsonResponse
     */
    public function destroy(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();
        $shop = $user->shop;
        $this->service->removeFeaturedProduct($shop, $product->id);
        return $this->successResponse([]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $user = $request->user();
        $shop = $user->shop;

        $name = $request->search;
        $excludedIds = $request->excludedIds;

        $params = [
            'name' => $name,
            'notIds' => $excludedIds,
        ];

        $products = $this->service->getProducts($shop, $params)->items();

        $items = collect($products)->map(function($item) {
            return [
                'value' => $item->id,
                'text' => $item->name,
            ];
        });

        return $this->successResponse(['items' => $items]);
    }

    private function getProductIds($ids) {
        $productIds = [];
        foreach ($ids as $id) {
            if (strpos($id, 'v-') === 0) {
                try {
                    $productId = $this->productStockService->findBy('id', substr($id, 2))->product_id;
                    if ($productId && !in_array($productId, $productIds)) {
                        $productIds[] = $productId;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            } else {
                $productIds[] = $id;
            }
        }

        return $productIds;
    }

}