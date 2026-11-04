<?php

namespace App\Http\Resources\Api\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'logo' => url($this->logo),
            'top' => $this->top,
            'slug' => $this->slug,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'products_count' => $this->productsCountByBrand($request->route('seller'), $this->id),
        ];
    }

    private function productsCountByBrand($seller = null, $brandId)
    {
        $query = \App\Models\Product::where('brand_id', $brandId)
                                    ->where('published', '1');

        if ($seller) {
            $userId = $seller->user->id;
            $query->where('user_id', $userId);
        }

        return $query->count();
    }
}