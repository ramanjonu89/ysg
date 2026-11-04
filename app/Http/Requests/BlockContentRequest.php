<?php

namespace App\Http\Requests;

class BlockContentRequest extends BaseFormRequest
{
  public function rules(): array
  {
    $regex = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,})([\/\w %&?\[\]=.\'"()-]*)*\/?$/i';

    return [
      'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
      'options.link' => 'nullable|regex:' . $regex,
    ];
  }

  public function messages(): array
  {
    return [
      'options.link.regex' => 'Error Invalid url',
    ];
  }

  protected function prepareForValidation()
  {
    $this->merge(
      [
        'options' => array_merge(
          $this->options,
          [
            'link' => isset($this->options['link']) ? urldecode($this->options['link']) : null
          ]
        )
      ]
    );
  }
}