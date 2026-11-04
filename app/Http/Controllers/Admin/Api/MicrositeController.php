<?php
namespace App\Http\Controllers\Admin\Api;

use App\Services\CustomPage\BlockService;
use App\Services\MicrositeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MicrositeController extends Controller
{
    protected $service;

    public function __construct(MicrositeService $service)
    {
        $this->service = $service;
    }

    public function toggleActive(Request $request): JsonResponse
    {
        $result = $this->service->toggleActive($request->input('id'));
        if ($result) {
            return response()->json([
                'code' => 200,
                'message' => __('Toggle Active Successfully!')
            ]);
        }

        return response()->json([
            'code' => 500,
            'message' => __('We have a problem that cannot toggle active this microsite!')
        ]);

    }


    public function addBlock(Request $request, $id, BlockService $blockService)
    {
        $page = $this->service->get($id);
        $block = $blockService->getById($request->input('blockId'));

        if (is_null($page) || is_null($block)) {
            return response()->json(['message' => 'failed']);
        }

        $numberOfBlocks = $page->blocks()->count();
        $block->sort = $numberOfBlocks + 1;
        $page->blocks()->save($block);

        return response()->json(['message' => 'success']);
    }

    public function removeBlock(Request $request, $id, BlockService $blockService): \Illuminate\Http\JsonResponse
    {
        $block = $blockService->getById($request->input('blockId'));
        if (is_null($block)) {
            return response()->json(['message' => 'failed']);
        }
        $block->microsite_id = null;
        $block->save();
        return response()->json(['message' => 'success']);
    }

    public function updateBlockIndex($id, Request $request)
    {
        $listIndex = $request->input('listIndex');
        $this->service->updateBlockIndex($id, $listIndex);
        return response()->json(['message' => 'success']);
    }
}

