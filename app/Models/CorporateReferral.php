<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CorporateReferral
 *
 * @property int $id
 * @property string $domain
 * @property int $share_limit
 * @property int $share_count
 * @property int $expire_after
 * @property string $expire_time_unit
 * @property string $link
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 */

class CorporateReferral extends Model
{
    protected $table = 'corporate_referral';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'domain',
        'share_limit',
        'share_count',
        'expire_after',
        'expire_time_unit',
        'link',
    ];
}

