<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'fcm_token',
        'device_type',
        'device_model',
        'os_version',
        'app_version',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
