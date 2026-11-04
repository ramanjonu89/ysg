<?php
namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserCorporateAccess
 *
 * @property int $id
 * @property int $user_id
 * @property int $user_level
 * @property int $parent_id
 * @property string $domain
 * @property \Illuminate\Support\Carbon $expire_at
 * @property int $is_active
 * @property int $duration
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 */

class UserCorporateAccess extends Model
{
    // TODO need check move this model

    protected $table = 'user_corporate_access';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'user_level',
        'parent_id',
        'domain',
        'expire_at',
        'is_active',
        'duration'
    ];

    protected $dates = ['expire_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function rootUser()
    {
        return $this->belongsTo(User::class, 'parent_id', 'id');
    }
}

