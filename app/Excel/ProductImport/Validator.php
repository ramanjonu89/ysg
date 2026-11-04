<?php
/**
 * Created by PhpStorm.
 * User: NamDG
 * Date: 28/04/2022
 * Time: 01:41
 */

namespace App\Excel\ProductImport;

use Illuminate\Support\Facades\Validator as LaravelValidator;
use Illuminate\Validation\Rule;

/**
 * Class Repo
 * @package App\Excel\ProductImport
 * @property
 */
class Validator
{
    protected $messages = [
        'in' => 'The :attribute must be one of the following types: :values',
    ];

    protected $ruleStatus;

    public function __construct(
        RuleStatus $ruleStatus
    )
    {
        $this->ruleStatus = $ruleStatus;
    }

    public function validateProduct($product, $variationsData = []) {
        $validator = LaravelValidator::make($product->all(),
            $this->productRules($product, $variationsData),
            $this->messages
        );
        if ($validator->fails()) {
            return $validator->errors();
        }

        return true;
    }

    public function productRules($product, $variationsData = []): array
    {
        $productId = $product['product_id']??0;
        $discountType = array_keys(Repo::DISCOUNT_TYPE);
        $hasVariation = (bool)count($variationsData);
        return [
            // Can also use callback validation rules
            'product_id' => 'nullable|integer|exists:App\Models\Product,id',
            'product_sku' => [
                'required',
                Rule::unique('products', 'unit')->where(function ($query) {
                    return $query->where('unit', 'not like', 'deleted%');
                })->ignore($productId),
                Rule::unique('product_stocks', 'sku'),
            ],
            'group_id' => 'required',
            'product_name' => 'required',
            'product_description_min_50_words' => 'required|min:50',
            'category' => 'required',
            'status' => [
                'required',
                $this->ruleStatus
            ],
            'unit_price' => [
                Rule::requiredIf(!$hasVariation),
                'nullable',
                'numeric',
                'min:0'
            ],
            'stock' => [
                Rule::requiredIf(!$hasVariation),
                'nullable',
                'integer',
                'min:0'
            ],
            'discount_type' => [
                'nullable',
                Rule::in($discountType),
            ],
            'discount_given' => [
                Rule::requiredIf($product['discount_type']),
                'nullable',
                'numeric',
                'min:0',
                new RuleDiscount($product, $variationsData, false)
            ],
            'minimum_purchase_quantity' => 'required|integer|min:1',
            'maximum_purchase_quantity' => 'nullable|integer|min:0',
            'maximum_purchase_quantity_start_date' => [
                Rule::requiredIf(!empty($product['maximum_purchase_quantity'])),
                'nullable',
                'date_format:d-m-Y'
            ],
            'maximum_purchase_quantity_end_date' => [
                Rule::requiredIf(!empty($product['maximum_purchase_quantity'])),
                'nullable',
                'date_format:d-m-Y',
                new RuleDateComparison($product['maximum_purchase_quantity_start_date'], $product['maximum_purchase_quantity'])
            ],
        ];
    }

    public function validateVariation($currentId, $variation, $allowAttributes) {
        $validator = LaravelValidator::make(
            $variation->all(),
            $this->variationRules($currentId, $variation, $allowAttributes),
            $this->messages
        );
        if ($validator->fails()) {
            return $validator->errors();
        }

        return true;
    }

    public function variationRules($currentId, $variation, $allowAttributes): array
    {
        return [
            // Can also use callback validation rules
            'product_sku' => [
                'required',
                Rule::unique('products', 'unit')->where(function ($query) {
                    return $query->where('unit', 'not like', 'deleted%');
                }),
                Rule::unique('product_stocks', 'sku')->ignore($currentId),
            ],
            'group_id' => 'required',
            'variation_1' => [
                'required',
                Rule::in($allowAttributes),
            ],
            'variation_option_1' => 'required',
            'variation_2' => [
                'nullable',
                Rule::in($allowAttributes),
            ],
            'variation_option_2' => Rule::requiredIf($variation['variation_2']),
            'status' => [
                'nullable',
                $this->ruleStatus
            ],
            'unit_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'discount_type' => [
                'nullable',
                Rule::in(['$', '%'])
            ],
            'discount_given' => [
                Rule::requiredIf($variation['discount_type']),
                'nullable',
                'numeric',
                'min:0',
                new RuleDiscount([], $variation, true)
            ],
        ];
    }
}