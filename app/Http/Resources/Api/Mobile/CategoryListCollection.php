<?php

namespace App\Http\Resources\Api\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryListCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {

        $showLevel = $request->input('show_level', 1);
        if ($showLevel < 1) {
            $showLevel = 1;
        }

        $sub = [];
        if ($showLevel > 1) {
            foreach ($this->subcategories as $subcategory) {
                $subSub = [];
                if ($showLevel > 2) {
                    foreach ($subcategory->subsubcategories as $subsubcategory) {
                        $subSub[] = [
                            'id' => $subsubcategory->id,
                            'name' => $subsubcategory->name,
                            'slug' => $subsubcategory->slug,
                            'meta_title' => $subsubcategory->meta_title,
                            'meta_description' => $subsubcategory->meta_description,
                            'level' => 3,
                            'parent_id' => $subcategory->id,
                        ];
                    }
                }
                $sub[] = [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'slug' => $subcategory->slug,
                    'meta_title' => $subcategory->meta_title,
                    'meta_description' => $subcategory->meta_description,
                    'level' => 2,
                    'parent_id' => $this->id,
                    'children' => $subSub
                ];
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_age_restricted' => $this->is_age_restricted,
            'slug' => $this->slug,
            'banner' => url($this->banner),
            'icon' => url($this->icon),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'top' => $this->top,
            'level' => 1,
            'parent_id' => 0,
            'children' => $sub,
            'products_count' => $this->productsCountByCategory($request->route('seller'), $this->id)
        ];
    }

    private function productsCountByCategory($seller = null, $categoryId)
    {
        $query = \App\Models\Product::where('category_id', $categoryId)
                                    ->where('published', '1');

        if ($seller) {
            $userId = $seller->user->id;
            $query->where('user_id', $userId);
        }

        return $query->count();
    }
}