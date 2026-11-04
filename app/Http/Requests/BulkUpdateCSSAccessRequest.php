<?php

namespace App\Http\Requests;

use App\Rules\ValidEmailDomains;

class BulkUpdateCSSAccessRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'seller_ids' => 'array|required',
            'domains_array' => ['array', new ValidEmailDomains()],
            'emails_array' => 'array',
            'emails_array.*' => 'email'
        ];
    }

    public function messages() {
        return [
            'email' => __('One or more emails are not valid.')
        ];
    }

    protected function prepareForValidation()
    {
        $domains = $this->input('domains');
        $emails = $this->input('emails');
        $convertedDomains = convertTextareaToArrayByLine($domains);
        $convertedEmails = convertTextareaToArrayByLine($emails);

        $this->merge([
            'domains_array' => $convertedDomains,
            'emails_array' => $convertedEmails,
        ]);
    }
}
