<?php

namespace App\Http\Requests\Api\SellerCenter\Product;

use App\Http\Requests\BaseFormRequest;

class ImportRequest extends BaseFormRequest
{
    protected $rules = [
        'uploaded_file' => ['required'],
    ];
}
