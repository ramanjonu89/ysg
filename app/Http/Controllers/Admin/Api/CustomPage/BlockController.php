<?php
namespace App\Http\Controllers\Admin\Api\CustomPage;

use App\Http\Controllers\Api\Controller;
use App\Models\CustomPage\Block;
use App\Services\CustomPage\BlockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    protected $service;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BlockService $service)
    {
        $this->service = $service;
    }

    /**
     * @param  Block  $block
     * @param  Request  $request
     * @return JsonResponse
     */
    public function updateContentIndex(Block $block, Request $request): \Illuminate\Http\JsonResponse
    {
        $listIndex = $request->listIndex;
        $this->service->updateContentIndex($block->id, $listIndex);
        return response()->json(['message' => 'success']);
    }
}

