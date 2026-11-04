<?php
namespace App\Http\Requests\Api\Mobile\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\BaseFormRequest;
use App\Support\PasswordPolicy;

class ForgotPasswordResetRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'email'    => 'required|email',
            'otp'      => 'required|string|size:6',
            'password' => [
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
            'email.required'      => 'The email field is required.',
            'email.email'         => 'Please provide a valid email address.',
            'otp.required'        => 'The OTP field is required.',
            'otp.size'            => 'The OTP must be exactly 6 characters.',
            'password.required'   => 'The password field is required.',
            'password.min'        => 'The password must be at least :min characters.',
            'password.confirmed'  => 'The password confirmation does not match.',
            'password.regex'      => 'The password must contain at least one uppercase letter, one lowercase letter, and one number.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'error'   => true,
                'message' => 'Validation errors',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
