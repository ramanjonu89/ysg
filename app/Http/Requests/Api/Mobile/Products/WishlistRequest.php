<?php
namespace App\Http\Requests\Api\Mobile\Products;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\BaseFormRequest;

class WishlistRequest extends BaseFormRequest {
    public function rules()
    {
        return [
            'product_id' => [
                'required',
                'integer',
            ],
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'Product ID is required',
            'product_id.integer'  => 'Product ID must be an integer',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }   
}