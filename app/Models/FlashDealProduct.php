<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FlashDealProduct
 *
 * @property int $id
 * @property int $flash_deal_id
 * @property int $product_id
 * @property float|null $discount
 * @property string|null $discount_type
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashDealProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashDealProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashDealProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashDealProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashDealProduct whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashDealProduct whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashDealProduct whereFlashDealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashDealProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashDealProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlashDealProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FlashDealProduct extends Model
{
    const DISCOUNT_TYPE_AMOUNT = 'amount';
    const DISCOUNT_TYPE_PERCENT = 'percent';

    protected $fillable = [
        'flash_deal_id',
        'product_id',
        'discount',
        'discount_type',
    ];

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public static function mapDiscountType(): array
    {
        return [
            '$' => self::DISCOUNT_TYPE_AMOUNT,
            '%' => self::DISCOUNT_TYPE_PERCENT,
        ];
    }

    public function getDiscountPriceAttribute(): float
    {
        $price = $this->product->unit_price;
        if ($this->discount_type == self::DISCOUNT_TYPE_AMOUNT) {
            return $price - $this->discount;
        } else if ($this->discount_type == self::DISCOUNT_TYPE_PERCENT) {
            return ((100 - $this->discount) * $price) / 100;
        }
        return $price;
    }

    public function isDiscount(): bool {
        return (float)$this->discount !== 0.0;
    }
}
