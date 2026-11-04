<?php
/**
 * Created by PhpStorm.
 * User: NamDG
 * Date: 28/04/2022
 * Time: 01:43
 */

namespace App\Excel\ProductImport;

use App\Events\ProductUpdated;
use Carbon\Carbon;
use App\Models\ProductStock;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class Service
 * @package App\Excel\ProductImport\Service
 * @property
 */
class Service
{
    protected $importHandler;
    protected $repo;
    protected $validator;
    protected $productService;

    protected $user;
    protected $allowAttributes = [];



    public function __construct(
        Import $importHandler,
        Repo $repo,
        Validator $validator,
        ProductService $productService
    )
    {
        $this->importHandler = $importHandler;
        $this->repo = $repo;
        $this->validator = $validator;
        $this->productService = $productService;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    private function processProduct($data, $variationsData) {
        $productSku = $data['product_sku'];
        $hasVariation = (bool)count($variationsData);

        $validateResult = $this->validator->validateProduct($data, $variationsData);
        if ($validateResult !== true) {
            $message = 'Product SKU '.$productSku.' : '.$validateResult->first();
            return new \Exception($message);
        }

        try {

            if ($data['product_id']) {
                $product = $this->repo->getProductById($data['product_id']);
                if ($product && $product->user_id !== $this->user->id) {
                    throw new \Exception('This product do not belong to you');
                }
            }

            if ($data['brand']) {
                $brandId = $this->repo->getBrandIdByName($data['brand']);
                if (!$brandId) {
                    throw new \Exception('Brand Name is not correct');
                }
                $data['brand_id'] = $brandId;
            }

            $catId = $this->repo->getCatIdByName(trim($data['category']));
            if (!$catId) {
                throw new \Exception('Category Name is not correct');
            }
            $data['category_id'] = $catId;

            if ($data['sub_category']) {
                $subCatId = $this->repo->getSubCatIdByName($data['sub_category'], $catId);
                if (!$subCatId) {
                    throw new \Exception('SubCategory Name is not correct');
                }
                $data['sub_category_id'] = $subCatId;
            } else $data['sub_category_id'] = null;

            if ($data['sub_sub_category']) {
                $subSubCatId = $this->repo->getSubSubCatIdByName($data['sub_sub_category'], $subCatId);
                if (!$subCatId) {
                    throw new \Exception('SubSubCategory Name is not correct');
                }
                $data['sub_sub_category_id'] = $subSubCatId;
            } else $data['sub_sub_category_id'] = null;

            $productOptions = $this->calculateProductOptions($variationsData);

            $data['choice_options'] = json_encode($productOptions);
            $data['colors'] = json_encode([]);
            $data['user_id'] = $this->user->id;

            if ($hasVariation) {
                $data['unit_price'] = null;
                $data['stock'] = null;
                $data['variant_product'] = 1;
            } else {
                $data['variant_product'] = 0;
            }

            $data['slug'] = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $data['product_name'])) . '-' . Str::random(5);

            $data['maxQtyStart'] = !empty($data['maximum_purchase_quantity_start_date'])
                            ? Carbon::createFromFormat('d-m-Y', $data['maximum_purchase_quantity_start_date'])->startOfDay()->format('Y-m-d H:i:s') : null;
            $data['maxQtyEnd']   = !empty($data['maximum_purchase_quantity_end_date']) 
                            ? Carbon::createFromFormat('d-m-Y', $data['maximum_purchase_quantity_end_date'])->endOfDay()->format('Y-m-d H:i:s') : null;
            
            $maxSorting = $this->productService->getMaxProductSortingBySeller($this->user);
            $data['frontend_sorting'] = $maxSorting + 1;

            return $this->repo->saveProduct($data);

        } catch (\Exception $e) {
            $message = 'Product SKU '.$productSku.' : '.$e->getMessage();
            return new \Exception($message);
        }
    }

    private function processVariations($newVariations, $productId) {
        $oldVariations = $this->repo->getVariations($productId)->keyBy('sku');
        $oldVariations = $oldVariations->mapWithKeys(function ($value, $key) {
            return [strtoupper($key) => $value];
        });

        $countedSKU = $newVariations->countBy(function ($variation) {
            return $variation['product_sku'];
        });

        try {
            foreach ($newVariations as $variation) {
                $sku = strtoupper($variation['product_sku']);
                $currentId = isset($oldVariations[$sku]) ? $oldVariations[$sku]->id : 0;

                if ($countedSKU[$variation['product_sku']] > 1) {
                    $message = 'Variation SKU '.$variation['product_sku'].' : Duplicated Variation SKU';
                    throw new \Exception($message);
                }

                $validateResult = $this->validator->validateVariation($currentId, $variation, $this->allowAttributes);
                if ($validateResult !== true) {
                    $message = 'Variation SKU '.$variation['product_sku'].' : '.$validateResult->first();
                    throw new \Exception($message);
                }


                $key = $variation['variation_option_1'];
                if ($variation['variation_2']) {
                    $key .= ProductStock::SPLIT_SYMBOL.$variation['variation_option_2'];
                }
                $variation['variant'] = $key;


                if (isset($oldVariations[$sku])) {
                    $result = $this->repo->updateVariation($oldVariations[$sku]->id, $variation);
                } else {
                    $result = $this->repo->insertVariation($variation, $productId);
                }

                if ($result instanceof \Exception) {
                    throw $result;
                }
            }

            $deleteResult = $this->deleteUnUseVariations($productId, $oldVariations, $newVariations);
            if ($deleteResult instanceof \Exception) {
                throw $deleteResult;
            }

        } catch (\Exception $e) {
            return $e;
        }
        return true;
    }

    private function deleteUnUseVariations($productId, $oldVariations, $newVariations) {

        $currentSKUs = $oldVariations->pluck('sku');
        $newSKUs = $newVariations->pluck('product_sku');

        $diff = $currentSKUs->diff($newSKUs)->toArray();

        if ($diff) {
            return $this->repo->deleteVariations($productId, $diff);
        }

        return true;
    }

    private function calculateProductOptions($variations) {
        $options = [];
        foreach ($variations as $variation) {
            $options[$variation['variation_1']][] = $variation['variation_option_1'];
            if ($variation['variation_2']) {
                $options[$variation['variation_2']][] = $variation['variation_option_2'];
            }
        }

        $map = [];
        $flipped = array_flip($this->allowAttributes);
        foreach ($options as $name => $values) {
            if (isset($flipped[$name])) {
                $id = $flipped[$name];
                $map[] = [
                    'attribute' => [
                        'value' => $id,
                        'text' => $name,
                    ],
                    'values' => $values,
                    'attribute_id' => $id,
                ];
            }

        }

        return $map;
    }


    public function importByFile($file): array
    {
        $this->allowAttributes = $this->repo->getAllowAttributes($this->user);

        $result = [
            'failed' => 0,
            'failedReports' => [],
            'success' => 0,
            'total' => 0,
        ];

        $handlers = [
            0 => $this->importHandler,
        ];
        $sheets = Excel::toCollection(new MultipleSheets($handlers), $file);


        $groups = $sheets[0]->groupBy('group_id')->reverse();

        $countedProductSKU = $groups->countBy(function ($group) {
            return $group->first()['product_sku'];
        });

        $result['total'] = $groups->count();
        $successIds = [];

        foreach ($groups as $group) {
            try {
                $productData = $group->shift();

                if (!$productData['product_sku']) { // skip line empty
                    $result['total']--;
                    continue;
                }

                if ($countedProductSKU[$productData['product_sku']] > 1) {
                    $message = 'Product SKU '.$productData['product_sku'].' : Duplicated Product SKU';
                    throw new \Exception($message);
                }

                $hasVariation = (bool)count($group);

                DB::beginTransaction();
                $resultProcessProduct = $this->processProduct($productData, $group);
                if ($resultProcessProduct instanceof \Exception) {
                    throw $resultProcessProduct;
                } else {
                    if ($hasVariation) {
                        $resultProcessVariations = $this->processVariations($group, $resultProcessProduct);
                        if ($resultProcessVariations instanceof \Exception) {
                            $message = 'Product SKU '.$productData['product_sku'].' : '.$resultProcessVariations->getMessage();
                            throw new \Exception($message);
                        }
                    }
                }
                DB::commit();

                $this->productService->updateMinMaxPrice($resultProcessProduct);

                $result['success']++;

                $successIds[] = $resultProcessProduct;

            } catch (\Exception $e) {
                DB::rollBack();

                $result['failed']++;
                $result['failedReports'][] = $e->getMessage();
            }
        }

        $this->syncToOms($successIds);

        return $result;
    }

    private function syncToOms($productIds)
    {
        if (count($productIds)) {
            try {
                $user = $this->user;
                if ($user->api_key) {
                    // chunk to avoid timeout
                    $chunks = array_chunk($productIds, 10);
                    foreach ($chunks as $chunk) {
                        $syncResult = event(new ProductUpdated($user->id, $chunk));
                    }
                }
            } catch (\Exception $e) {
            }
        }
    }

}