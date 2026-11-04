<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserOtp extends Model
{
    // use SoftDeletes;
    const SEND_LIMIT_EXCEEDED = 3;

    protected $table = 'user_otps';

    protected $fillable = [
        'id',
        'email',
        'username',
        'user_type',
        'original_otp',
        'expired',
        'count',
        'user_details'
    ];

    public function isExpired()
    {
        $now = strtotime(Carbon::now()->format('Y-m-d H:i:s'));
        return $now <= $this->expired;
    }

    public function isSellers() {
        return $this->user_type === User::TYPE_SELLER;
    }

}
