<?php

namespace App\Http\Resources\Mobile;

use App\Models\CustomPage\Block;
use App\Services\ProductService;
use App\Services\CustomPage\BlockService;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Block",
 *     type="object",
 *     title="Block",
 *     description="Block resource representation",
 *     @OA\Property(property="id", type="integer", example=1, description="Block ID"),
 *     @OA\Property(property="title", type="string", example="Featured Products", description="Block title"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Description of the block", description="Block description"),
 *     @OA\Property(property="content", type="string", nullable=true, example="Block content", description="Block content"),
 *     @OA\Property(property="image", type="string", nullable=true, example="blocks/image.jpg", description="Block image path"),
 *     @OA\Property(property="type", type="string", example="product_carousel", description="Block type"),
 *     @OA\Property(property="options", type="object", nullable=true, description="Block options as JSON object"),
 *     @OA\Property(
 *         property="block_contents",
 *         type="array",
 *         description="Block contents collection",
 *         @OA\Items(ref="#/components/schemas/BlockContent")
 *     ),
 * )
 */
class BlockResource extends JsonResource
{
    public function toArray($request)
    {
        $options = $this->options;
        $items = BlockContentResource::collection(
            $this->blockContents->filter(function ($blockContent) {
                return $blockContent->is_enabled && !$blockContent->isExpired();
            })
        );

        if ($this->type == Block::TYPE_PRODUCT_CAROUSEL) {
            unset($options->mobile_bg);
            unset($options->tablet_bg);
            unset($options->pc_bg);
            $options->background = $this->options->mobile_bg ? url($this->options->mobile_bg) : url($this->options->pc_bg);
            $items = [];
            foreach ($this->blockContents as $blockContent) {
                $productService = app(ProductService::class);
                $product = $productService->getFirstWithConditions(['id' => $blockContent->options->id]);
                if ($product) {
                    $items[] = new ProductResource($product);
                }
            }
        }

        if ($this->type == Block::TYPE_SHOP_LIST) {

            $items = [];
            $blockService = app(BlockService::class);
            foreach($blockService->getListSeller($this) as $seller) {
                $items[] = [
                    'id' => $seller->id,
                    'name' => $seller->user->name,
                    'image' => $seller->user->shop->logo ? url($seller->user->shop->logo) : null,
                    'url' => route('shop.visit', $seller->user->shop->slug),
                    'options' => analyzeLinkRoute(route('shop.visit', $seller->user->shop->slug)),
                ];
            }
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'image' => $this->image ? url($this->image) : null,
            'type' => $this->type,
            'options' => analyzeLinkRoute($options),
            'block_contents' => $items,
        ];
    }
}