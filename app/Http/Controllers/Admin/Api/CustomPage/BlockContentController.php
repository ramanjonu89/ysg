<?php

namespace App\Http\Controllers\Admin\Api\CustomPage;

use App\Http\Controllers\Api\Controller;
use App\Models\CustomPage\Block;
use App\Models\CustomPage\BlockContent;
use App\Services\CustomPage\BlockContentService;
use App\Services\CustomPage\BlockService;
use App\Services\CustomPage\YsgBlockContentTranslationService;
use App\Services\ProductService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BlockContentController extends Controller
{
    protected $service;
    protected $blockService;
    protected $productService;
    protected $ysgBlockContentTranslationService;

    public function __construct(
        BlockContentService $service,
        BlockService $blockService,
        ProductService $productService,
        YsgBlockContentTranslationService $ysgBlockContentTranslationService
    ) {
        $this->service = $service;
        $this->blockService = $blockService;
        $this->productService = $productService;
        $this->ysgBlockContentTranslationService = $ysgBlockContentTranslationService;
    }

    /**
     * @param $image
     * @return string
     */
    private function storeImage($image): string
    {
        $image_file_name = time().rand(11111, 99999).$image->getClientOriginalName();
        $block_content_image_path = config('constant.block_content_image_path');
        if (!File::isDirectory($block_content_image_path)) {
            File::makeDirectory($block_content_image_path, 0775, true);
        }
        $image->move($block_content_image_path, $image_file_name);
        return '/'.$block_content_image_path.'/'.$image_file_name;
    }

    /**
     * @param  Block  $block
     * @param  Request  $request
     * @return JsonResponse
     */
    public function addProduct(Block $block, Request $request): JsonResponse
    {
        try {

            $blockContents = $block->blockContents()->get();

            $currentIds = $blockContents->map(function ($item) {
                return strval($item->options->id);
            })->toArray();

            $count = count($blockContents);
            $max = Block::TYPE_PRODUCT_CAROUSEL_LIMIT_CONTENT;

            $addIds = $request->get('ids');

            $collection = collect($addIds);
            $filtered = $collection->reject(function ($value, $key) use ($currentIds) {
                return in_array($value, $currentIds);
            });

            $productIds = $filtered->toArray();

            if ($productIds) {
                foreach ($addIds as $productId) {
                    if ($count < $max) {
                        $product = $this->productService->get($productId);

                        $blockTranslationIds = $block->translations->pluck('language_id');

                        $inputs = [];
                        $inputs['language_id']      = $blockTranslationIds;
                        $inputs['is_enabled']       = true;
                        $inputs['block_id']         = $block->id;
                        $inputs['title']            = $product->name;
                        $inputs['description']      = '' ;
                        $inputs['content']          = '';
                        $inputs['options']          = json_encode(['id' => $product->id]);
                        $inputs['image']            = '/'.$product->thumbnail_img;

                        if (!$this->doStore($inputs)) {
                            throw new \Exception('');
                        }

                        $count++;
                    } else {
                        return $this->errorResponse(__('Limit Products reached!'));
                    }
                }
                return $this->successResponse(__('Item Block has been inserted successfully'));
            } else {
                return $this->errorResponse(__('Item Blocks exits'));
            }
        } catch (Exception $e) {
        }

        return $this->errorResponse(__('Something went wrong'));
    }

    /**
     * @param $data
     * @return bool
     */
    private function doStore($data): bool
    {
        try {
            DB::beginTransaction();

            $blockContent = $this->service->create($data);
            if ($blockContent) {
                if (isset($image)) {
                    $imagePath = $this->storeImage($image);
                    $this->service->update($blockContent->id, [
                        'image' => $imagePath,
                    ]);
                }
                $inputYsgBlockContentTranslations = $this->ysgBlockContentTranslationService->formatDataInputs((object) $data,
                    $blockContent->id);
                $this->ysgBlockContentTranslationService->insert($inputYsgBlockContentTranslations);

                DB::commit();
                return true;
            } else {
                DB::rollBack();
            }
        } catch (Exception $e) {
            DB::rollBack();
        }

        return false;
    }

    /**
     * @param  Block  $block
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Block $block, Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $data = $request->all();
            $inputs = $this->service->formatDataInputs((object) $request->all());
            $inputs['block_id'] = $block->id;

            if (isset($data['image'])) {
                $image = $data['image'];
                unset($data['image']);
            }
            $blockContent = $this->service->create($inputs);
            if ($blockContent) {
                if (isset($image)) {
                    $imagePath = $this->storeImage($image);
                    $this->service->update($blockContent->id, [
                        'image' => $imagePath,
                    ]);
                }
                $inputYsgBlockContentTranslations = $this->ysgBlockContentTranslationService->formatDataInputs($request,
                    $blockContent->id);
                $this->ysgBlockContentTranslationService->insert($inputYsgBlockContentTranslations);

                DB::commit();
                return $this->successResponse(__('Item Block has been inserted successfully'));
            } else {
                DB::rollBack();
            }
        } catch (Exception $e) {
            DB::rollBack();
        }

        return $this->errorResponse(__('Something went wrong'));
    }

    /**
     * @param  BlockContent  $content
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update(BlockContent $content, Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $inputs = $this->service->formatDataInputs($request);
            $data = $request->all();
            if (isset($data['image'])) {
                $inputs['image'] = $this->storeImage($data['image']);
            }

            $result = $this->service->update($content->id, $inputs);
            if ($result) {
                $inputYsgBlockContentTranslations = $this->ysgBlockContentTranslationService->formatDataInputs($request,
                    $content->id);
                $this->ysgBlockContentTranslationService->updateOrCreateMultiple($inputYsgBlockContentTranslations);
                DB::commit();
                return $this->successResponse(__('Item Block has updated successfully'));
            } else {
                DB::rollBack();
            }
        } catch (Exception $e) {
            DB::rollBack();
        }

        return $this->errorResponse(__('Something went wrong'));
    }


    /**
     * @param  BlockContent  $content
     * @return JsonResponse
     */
    public function destroy(BlockContent $content): JsonResponse
    {
        $this->service->destroy($content->id);
        return $this->successResponse();
    }
}

