<?php
namespace App\Http\Requests\Api\Mobile\Users;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Address;
use App\Models\Country;

class AddressRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'title'       => 'required|string|max:255',
            'address'     => 'required|string|max:255',
            'country_id'  => 'required|integer',
            'city_id'     => 'required|integer',
            'postal_code' => [
                'required',
                'regex:/^\d{6}$/',
            ],
            'country_code' => 'required|string',
            'phone'       => 'required|integer',
            'set_defalt'  => 'sometimes|boolean',
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
            'title.required'       => 'The title field is required.',
            'country_code.required' => 'The country code field is required.',
            'phone.required'       => 'The phone number field is required.',
            'address.required'     => 'The address field is required.',
            'city_id.required'     => 'The city field is required.',
            'postal_code.required' => 'The postal code field is required.',
            'postal_code.regex'    => 'The postal code must be 6 digits.',
            'country_id.required'  => 'The country field is required.',
            'country_id.integer'   => 'The country field must be an integer.',
            'city_id.integer'      => 'The city field must be an integer.',
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
