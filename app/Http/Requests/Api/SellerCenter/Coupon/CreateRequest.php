<?php

namespace App\Http\Requests\Api\SellerCenter\Coupon;

use App\Http\Requests\BaseFormRequest;

class CreateRequest extends BaseFormRequest
{
    protected $rules = [
        'title' => ['required', 'max:255'],
        'code' => ['required', 'max:20', 'unique:coupons', 'alpha_num'],
        'type' => ['required'],
        'discount' => ['required'],
        'discount_type' => ['required'],
        'start_date' => ['required', 'date'],
        'end_date' => ['required', 'date', 'after_or_equal:start_date'],
    ];

    public function messages()
    {
        return [
            'code.unique' => 'This coupon is already on system!',
        ];
    }
}
