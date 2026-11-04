<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\Country;
use App\Models\Address;
use App\Support\PasswordPolicy;
use Illuminate\Validation\Rule;

class UserUpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     * The numeric value (65, 81) is used only for validation
     */
    protected function prepareForValidation()
    {
        if ($this->has('country_code')) {
            $this->merge([
                'country_code_numeric' => ltrim($this->country_code, '+'),
            ]);
        }

        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/\D+/', '', $this->phone),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'name'  => 'required|string|max:20',
            'phone' => 'required',

            'country_code' => 'required',

            'country_code_numeric' => [
                'required',
                Rule::exists('countries', 'country_code')->where(function ($query) {
                    $query->where('status', Country::STATUS_ACTIVE);
                }),
            ],

            'new_password' => [
                'nullable',
                'string',
                'min:8',
                'required_with:confirm_password',
                'same:confirm_password',
                'regex:'. PasswordPolicy::regex(),
            ],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages()
    {
        return [
            'name.required' => __('The name field is required.'),
            'phone.required' => __('The phone field is required.'),
            'country_code.required' => __('The country code field is required.'),
            'country_code_numeric.exists' => __('Invalid country code.'),
            'new_password.regex' => __('The password must contain at least one uppercase letter, one lowercase letter, and one number.'),
            'new_password.min' => __('The new password must be at least 8 characters.'),
            'new_password.same' => __('The password confirmation does not match.'),
        ];
    }

    /**
     * After validation hook (validate phone theo country)
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $phone       = $this->phone;
            $countryCode = $this->country_code_numeric;

            if (!$phone || !$countryCode) {
                return;
            }

            // Singapore (+65)
            if ($countryCode == Address::COUNTRY_CODE_SG) {
                $cleanPhone = preg_replace('/\s+/', '', $phone);

                if (!preg_match('/^[689]\d{7}$/', $cleanPhone)) {
                    $validator->errors()->add(
                        'phone',
                        __('For Singapore (+65), the phone number must be 8 digits and start with 6, 8, or 9.')
                    );
                }
            } else {
                if (!preg_match('/^[0-9]{4,15}$/', $phone)) {
                    $validator->errors()->add(
                        'phone',
                        __('The phone number must be between 4 and 15 digits.')
                    );
                }
            }
        });
    }
}
