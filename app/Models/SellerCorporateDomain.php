<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SellerCorporateDomain
 *
 * @property int $id
 * @property int $seller_id
 * @property string $domain
 * @mixin \Eloquent
 */
class SellerCorporateDomain extends Model
{
    protected $fillable = [
        'id',
        'seller_id',
        'domain',
    ];

    public $timestamps = false;

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id', 'id');
    }
}
