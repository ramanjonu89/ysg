<?php

namespace App\Http\Requests\Api\Mobile\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\BaseFormRequest;
use App\Models\Address;
use App\Models\Country;

class UserRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'name' => 'nullable|string|max:255',
            'country_code' => 'nullable|string|required_with:phone',
            'phone' => 'nullable|integer|required_with:country_code',
            'avatar_original' => 'nullable|string',
        ];
    }

    /**
     * Custom validation after basic rules
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $countryCode = $this->country_code;
            $phone   = $this->phone;

            if (!$countryCode || !$phone) {
                return;
            }

            if ($countryCode) {
                $normalizedCountryCode = ltrim($countryCode, '+');
                $exists = Country::where('country_code', $normalizedCountryCode)
                                ->where('status', Country::STATUS_ACTIVE)->exists();

                if (!$exists) {
                    $validator->errors()->add(
                        'country_code',
                        'The selected country code is invalid.'
                    );

                    return;
                }
            }

            // Singapore (+65)
            if ($countryCode == Address::COUNTRY_CODE_SG) {
                if (!preg_match('/^[689]\d{7}$/', $phone)) {
                    $validator->errors()->add(
                        'phone',
                        'For Singapore (+65), the phone number must be 8 digits and start with 6, 8, or 9.'
                    );
                }
            } else {
                // Other countries
                if (!preg_match('/^[0-9]{4,15}$/', $phone)) {
                    $validator->errors()->add(
                        'phone',
                        'The phone number must contain 4 to 15 digits for this country.'
                    );
                }
            }
        });
    }

    public function messages()
    {
        return [
            'country_code.required_with' => 'Country code is required when phone number is provided.',
            'phone.required_with' => 'Phone number is required when country code is provided.',
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