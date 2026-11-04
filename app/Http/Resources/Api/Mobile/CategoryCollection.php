<?php

namespace App\Http\Resources\Api\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $cateLevel = $request->input('cate_level', 0);
        if ($cateLevel == 0 || $cateLevel > 3) {
            return [];
        }

        if ($cateLevel == 2) {
            $parentId = $this->category_id;
        } elseif ($cateLevel == 3) {
            $parentId = $this->subcategory_id;
        } else {
            $parentId = 0;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_age_restricted' => $this->is_age_restricted,
            'slug' => $this->slug,
            'banner' => $this->banner,
            'icon' => $this->icon,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'image' => $this->image,
            'top' => $this->top,
            'level' => $cateLevel,
            'parent_id' => $parentId,
        ];
    }
}