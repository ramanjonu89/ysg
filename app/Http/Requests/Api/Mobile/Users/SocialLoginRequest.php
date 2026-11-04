<?php
namespace App\Http\Requests\Api\Mobile\Users;

use App\Http\Requests\BaseFormRequest;

class SocialLoginRequest extends BaseFormRequest {
    protected $rules = [
        'provider' => ['required', 'string', 'in:google,facebook,apple'],
        'access_token' => ['required', 'string'],
    ];
} 