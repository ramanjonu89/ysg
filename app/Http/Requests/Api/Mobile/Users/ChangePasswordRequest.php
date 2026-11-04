<?php
namespace App\Http\Requests\Api\Mobile\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\BaseFormRequest;
use App\Support\PasswordPolicy;

class ChangePasswordRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:' . PasswordPolicy::regex(),
            ],
        ];
    }

    public function messages()
    {
        return [
            'new_password.required'   => 'The new password field is required.',
            'new_password.min'        => 'The new password must be at least :min characters.',
            'new_password.confirmed'  => 'The password confirmation does not match.',
            'new_password.regex'      => 'The password must contain at least one uppercase letter, one lowercase letter, and one number.',
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