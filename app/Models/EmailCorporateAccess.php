<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EmailCorporateAccess
 *
 * @property int $id
 * @property int $seller_id
 * @property string $email
 * @mixin \Eloquent
 */
class EmailCorporateAccess extends Model
{
    protected $table = 'email_corporate_access';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'seller_id',
        'email',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id', 'id');
    }
}
