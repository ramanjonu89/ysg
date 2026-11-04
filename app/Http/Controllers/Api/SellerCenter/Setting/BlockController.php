<?php
namespace App\Http\Controllers\Api\SellerCenter\Setting;

use App\Http\Controllers\Api\SellerCenter\Controller;
use App\Models\CustomPage\Block;
use App\Models\CustomPage\BlockContent;
use App\Models\CustomPage\Page;
use App\Models\Language;
use App\Models\Product;
use App\Services\CustomPage\BlockContentService;
use App\Services\CustomPage\BlockService;
use App\Services\CustomPage\YsgBlockTranslationService;
use App\Services\ShopService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BlockController extends Controller
{
    /**
     * @param BlockService
     */
    protected $blockService;

    /**
     * @param BlockContentService
     */
    protected $blockContentService;

    /**
     * @param YsgBlockTranslationService
     */
    protected $ysgBlockTranslationService;

    public function __construct(
        BlockService $blockService,
        BlockContentService $blockContentService,
        YsgBlockTranslationService $ysgBlockTranslationService

    ) {
        $this->blockService = $blockService;
        $this->blockContentService = $blockContentService;
        $this->ysgBlockTranslationService = $ysgBlockTranslationService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $data = [
            'items' => []
        ];
        $user = $request->user();
        $shop = $user->shop;

        $shopBlockType = $this->blockService->getMicrositeListType();
        if ($blocks = $shop->blocks) {
            foreach ($blocks as $block) {
                $data['items'][] = [
                    'id' => $block->id,
                    'name' => $block->title,
                    'is_enabled' => $block->is_enabled,
                    'type' => $shopBlockType[$block->type],
                ];
            }
        }
        return $this->successResponse($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Block  $block
     * @param  Request  $request
     * @return JsonResponse
     */
    public function show(Block $block, Request $request): JsonResponse
    {
        $block = $block->getAttributes();
        $data = $this->formatDatetime($block);
        $data['datetime_type'] = $this->getTypeDatetimeBlock($block);

        return $this->successResponse($data);
    }

    /**
     * Get list of types.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function getTypes(Request $request): JsonResponse
    {
        $data = [
            'items' => []
        ];
        $shopBlockType = $this->blockService->getMicrositeListType();
        foreach ($shopBlockType as $type => $name) {
            $data['items'][] = [
                'text' => $name,
                'value' => $type,
            ];
        }

        return $this->successResponse($data);
    }

    /**
     * Add feature product.
     *
     * @param  Request  $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = $request->user();
            $storeBlock = true;
            $data = $this->formatDataInputs($user, $request->all(), $storeBlock);

            $model = $this->blockService->create($data);

            if ($model) {
                $block = $model->getAttributes();
                $data = $this->formatDatetime($block);
                $data['datetime_type'] = $this->getTypeDatetimeBlock($block);

                $inputYsgBlockTranslations = $this->formatDataInputYsgBlockTranslations($model);
                $this->ysgBlockTranslationService->updateOrCreateMultiple($inputYsgBlockTranslations);

                DB::commit();
                return $this->successResponse($data);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->errorResponse(__('Something went wrong' . $e->getMessage()));
        }
    }

    /**
     * Add feature product.
     *
     * @param  Block  $block
     * @param  Request  $request
     * @return JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function update(Block $block, Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = $request->user();
            $data = $this->formatDataInputs($user, $request->all());

            $result = $this->blockService->update($block->id, $data);
            if ($result) {
                $inputYsgBlockTranslations = $this->formatDataInputYsgBlockTranslations($result);
                $this->ysgBlockTranslationService->updateOrCreateMultiple($inputYsgBlockTranslations);
                DB::commit();
                return $this->successResponse([]);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->errorResponse(__('Has error when update block' . $e->getMessage()));
        }
    }

    /**
     * Update sort of feature product.
     *
     * @param  Request  $request
     * @param  Product  $product
     * @return JsonResponse
     */
    public function updateSort(Block $block, Request $request): JsonResponse
    {
        $user = $request->user();
        $shop = $user->shop;

        if ($blocks = $shop->blocks) {
            if ($request->sort == 'up' && $block->sort > 1) {
                $tempBlock = $blocks->where('sort', $block->sort - 1)->first();
                $this->blockService->update($tempBlock->id, ['sort' => $block->sort]);
                $this->blockService->update($block->id, ['sort' => $block->sort - 1]);
            } else if ($request->sort == 'down' && $block->sort < count($blocks)) {
                $tempBlock = $blocks->where('sort', $block->sort + 1)->first();
                $this->blockService->update($tempBlock->id, ['sort' => $block->sort]);
                $this->blockService->update($block->id, ['sort' => $block->sort + 1]);
            }
        }

        return $this->successResponse([]);
    }

    public function updateListSort(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->get('data', []);
        $shop = $user->shop;
        $blocks = $shop->blocks;

        foreach ($data as $key => $item) {
            $tempBlock = $blocks->where('id', $item['id'])->first();
            if (!$tempBlock) {
                return $this->errorResponse('Not found Block');
            }

            $this->blockService->update($item['id'], ['sort' => $key + 1]);
            $shop->blocks()->detach($tempBlock);
            $shop->blocks()->attach($tempBlock, ['sort' => $key + 1]);
        }

        return $this->successResponse([]);
    }

    /**
     * Remove feature product.
     *
     * @param  Request  $request
     * @param  Product  $product
     * @return JsonResponse
     */
    public function destroy(Block $block): JsonResponse
    {
        $this->blockService->destroy($block->id);
        return $this->successResponse([]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Block  $block
     * @param  Request  $request
     * @return JsonResponse
     */
    public function items(Block $block, Request $request): JsonResponse
    {
        $items = $block->allBlockContents()->get();

        $sort = 1;
        $items->map(function($item) use (&$sort) {
            $item->sort = $sort;
            $sort++;
            return $item;
        });

        return $this->successResponse(['items' => $items]);
    }

    /**
     * Add BlockContent item.
     *
     * @param  Block  $block
     * @param  Request  $request
     * @return JsonResponse
     */
    public function storeItem(Block $block, Request $request): JsonResponse
    {
        $data = $request->all();

        $data['block_id'] = $block->id;

        if (isset($data['imageData'])) {
            $imageData = $data['imageData'];
            unset($data['imageData']);
        }
        if (isset($data['options'])) {
            $data['options'] = json_encode($data['options']);
        }

        $blockContent = $this->blockContentService->create($data);
        if (isset($imageData)) {
            $imagePath = $this->storeImage($imageData);
            $this->blockContentService->update($blockContent->id, [
                'image' => $imagePath,
            ]);
        }

        return $this->successResponse([]);
    }

    private function storeImage($image): string
    {
        $folderPath = config('constant.block_content_image_path');

        $filePath = storeBase64Image($folderPath, $image);
        if ($filePath) {
            return $filePath;
        } else {
            Log::debug('Issue save storeBase64Image at Api/SellerCenter/Setting/BlockController');
        }
        return '';
    }

    /**
     * Update BlockContent item.
     *
     * @param  Block  $block
     * @param  BlockContent  $blockContent
     * @param  Request  $request
     * @return JsonResponse
     */
    public function updateItem(Block $block, BlockContent $blockContent, Request $request): JsonResponse
    {
        $data = $request->all();

        $data['block_id'] = $block->id;

        if (isset($data['imageData'])) {
            $imageData = $data['imageData'];
            unset($data['imageData']);
        }
        if (isset($data['options'])) {
            $data['options'] = json_encode($data['options']);
        }

        $this->blockContentService->update($blockContent->id, $data);
        if (isset($imageData)) {
            $imagePath = $this->storeImage($imageData);
            $this->blockContentService->update($blockContent->id, [
                'image' => $imagePath,
            ]);
        }

        return $this->successResponse([]);
    }

    /**
     * Update BlockContent item.
     *
     * @param  Block  $block
     * @param  BlockContent  $blockContent
     * @param  Request  $request
     * @return JsonResponse
     */
    public function destroyItem(Block $block, BlockContent $blockContent, Request $request): JsonResponse
    {
        $status = Storage::delete($blockContent->image);
        $this->blockContentService->destroy($blockContent->id);
        return $this->successResponse([]);
    }

    /**
     * Update Sort of BlockContent item.
     *
     * @param  Block  $block
     * @param  Request  $request
     * @return JsonResponse
     */
    public function updateSortItem(Block $block, Request $request): JsonResponse
    {
        $items = $request->all();
        foreach ($items as $item) {
            $this->blockContentService->update($item['id'], ['sort' => $item['sort']]);
        }
        return $this->successResponse([]);
    }

    private function formatDatetime($block) {
        $data['startDate'] = Carbon::now()->startOfDay()->format('Y-m-d');
        $data['endDate']   = Carbon::now()->endOfDay()->format('Y-m-d');
        $data['startTime'] = Carbon::now()->startOfDay()->format('H:i:s');
        $data['endTime']   = Carbon::now()->endOfDay()->format('H:i:s');

        $block['startDate'] = !empty($block['start_date']) ? date('Y-m-d', $block['start_date']) : $data['startDate'];
        $block['endDate']   = !empty($block['end_date']) ? date('Y-m-d', $block['end_date']) : $data['endDate'];

        $block['startTime'] = !empty($block['start_date']) ? date('H:i:s', $block['start_date']) : $data['startTime'];
        $block['endTime']   = !empty($block['end_date']) ? date('H:i:s', $block['end_date']) : $data['endTime'];

        return $block;
    }

    private function getTypeDatetimeBlock($block) {
        if (!empty($block['start_date']) && !empty($block['end_date'])) {
            return Block::DATE_RANGE;
        }

        if (!empty($block['start_date'])) {
            return Block::START_DATE_ONLY;
        }

        if (!empty($block['end_date'])) {
            return Block::END_DATE_ONLY;
        }

        return Block::DATE_RANGE;
    }

    private function formatDataInputs($user, $data, $storeBlock = null) {
        if ($data['datetime_type'] === Block::START_DATE_ONLY || $data['datetime_type'] === Block::DATE_RANGE) {
            $data['start_date'] = Carbon::createFromFormat('Y-m-d H:i:s', $data['startDate'].' '.$data['startTime'])->timestamp;
        } else {
            $data['start_date'] = 0;
        }

        if ($data['datetime_type'] === Block::END_DATE_ONLY || $data['datetime_type'] === Block::DATE_RANGE) {
            $data['end_date'] = Carbon::createFromFormat('Y-m-d H:i:s', $data['endDate'].' '.$data['endTime'])->timestamp;
        } else {
            $data['end_date'] = 0;
        }

        $data['entity_id'] = $user->shop->id;
        $data['page_type'] = Page::TYPE_SHOP;

        if ($storeBlock) {
            $title[0] = $data['title'];
            $data['title'] = $title;
        }

        return $data;
    }

    public function togglePublish(Request $request, Block $block): \Illuminate\Http\JsonResponse
    {
        $data['is_enabled'] = intval($request->get('is_enabled'));
        $this->blockService->update($block->id, $data);
        return $this->successResponse([]);
    }

    private function formatDataInputYsgBlockTranslations($block) {
        $inputs = [];

        $item['ysg_block_id']       = $block->id;
        $item['language_id']        = Language::ENGLISH;
        $item['title']              = isset($block->title) ? $block->title : '';
        $item['description']        = isset($block->description) ? $block->description : '';
        $item['content']            = isset($block->content) ? $block->content : '';
        $item['created_at']         = date('Y-m-d H:i:s');
        $item['updated_at']         = date('Y-m-d H:i:s');

        array_push($inputs,$item);
        return $inputs;
    }
}