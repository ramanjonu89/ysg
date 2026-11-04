<?php
namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\CorporateAccessToken
 *
 * @property string $token
 * @property int $created_by
 * @property int $activated_by
 * @property \Illuminate\Support\Carbon $expire_at
 * @property \Illuminate\Support\Carbon $activated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 */
class CorporateAccessToken extends Model
{
// TODO remember change method deleted to active

    const LIMIT_USER_SHARE = 5;

    protected $primaryKey = 'token';

    public $incrementing = false;

    protected $fillable = [
        'token',
        'created_by',
        'activated_by',
        'expire_at',
        'activated_at',
    ];

    /**
     * @return HasOne
     */
    public function createBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function activeBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
