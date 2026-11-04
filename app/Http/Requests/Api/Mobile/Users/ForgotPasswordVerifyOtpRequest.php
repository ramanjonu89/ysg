<?php
namespace App\Http\Requests\Api\Mobile\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\BaseFormRequest;

class ForgotPasswordVerifyOtpRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'The email field is required.',
            'email.email'    => 'Please provide a valid email address.',
            'otp.required'   => 'The OTP field is required.',
            'otp.size'       => 'The OTP must be exactly 6 characters.',
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
