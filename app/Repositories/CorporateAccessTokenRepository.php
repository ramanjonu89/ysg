<?php
namespace App\Repositories;

use App\BusinessSetting;
use App\Models\CorporateAccessToken;
use Illuminate\Support\Str;

class CorporateAccessTokenRepository extends AbstractRepo
{

    public function getModel(): string
    {
        return CorporateAccessToken::class;
    }

    /**
     * @param  array  $params
     * @return mixed
     */
    public function getList(array $params)
    {
        $createdBy = $params['created_by'] ?? null;
        $activatedBy = $params['activated_by'] ?? null;
        $activatedAt = $params['activated_at'] ?? null;

        $query = $this->model::query();

        if ($createdBy) {
            $query->where('created_by', $createdBy);
        }

        if ($activatedBy) {
            $query->where('activated_by', $activatedBy);
        }

        if ($activatedAt) {
            $query->where('activated_at', $activatedAt);
        }

        return $query->get();
    }

    /**
     * @param  string  $domain
     * @param  string  $recipientEmail
     * @param  int  $referrer_id
     * @return string
     */
    public function genToken(string $domain, string $recipientEmail, int $referrer_id): string
    {
        $baseToken = hash('sha256', Str::random(80));
        return base64_encode(json_encode([
            'domain' => $domain,
            'email' => $recipientEmail,
            'type' => BusinessSetting::REFERRAL_TYPE_CORPORATE,
            'token' => $baseToken,
            'referrer' => $referrer_id
        ]));
    }

    public function getByToken(string $ref): ?CorporateAccessToken
    {
        return $this->model::where('token', $ref)->first();
    }
}
