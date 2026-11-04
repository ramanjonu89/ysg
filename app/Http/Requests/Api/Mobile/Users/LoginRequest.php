<?php
namespace App\Http\Requests\Api\Mobile\Users;

use App\Http\Requests\BaseFormRequest;

class LoginRequest extends BaseFormRequest {
    protected $rules = [
        'email' => ['required', 'string', 'email', 'max:255'],
        'password' => ['required', 'string', 'min:4']
    ];
} 