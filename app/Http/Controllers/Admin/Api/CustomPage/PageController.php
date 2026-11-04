<?php
namespace App\Http\Controllers\Admin\Api\CustomPage;

use App\Http\Controllers\Api\Controller;
use App\Services\CustomPage\BlockService;
use App\Services\CustomPage\PageService;
use Illuminate\Http\Request;

class PageController extends Controller
{
    protected $service;
    protected $blockService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PageService $service, BlockService $blockService)
    {
        $this->service = $service;
        $this->blockService = $blockService;
    }


    public function addBlock(Request $request, $id)
    {
        $page = $this->service->get($id);
        $block = $this->blockService->getById($request->input('blockId'));

        if (is_null($page) || is_null($block)) {
            return response()->json(['message' => 'failed']);
        }

        $numberOfBlocks = $page->blocks()->count();
        $block->sort = $numberOfBlocks + 1;
//        $block->save();
        $page->blocks()->save($block);

        return response()->json(['message' => 'success']);
    }

    public function removeBlock(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $block = $this->blockService->getById($request->input('blockId'));
        if (is_null($block)) {
            return response()->json(['message' => 'failed']);
        }
        $block->ysg_page_id = null;
        $block->save();
        return response()->json(['message' => 'success']);
    }

    public function blockList($id)
    {
        $page = $this->service->get($id);
        if (is_null($page)) {
            return redirect()->route('admin.custom_page.page.index');
        }

        $template = 'admin.custom_page.page.blocks';

        return view($template, [
            'page' => $page,
            'service' => $this->service,
            'blocks' => $page->blocks??[],
            'allBlocks' => $this->blockService->getList(['keyword' => '']),
            'customCss' => 'active in',
        ]);
    }

    public function updateBlockIndex($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $listIndex = $request->listIndex;
        $status = $this->service->updateBlockIndex($id, $listIndex);
        return response()->json([
            'code' => 200,
            'status' => $status,
            'message' => 'success'
        ]);
    }
}

