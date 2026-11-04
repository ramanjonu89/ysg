<?php
namespace App\Http\Controllers\Admin\Api;

use App\Services\CustomPage\BlockService;
use App\Services\ShopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    protected $service;

    public function __construct(ShopService $service)
    {
        $this->service = $service;
    }

    public function addBlock(Request $request, $id, BlockService $blockService): JsonResponse
    {
        $shop = $this->service->get($id);
        $block = $blockService->getById($request->input('blockId'));

        if (is_null($shop) || is_null($block)) {
            return response()->json(['message' => 'failed']);
        }

        $numberOfBlocks = $shop->blocks()->count();
        $newSort = $numberOfBlocks + 1;
        $shop->blocks()->attach($block, ['sort' => $newSort]);

        return response()->json(['message' => 'success']);
    }

    /**
     * @throws \Exception
     */
    public function removeBlock(Request $request, $id, BlockService $blockService): JsonResponse
    {
        $block = $blockService->getById($request->input('blockId'));
        if (is_null($block)) {
            return response()->json(['message' => 'Block does not exits']);
        }
        $shop = $this->service->get($id);
        if (is_null($shop)) {
            return response()->json(['message' => 'Shop does not exits']);
        }
        $shop->blocks()->detach($block);

        return response()->json(['message' => 'success']);
    }

    public function updateBlockIndex($id, Request $request): JsonResponse
    {
        $listIndex = $request->input('listIndex');
        // $this->service->updateBlockIndex($id, $listIndex);
        $status = $this->service->updateBlockIndex($id, $listIndex);
        return response()->json([
            'code' => 200,
            'status' => $status,
            'message' => 'success'
        ]);
        // return response()->json(['message' => 'success']);
    }
}

