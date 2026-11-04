<?php

namespace App\Excel\ProductImport;

use App\Models\Product;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class Repo
 * @package App\Excel\ProductImport
 * @property
 */
class RuleDiscount implements Rule
{
    protected $data;
    protected $variationsData;
    protected $discountType;
    protected $validateVariation;

    public function __construct($data = [], $variationsData = [], $validateVariation)
    {
        $this->data = $data;
        $this->variationsData = $variationsData;
        $this->validateVariation = $validateVariation;
        if (isset($data['discount_type']) && !$this->validateVariation) {
            $this->discountType = Repo::DISCOUNT_TYPE[$data['discount_type']]??null;
        } else if($this->validateVariation) {
            $this->discountType = Repo::DISCOUNT_TYPE[$variationsData['discount_type']]??null;
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($this->discountType === Product::DISCOUNT_TYPE_AMOUNT) {
            return $value <= $this->getMinPrice();
        } else if ($this->discountType === Product::DISCOUNT_TYPE_PERCENT) {
            return $value <= 100;
        }

        return true;
    }

    private function getMinPrice() {
        $minPrice = PHP_INT_MAX;
        if ($this->validateVariation) {
            $unitPrice = $this->variationsData['unit_price'];
            if (isset($unitPrice) && $unitPrice) {
                $minPrice = ($minPrice>(float)$unitPrice) ? (int)$unitPrice : $minPrice;
            }
        } else if(!count($this->variationsData)) {
            $minPrice = $this->data['unit_price'];
        }

        return $minPrice;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $message = 'Discount Type is not valid';

        if ($this->discountType === Product::DISCOUNT_TYPE_AMOUNT) {
            if ($this->validateVariation) {
                $price = $this->variationsData['unit_price'];
            } else {
                $price = $this->data['unit_price'];
            }
            $message = 'The :attribute must be equal or smaller than Unit Price: '.$price;
        } else if ($this->discountType === Product::DISCOUNT_TYPE_PERCENT) {
            $message = 'The :attribute must be equal or smaller than: 100';
        }

        return $message;
    }
}