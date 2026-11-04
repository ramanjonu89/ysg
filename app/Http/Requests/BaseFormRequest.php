<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BaseFormRequest extends FormRequest
{
    protected $rules = [];
    protected $messages = [];

    public function rules()
    {
        return $this->rules;
    }

    public function messages()
    {
        return $this->messages;
    }
    
    protected function failedValidation(Validator $validator)
    {
        flash( __($validator->getMessageBag()->first()))->error();
        return parent::failedValidation($validator);
    }
}