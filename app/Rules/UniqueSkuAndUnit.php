<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueSkuAndUnit implements Rule
{
    protected $request;
    protected $message;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function passes($attribute, $value)
    {
        if (!$value) return true;

        if (strpos($attribute, 'stocks') === 0) {
            return $this->checkSku($attribute, $value);
        }

        if ($attribute === 'unit') {
            return $this->checkUnit($value);
        }

        return true;
    }

    protected function checkSku($attribute, $value)
    {
        $index     = explode('.', $attribute)[1];
        $stockId   = $this->request->input("stocks.$index.id");
        $productId = $this->request->input('id');

        $existsSku = DB::table('product_stocks')
            ->where('sku', $value)
            ->when($stockId, function($q) use ($stockId) {
                return $q->where('id', '!=', $stockId);
            })
            ->exists();

        if ($existsSku) {
            $this->message = "SKU \"$value\" already exists in the system.";
            return false;
        }

        $unit = $this->request->input('unit');
        if ($unit && strtolower(trim($unit)) === strtolower(trim($value))) {
            $this->message = "SKU \"$value\" must not be the same as the product unit.";
            return false;
        }

        $existsUnit = DB::table('products')
            ->where('unit', $value)
            ->when($productId, function($q) use ($productId) {
                return $q->where('id', '!=', $productId);
            })
            ->exists();

        if ($existsUnit) {
            $this->message = "SKU \"$value\" must not be the same as another product's unit.";
            return false;
        }

        return true;
    }

    protected function checkUnit($value)
    {
        $productId = $this->request->input('id');

        $existsSku = DB::table('product_stocks')
            ->where('sku', $value)
            ->exists();

        if ($existsSku) {
            $this->message = "Sku \"$value\" already exists.";
            return false;
        }

        $existsUnit = DB::table('products')
            ->where('unit', $value)
            ->when($productId, function($q) use ($productId) {
                return $q->where('id', '!=', $productId);
            })
            ->exists();

        if ($existsUnit) {
            $this->message = "Unit \"$value\" already exists in another product.";
            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->message ?? 'The given value is invalid.';
    }
}
