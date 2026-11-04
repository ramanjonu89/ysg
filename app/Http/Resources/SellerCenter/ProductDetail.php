<?php

namespace App\Http\Resources\SellerCenter;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetail extends JsonResource
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
            'category_id' => $this->isRelationActive('category') ? $this->category_id : null,
            'subcategory_id' => $this->isRelationActive('subcategory') ? $this->subcategory_id : null,
            'subsubcategory_id' => $this->isRelationActive('subsubcategory') ? $this->subsubcategory_id : null,
            'brand_id' => $this->brand_id,
            'unit' => $this->unit,
            'min_qty' => $this->min_qty,
            'max_qty' => $this->max_qty??0,
            'max_qty_start' => $this->formatDate(Product::MAX_QTY_START),
            'max_qty_end' => $this->formatDate(Product::MAX_QTY_END),
            'photos' => $this->photos?json_decode($this->photos):[],
            'thumbnail_img' => $this->thumbnail_img,
            'unit_price' => $this->unit_price,
            'purchase_price' => $this->purchase_price,
            'tax' => $this->tax,
            'tax_type' => $this->tax_type,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'current_stock' => $this->current_stock,
            'description' => $this->description,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_img' => $this->meta_img,
            'choice_options' => json_decode($this->choice_options),
            'published' => boolval($this->published),
            'stocks' => $this->stocks
        ];
    }

    private function formatDate($type)
    {
        $date = Carbon::now()->format('Y-m-d');

        if ($type === Product::MAX_QTY_START && !empty($this->max_qty_start)) {
            $date = Carbon::parse($this->max_qty_start)->format('Y-m-d');
        }

        if ($type === Product::MAX_QTY_END && !empty($this->max_qty_end)) {
            $date = $this->max_qty_end;
        }

        return $date;
    }

    private function isRelationActive($relation)
    {
        return $this->$relation && $this->$relation->status === 1;
    }
}
