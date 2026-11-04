<?php

namespace App\Http\Requests\Microsite;

use App\Http\Requests\BaseFormRequest;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'seller_id' => 'required|unique:microsites',
        ];
    }

    public function messages(): array
    {
        return [
            'seller_id.unique' => 'This seller already has microsite configuration',
        ];
    }
}
