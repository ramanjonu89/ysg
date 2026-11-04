<?php
namespace App\Http\Requests\Api\Mobile\Users;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\BaseFormRequest;
use App\Models\Address;
use App\Models\Country;
use App\Support\PasswordPolicy;

class RegisterRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|unique:App\User,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:'. PasswordPolicy::regex(),
            ],
            'country_code' => 'required|string',
            'phone' => 'required|integer',
            'receive_special_offers' => 'nullable|integer|in:0,1',
            'referral_by' => 'nullable|string'
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
            'phone.integer'       => 'The phone field must be a number.',
            'password.regex'      => 'The password must contain at least one uppercase letter, one lowercase letter, and one number.',
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