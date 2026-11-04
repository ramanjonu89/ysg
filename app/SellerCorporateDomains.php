<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static firstOrCreate(array $array)
 * @method static where(string $string, $id)
 */
class SellerCorporateDomains extends Model
{
    public $timestamps = false;
    protected $fillable = ['id', 'seller_id', 'domain'];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id', 'id');
    }
}
