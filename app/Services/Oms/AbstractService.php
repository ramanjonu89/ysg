<?php

namespace App\Services\Oms;

use Ixudra\Curl\Facades\Curl;

abstract class AbstractService {
    /**
     * @var string
     */
    protected $accessToken;

    public function __construct() {
    }

    public function setAccessToken($token)
    {
        $this->accessToken = $token;
    }

    public function getAccessToken($companyId)
    {
        $response = Curl::to(config('api.oms') . 'v1/auth')
            ->withData(['CompanyId' => $companyId])
            ->asJson()
            ->post();

        return $response->accessToken;
    }
}