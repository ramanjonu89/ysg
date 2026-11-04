<?php

namespace App\Http\Requests\Api\SellerCenter\Product;

use App\Http\Requests\BaseFormRequest;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Rules\UniqueSkuAndUnit;

class UpdateRequest extends BaseFormRequest
{
    
    public function rules()
    {
        return [
            'unit' => [
                'sometimes',
                'max:128',
                new UniqueSkuAndUnit($this),
            ],
            'category_id' => 'integer|exists:categories,id',
            'subcategory_id' => [
                'integer',
                function ($id) {
                    if (SubCategory::find($id)) {
                        $this->merge(['subcategory_id' => 0]);
                    }
                },
            ],
            'subsubcategory_id' => [
                'integer',
                function ($id) {
                    if (SubSubCategory::find($id)) {
                        $this->merge(['subsubcategory_id' => 0]);
                    }
                },
            ],
            'choice_options' => [
                'sometimes',
                $this->choiceOptionsRules(),
            ],
            'stocks.*.price' => 'numeric|min:0|required_with:stocks',
            'stocks.*.sku' => [
                'max:128',
                'distinct',
                new UniqueSkuAndUnit($this),
            ],
        ];
    }

    private function choiceOptionsRules()
    {
        return function ($attribute, $value, $fail) {
            $choices = $this->input('choice_options');
            $uniqueValues = collect($choices)->pluck('attribute.value')->unique();
            if ($uniqueValues->count() < count($choices)) {
                $fail('Variant Attribute was duplicated.');
            }
        };
    }

    public function messages()
    {
        return [
            'category_id.exists' => 'Error Invalid category',
            'stocks.*.sku.distinct' => 'The SKU values in stocks must be unique.',
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'added_by' => 'seller',
            'published' => $this->published == 'true',
            'category_id' => isset($this->category_id) ? intval($this->category_id) : 0,
            'subcategory_id' => isset($this->subcategory_id) ? intval($this->subcategory_id) : 0,
            'subsubcategory_id' => isset($this->subsubcategory_id) ? intval($this->subsubcategory_id) : 0,
        ]);

        if ($this->has('choice_options')) {
            $this->merge([
                'choice_options' => json_decode($this->input('choice_options')),
            ]);
        }

        if ($this->has('stocks')) {
            $this->merge([
                'stocks' => json_decode($this->input('stocks'), true),
            ]);
        }


        if ($this->subcategory_id) {
            $subCate = Subcategory::find($this->subcategory_id);
            if (!isset($subCate) || $subCate->category_id !== $this->category_id) {
                $this->merge(['subcategory_id' => 0]);
                $this->merge(['subsubcategory_id' => 0]);
            }
        } else {
            $this->merge(['subsubcategory_id' => 0]);
        }

        if ($this->subcategory_id && $this->subsubcategory_id) {
            $subsubCat = SubSubCategory::find($this->subsubcategory_id);
            if (!isset($subsubCat) || $subsubCat->subCategory->category_id !== $this->category_id || $subsubCat->sub_category_id !== $this->subcategory_id) {
                $this->merge(['subsubcategory_id' => 0]);
            }
        }
    }
}
