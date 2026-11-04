<?php

namespace App\Http\Requests\Api\Mobile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ContactFormRequest extends BaseFormRequest
{
    protected $rules = [
        'name'     => 'required|string|max:255',
        'email'    => [
            'required',
            'email:rfc,filter',
        ],
        'message'  => 'required|string',
    ];

    protected $messages = [
        'name.required'     => 'The name field is required.',
        'email.required'    => 'The email field is required.',
        'email.email'       => 'The email must be a valid email address.',
        'message.required'  => 'The message field is required.',
    ];

    protected function failedValidation(Validator $validator)
    {
        // Debug: Log the request data
        Log::info('ContactFormRequest validation failed', [
            'request_data' => $this->all(),
            'validation_errors' => $validator->errors()->toArray()
        ]);
        
        throw new HttpResponseException(
            response()->json([
                'status'  => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
