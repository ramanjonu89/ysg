<?php
namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\MicrositeUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $microsite_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at

 * @mixin \Eloquent
 */

class MicrositeUser extends Model
{

    protected $fillable = ['user_id', 'microsite_id'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function microsite(): BelongsTo
    {
        return $this->belongsTo(Microsite::class);
    }
}
