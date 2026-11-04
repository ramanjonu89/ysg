<?php
namespace App\Http\Requests\Api\Mobile\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\BaseFormRequest;

class ForgotPasswordSendOtpRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email:rfc,dns|exists:users,email',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'The email field is required.',
            'email.email'    => 'Please provide a valid email address.',
            'email.exists'   => 'No account found with this email address.',
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
