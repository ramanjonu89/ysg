<?php

namespace App\Http\Requests\Api\Mobile\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BaseFormRequest;

class SendEmailCodeRequest extends BaseFormRequest
{
    public function authorize()
    {
        if (Auth::check()) {
            return true;
        }

        throw new HttpResponseException(
            response()->json([
                'status'  => 0,
                'message' => 'Sender does not exist!',
            ], 401)
        );
    }

    public function rules()
    {
        return [
            'emails'   => 'required|array|min:1',
        ];
    }

    public function messages()
    {
        return [
            'emails.required' => 'Emails not found!',
            'emails.array'    => 'Emails must be an array.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => 0,
                'message' => 'Validation errors',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
