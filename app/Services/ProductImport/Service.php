<?php
/**
 * Created by PhpStorm.
 * User: NamDG
 * Date: 28/04/2022
 * Time: 01:43
 */

namespace App\Services\ProductImport;

use App\Events\ProductUpdated;
use App\Services\BrandService;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class Service
 * @package App\Services\ProductImport
 * @property
 */
class Service
{
    protected $user;
    protected $productService;
    protected $brandService;
    protected $categoryService;
    protected $importHandler;

    public function __construct(
        ProductService $productService,
        BrandService $brandService,
        CategoryService $categoryService,
        Import $importHandler
    )
    {
        $this->productService = $productService;
        $this->brandService = $brandService;
        $this->categoryService = $categoryService;
        $this->importHandler = $importHandler;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    private function doImport($data) {
        try {
            $product = $this->productService->findBy('unit', $data['unit']);
        } catch (\Exception $e) {
            $product = null;
        }

        try {
            if ($product) {
                unset($data['slug']);
                return $this->productService->update($product->id, $data);
            } else {
                $data['photos'] = json_encode([]);
                return $this->productService->create($data);
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function importByFile($file): array
    {
        $result = [
            'failed' => 0,
            'failedReports' => [],
            'success' => 0,
            'total' => 0,
        ];

        $handlers = [
            0 => $this->importHandler,
        ];
        $sheets = Excel::toArray(new MultipleSheets($handlers), $file);

        $productIds = [];

        foreach ($sheets[0] as $index => $row) {
            $result['total'] ++;
            $validator = Validator::make($row, $this->rules());
            if (!$validator->fails()) {
                if ($data = $this->formatData($row)) {
                    if ($product = $this->doImport($data)) {
                        $productIds[] = $product->id;
                        $result['success'] ++;
                        continue;
                    }
                }
            }
            $result['failed'] ++;
            $result['failedReports'][] = [
                'row' => $index + 2,
                'sku' => $row['sku'],
            ];
        }

        if ($productIds) {
            try {
                $syncResult = event(new ProductUpdated($this->user->id, $productIds));
            } catch (\Exception $e) {
            }
        }

        return $result;
    }

    private function formatData($data): array
    {
        try {
            $data['tags'] = $data['product_tag'];
            $data['unit'] = $this->user->id . '-'. $data['sku'];
            $data['added_by'] = 'seller';
            $data['user_id'] = $this->user->id;
            $data['unit_price'] = $data['price'];
            $data['purchase_price'] = $data['price'];
            $data['slug'] = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $data['name'])) . '-' . Str::random(5);
//            $data['digital'] = $row['is_digital_goods'];
            $data['colors'] = json_encode([]);
            $data['choice_options'] = json_encode([]);
            $data['tax'] = 0;
            $data['discount'] = 0;
            $data['current_stock'] = $data['quantity']??1;

            if ($cat1Name = trim($data['category'])) {
                $cat1 = $this->categoryService->findBy('name', $cat1Name);
                $data['category_id'] = $cat1->id;

                if ($cat2Name = trim($data['subcategory'])) {
                    $cat2 = $this->categoryService->findSubBy($cat1->id, 'name', $cat2Name);
                    $data['subcategory_id'] = $cat2->id;

                    if ($cat3Name = trim($data['subsubcategory'])) {
                        $cat3 = $this->categoryService->findSubSubBy($cat2->id, 'name', $cat3Name);
                        $data['subsubcategory_id'] = $cat3->id;
                    }
                }
            } else throw new \Exception();

            if ($data['brand']) {
                $brand = $this->brandService->findBy('name', $data['brand']);
                $data['brand_id'] = $brand->id;
            }

        } catch (\Exception $e) {
            return [];
        }

        return $data;
    }

    public function rules(): array
    {
        return [
            // Can also use callback validation rules
            'sku' => 'required',
            'name' => 'required',
            'brand' => 'required',
            'category' => 'required',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ];
    }
}