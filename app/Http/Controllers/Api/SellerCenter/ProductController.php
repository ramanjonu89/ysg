<?php

namespace App\Http\Controllers\Api\SellerCenter;

use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Excel\ProductExport;
use App\Http\Requests\Api\SellerCenter\Product\{
    CreateRequest,
    ImportRequest,
    UpdateRequest
};

use App\Http\Resources\SellerCenter\{
    ProductCollection,
    ProductWithVariantCollection,
    AttributeOptionCollection,
    ProductDetail
};

use Illuminate\Http\{
    JsonResponse,
    Request
};

use App\Models\{
    Product,
    ProductStock
};

use App\Services\{
    BrandService,
    CategoryService,
    ProductService,
    ProductStockService,
    SubCategoryService,
    SubSubCategoryService
};

use Illuminate\Support\{
    Carbon,
    Str
};

use Illuminate\Support\Facades\{
    Log,
    Validator
};

use App\Excel\ProductImport\Service as ProductsImport;
use App\User;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * @param ProductService
     */
    protected $service;

    /**
     * @param CategoryService
     */
    protected $categoryService;

    /**
     * @param BrandService
     */
    protected $brandService;
    protected $productsImport;

    /**
     * @param ProductStockService
     */
    protected $productStockService;

    /**
     * @param SubCategoryService
     */
    protected $subCategoryService;

    /**
     * @param SubSubCategoryService
     */
    protected $subsubCategoryService;

    public function __construct(
        ProductService $productService,
        CategoryService $categoryService,
        BrandService $brandService,
        ProductsImport $productsImport,
        SubCategoryService $subCategoryService,
        ProductStockService $productStockService,
        SubSubCategoryService $subSubCategoryService
    ) {
        $this->service = $productService;
        $this->categoryService = $categoryService;
        $this->brandService = $brandService;
        $this->productsImport = $productsImport;
        $this->subCategoryService = $subCategoryService;
        $this->productStockService = $productStockService;
        $this->subsubCategoryService = $subSubCategoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if ($user->user_type === 'staff') {
            $user = null;
        }
        $data = new ProductCollection($this->service->getProducts($user, $request));
        return $this->successResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRequest $request): \Illuminate\Http\JsonResponse
    {
        $response = [];
        $data = $request->all();
        $images = $request->file('photos');

        $data['added_by'] = 'seller';
        $data['published'] = $data['published'] == 'true';
        $data['category_id'] = isset($data['category_id']) ? intval($data['category_id']) : "";
        $data['subcategory_id'] = isset($data['subcategory_id']) ? intval($data['subcategory_id']) : "";
        $data['subsubcategory_id'] = isset($data['subsubcategory_id']) ? intval($data['subsubcategory_id']) : "";

        $stocks = $data['stocks'];
        unset($data['stocks']);

        $stocksValidator = $this->validateProductStocks($stocks);
        if ($stocksValidator) {
            return $this->errorResponse($stocksValidator);
        }

        try {
            $attributes = [];
            $data['photos'] = '';

            // if (isset($data['thumbnail_img'])) {
            //     if (isBase64Image($data['thumbnail_img'])) {
            //         $data['thumbnail_img'] = $this->storeImage($data['thumbnail_img'], config('constant.product_thumbnail_path'));
            //     }
            // } else if (is_array($photos)) {
            //     $data['thumbnail_img'] = $photos[0]??'';
            // }
            // $thumbnail_img_index = $data['thumbnail_img_index'] ?? 0;
            // $data['thumbnail_img'] = $photos[$thumbnail_img_index] ?? '';

            if ($request->has('meta_img') && $data['meta_img'] !== 'null') {
                $data['meta_img'] = $this->storeImage($data['meta_img'], config('constant.product_meta_image_path'));
            }

            $data['slug'] = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $data['name'])) . '-' . Str::random(5);
            $data['colors'] = json_encode([]);

            foreach ($data['choice_options'] as $key => $option) {
                $attributeId = $option->attribute->value;
                $data['choice_options'][$key]->attribute_id = $attributeId;
                array_push($attributes, $attributeId);
            }
            $data['variant_product'] = count($attributes) ? 1 : 0;
            $data['attributes'] = json_encode($attributes);
            $data['choice_options'] = json_encode($data['choice_options']);
            $user = $request->user();
            $data['user_id'] = $user->id;
            $data['max_qty'] = $data['max_qty'] ?? 0;
            $data['unit_price'] = $data['unit_price'] ?? 0;
            $generateSku = substr($data['slug'], 0, 10) . '-' . Str::random(5);
            $data['unit'] = $data['variant_product'] ? $generateSku : ($data['unit'] ?? $generateSku);
            $data['purchase_price'] = $data['unit_price'] ?? 0;
            $data['current_stock']  = $data['current_stock'] ?? 0;

            if ($data['max_qty']) {
                $start_date = Carbon::create($data['max_qty_start']);
                $end_date = Carbon::create($data['max_qty_end']);
                $data['max_qty_start'] = $start_date->startOfDay();
                $data['max_qty_end'] = $end_date->endOfDay();
            } else {
                $data['max_qty_start'] = null;
                $data['max_qty_end'] = null;
            }

            $maxSorting = $this->service->getMaxProductSortingBySeller($user);
            $data['frontend_sorting'] = $maxSorting + 1;

            if (!isset($data['meta_title']) || $data['meta_title'] === null || $data['meta_title'] === 'null') {
                $data['meta_title'] = '';
            }

            if (!isset($data['meta_description']) || $data['meta_description'] === null || $data['meta_description'] === 'null') {
                $data['meta_description'] = '';
            }

            // default values
            $data['todays_deal'] = $data['todays_deal'] ?? 0;
            $data['featured'] = $data['featured'] ?? 0;
            $data['only_microsite'] = $data['only_microsite'] ?? 0;

            $product = $this->service->create($data);

            $thumbnail_img = $data['thumbnail_img'] ?? '';

            if (!empty($images)) {
                $photos = [];
                foreach($images as $image) {
                    $name = $this->service->uploadImage($product, $image);
                    $photos[] = $name;
                    if ($thumbnail_img == $image->getClientOriginalName()) {
                        $product->thumbnail_img = $name;
                    }
                }
                if (!$product->thumbnail_img && !empty($photos)) {
                    $product->thumbnail_img = $photos[0];
                }

                $product->photos = json_encode($photos);
                $product->save();
            }
        } catch (\Exception $e) {
            return $this->errorResponse(__('Has error when add product. ' . $e->getMessage())) ;
        }

        $this->saveProductStocks($product, $stocks);

        $this->service->updateMinMaxPrice($product->id);

        try {
            if ($user->api_key) {
                $syncResult = event(new ProductCreated($user->id,[$product->id]));
            }
        } catch (\Exception $e) {
        }

        return $this->successResponse($product);
    }

    /**
     * Get All select options.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSelectOptions(): \Illuminate\Http\JsonResponse
    {
        $categoryTree = $this->categoryService->getTree();
        $brands = $this->brandService->getMainBrands()->map(function ($brand) {
            return [
                'text' => $brand->name,
                'value' => $brand->id,
            ];
        });
        $data = [
            'categoryTree' => $categoryTree,
            'brands' => $brands,
        ];
        return $this->successResponse($data);
    }

    public function getAllowedAttributes(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $allowedAttributes = $user->shop->allowedAttributes;
        return $this->successResponse(new AttributeOptionCollection($allowedAttributes));
    }

    public function getProductAllowedAttributes(Product $product, Request $request): \Illuminate\Http\JsonResponse
    {
        $allowedAttributes = $product->user->shop->allowedAttributes;
        return $this->successResponse(new AttributeOptionCollection($allowedAttributes));
    }

    /**
     * Display the specified resource.
     *
     * @param  Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $product, Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if($this->service->isBelongTo($product, $user) === true) {
            return $this->successResponse(new ProductDetail($product, $user));
        } else {
            return $this->errorResponse(__("You don't have permission to view this product"));
        }
        //TODO: later logic will have to be placed elsewhere
    }

    private function storeImage($image, $folderPath = null): string
    {
        if (is_null($folderPath)) {
            $folderPath = config('constant.product_photo_path');
        }

        $filePath = storeBase64Image($folderPath, $image);
        if ($filePath) {
            return substr($filePath, 1);
        } else {
            Log::debug('Issue save storeBase64Image at Api/SellerCenter/ProductController . Data: '.$image);
        }
        return '';
    }

    /**
     * @param  Request  $request
     * @param  Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function togglePublish(Request $request, Product $product): \Illuminate\Http\JsonResponse
    {
        $data['published'] = intval($request->get('published'));
        $this->service->update($product->id, $data);
        $user = $request->user();
        if ($user->api_key) {
            $syncResult = event(new ProductUpdated($user->id, [$product->id]));
        }
        return $this->successResponse([]);
    }

    private function validateProductStocks($stocks, $ignoreIds = []): string
    {
        $uniqueSkus = collect($stocks)->pluck('sku')->unique();
        if ($uniqueSkus->count() === 1 && $uniqueSkus->contains(null)) {
            return '';
        }

        $notNullSkuCount = collect($stocks)->whereNotNull('sku')->count();
        if ($notNullSkuCount === count($stocks) && $uniqueSkus->count() < count($stocks)) {
            return __('Duplicate SKU in product variants.');
        }

        foreach ($stocks as $stock) {
            $stock = (array) $stock;
            $stockId = $stock['id'] ?? 0;
            $stockRules = [
                'sku' => [
                    'sometimes',
                    Rule::unique('products', 'unit')->where(function ($query) {
                        return $query->where('unit', 'not like', 'deleted%');
                    }),
                    Rule::unique('product_stocks', 'sku')->ignore($stockId),
                ],
            ];

            $validator = Validator::make($stock, $stockRules);
            if ($validator->fails()) {
                return $validator->errors()->first();
            }
        }

        return '';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, Product $product): \Illuminate\Http\JsonResponse
    {
        $data = $request->all();
        $images = $request->file('added_images');

        $stocks = $data['stocks']??[];
        unset($data['stocks']);

        $currentStockIds = $product->stocks->pluck('id')->toArray();

        $stocksValidator = $this->validateProductStocks($stocks, $currentStockIds);
        if ($stocksValidator) {
            return $this->errorResponse($stocksValidator);
        }

        if (!empty($images)) {
            $photos = [];
            foreach($images as $image) {
                $originalName = $image->getClientOriginalName();
                $name = $this->service->uploadImage($product, $image);

                $photos[] = $name;
                if ($data['thumbnail_img'] == $originalName) {
                    $data['thumbnail_img'] = $name;
                }
                $key = array_search($originalName, $data['photos']);
                if ($key !== false) {
                    $data['photos'][$key] = $name;
                }
            }
        }

        if (!$data['thumbnail_img'] && !empty($photos)) {
            $data['thumbnail_img'] = $photos[0];
        }

        $product->fill($data);

        if ($product->isDirty()) {
            $changes = $product->getDirty();

            foreach ($changes as $key => $change) {
                if ($key == 'meta_img' && null !== $data['meta_img']) {
                    $product->meta_img = $this->storeImage($change, config('constant.product_meta_image_path'));
                }

                if ($key === 'choice_options') {
                    $attributes = [];
                    foreach ($data['choice_options'] as $keyOption => $option) {
                        $attributeId = $option->attribute->value;
                        $data['choice_options'][$keyOption]->attribute_id = $attributeId;
                        array_push($attributes, $attributeId);
                    }
                    $product->choice_options = $data['choice_options'];
                    $product->attributes = json_encode($attributes);

                }

                if ($key === Product::MAX_QTY_START) {
                    $date = Carbon::create($change); // Replace with your desired date
                    $product->max_qty_start = $date->startOfDay();
                }

                if ($key === Product::MAX_QTY_END) {
                    $date = Carbon::create($change); // Replace with your desired date
                    $product->max_qty_end = $date->endOfDay();
                }

                if ($key === 'name') {
                    $product->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $data['name'])) . '-' . Str::random(5);
                }

                if ($key === 'meta_title' && $data['meta_title'] === 'null') {
                    $data['meta_title'] = '';
                }

                if ($key === 'meta_description' && $data['meta_description'] === 'null') {
                    $data['meta_description'] = '';
                }

            }

            if(!$data['max_qty']) {
                $product->max_qty_start = null;
                $product->max_qty_end = null;
            }

            if ($product->thumbnail_img == '' && !empty($changes['photos'])) {
                $product->thumbnail_img = $changes['photos'][0];
            }

            $product->discount = $data['discount'] ?? 0;
            $product->current_stock = $data['current_stock'] ?? 0;
            $product->variant_product = $stocks ? 1 : 0;
        }

        try {
            $product->save();
        } catch (\Exception $e) {
            return $this->errorResponse(__('Has error when update product 1 ' . $e->getMessage())) ;
        }

        $this->saveProductStocks($product, $stocks);

        $this->service->updateMinMaxPrice($product->id);

        try {
            $user = $request->user();
            if ($user->api_key) {
                $syncResult = event(new ProductUpdated($user->id, [$product->id]));
            }
        } catch (\Exception $e) {
        }

        return $this->successResponse([]);
    }

    private function saveProductStocks($product, $stocks) {
        $this->service->removeStock($product);
        if (!empty($stocks)) {
            foreach ($stocks as $stock) {
                $stock = (array) $stock;
                $stock['product_id'] = $product->id;
                $stock['sku'] = $stock['sku'] ?? $product->unit . '-' . $stock['variant'] . '-' . Str::random(5);
                if (!empty($stock['image']) && isBase64Image($stock['image'])) {
                    $stock['image'] = $this->storeImage($stock['image'], config('constant.product_photo_path'));
                }

                if (empty($stock['discount_type'])
                    || !in_array($stock['discount_type'], [ProductStock::DISCOUNT_TYPE_AMOUNT, ProductStock::DISCOUNT_TYPE_PERCENT])) {
                    $stock['discount'] = 0;
                    $stock['discount_type'] = ProductStock::DISCOUNT_TYPE_AMOUNT;
                }

                $this->service->addStock($product, $stock);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return void
     */
    public function destroy($id)
    {
        //
    }


    /**
     * Bulk import resource from upload file.
     *
     * @param  ImportRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importByFile(ImportRequest $request): \Illuminate\Http\JsonResponse
    {
        if ($request->hasFile('uploaded_file')) {
            $user = $request->user();
            $this->productsImport->setUser($user);
            $result = $this->productsImport->importByFile($request->file('uploaded_file'));

            $message = sprintf(
                __('Import total %s, success %s, failed %s.'),
                $result['total'],
                $result['success'],
                $result['failed'],
            );
            if ($result['failed']) {
                return $this->errorResponse($message, $result);
            } else {
                return $this->successResponse([
                    'message' =>  $message,
                    'data' =>  $result
                ]);
            }
        }
        return $this->errorResponse(__('Server has error, please try again!'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function exportExcel(Request $request): JsonResponse
    {
        $user = $request->user();
        $productIds = [];
        $filePath = '/download/product_export_'.$user->id.'.xlsx';
        Excel::store(new ProductExport($user, $productIds), $filePath);

        return $this->successResponse([
            'filePath' =>  $filePath
        ]);
    }

    public function updateProductPrice(Request $request, Product $product): \Illuminate\Http\JsonResponse
    {
        try {
            $rawData = $request->get('data');
            foreach($rawData as $item) {
                if (count($product->stocks)) {
                    $stock = $this->productStockService->getFirstWithConditions(['product_id' => $product->id, 'variant' => $item['variant']]);
                    $data['price'] = $item['price'];
                    $this->productStockService->update($stock->id, $data);
                } else {
                    $data['unit_price'] = $item['price'];
                    $this->service->update($product->id, $data);
                }
            }
            $this->service->updateMinMaxPrice($product->id);

            $user = $request->user();
            if ($user->api_key) {
                $syncResult = event(new ProductUpdated($user->id, [$product->id]));
            }


            return $this->successResponse([]);
        } catch (\Exception $e) {
            return $this->errorResponse(__('Has error when update product 2 ' . $e->getMessage()));
        }
    }

    public function updateProductStock(Request $request, Product $product): \Illuminate\Http\JsonResponse
    {
        try {
            $rawData = $request->get('data');
            foreach($rawData as $item) {
                if (count($product->stocks)) {
                    $stock = $this->productStockService->getFirstWithConditions(['product_id' => $product->id, 'variant' => $item['variant']]);
                    $data['qty'] = $item['quantity'];
                    $this->productStockService->update($stock->id, $data);
                } else {
                    $data['current_stock'] = $item['quantity'];
                    $this->service->update($product->id, $data);
                }
            }

            $user = $request->user();
            if ($user->api_key) {
                $syncResult = event(new ProductUpdated($user->id, [$product->id]));
            }

            return $this->successResponse([]);
        } catch (\Exception $e) {
            return $this->errorResponse(__('Has error when update product 3 ' . $e->getMessage())) ;
        }
    }

    public function updateSort(Request $request)
    {
        $user = $request->user();
        $products = $this->service->getProducts($user, $request);
        if ($this->service->dontHaveFrontSortingValue($products->toArray()['data'])) {
            $this->service->initFrontSortingValue($products);
        }

        $newIndex = $request->newIndex;
        $oldIndex = $request->oldIndex;

        $tmpIndex = [];

        $productCollection = $products->getCollection();

        $product_1 = $productCollection[$newIndex];
        $product_2 = $productCollection[$oldIndex];

        $tmpIndex[$oldIndex] = $product_2->frontend_sorting;
        $product_2->frontend_sorting = $product_1->frontend_sorting;
        $product_2->save();

        $biggerIndex = $newIndex > $oldIndex ? $newIndex : $oldIndex;
        $smallerIndex = $newIndex > $oldIndex ? $oldIndex : $newIndex;

        if ($smallerIndex != $oldIndex) {
            for ($i = $smallerIndex; $i < $biggerIndex; $i++) {
                $product = $productCollection[$i];
                $tmpIndex[$i] = $product->frontend_sorting;
                $product->frontend_sorting = isset($tmpIndex[$i+1]) ? $tmpIndex[$i+1] : $productCollection[$i+1]->frontend_sorting;
                $product->save();
                unset($product);
            }
        } else {
            for ($i = $smallerIndex + 1; $i <= $biggerIndex; $i++) {
                $product = $productCollection[$i];
                $tmpIndex[$i] = $product->frontend_sorting;
                $product->frontend_sorting = isset($tmpIndex[$i-1]) ? $tmpIndex[$i-1] : $productCollection[$i-1]->frontend_sorting;
                $product->save();
                unset($product);
            }
        }

        return $this->successResponse([]);
    }

    public function updateProductDiscount(Request $request, Product $product): \Illuminate\Http\JsonResponse
    {
        try {
            $rawData = $request->get('data');
            foreach($rawData as $item) {
                if (count($product->stocks)) {
                    $stock = $this->productStockService->getFirstWithConditions(['product_id' => $product->id, 'variant' => $item['variant']]);
                    $data['discount'] = $item['discount'];
                    $data['discount_type'] = $item['discount_type'];
                    $this->productStockService->update($stock->id, $data);
                } else {
                    $data['discount'] = $item['discount'];
                    $data['discount_type'] = $item['discount_type'];
                    $this->service->update($product->id, $data);
                }
            }
            $this->service->updateMinMaxPrice($product->id);
            return $this->successResponse([]);
        } catch (\Exception $e) {
            return $this->errorResponse(__('Has error when update product 4 ' . $e->getMessage())) ;
        }
    }

    public function productsWithVariant(Request $request) {
        $user = $request->user();
        if ($user->user_type === User::TYPE_STAFF) {
            $user = null;
        }
        $data = new ProductWithVariantCollection($this->service->getProductsWithVariant($user, $request));
        return $this->successResponse($data);
    }
}
