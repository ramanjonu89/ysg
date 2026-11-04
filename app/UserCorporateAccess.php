<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCorporateAccess extends Model
{
    protected $table = 'user_corporate_access';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'domain',
        'expire_at',
        'is_active'
    ];
    protected $dates = ['expire_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

