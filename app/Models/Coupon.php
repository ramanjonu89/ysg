<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Coupon
 *
 * @property int $id
 * @property string $type
 * @property string $owner
 * @property string $code
 * @property bool $free_shipping
 * @property int $seller_id
 * @property string $details
 * @property bool $is_active
 * @property float $discount
 * @property string $discount_type
 * @property string $discount_label
 * @property int $start_date
 * @property int $end_date
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Coupon whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Coupon extends Model
{
    protected $withCount = ['usages'];
    protected $guarded = ['id'];
    
    const TYPE_PERCENTAGE    = 'percent';
    const TYPE_AMOUNT        = 'amount';
    const TYPE_CART_BASE     = 'cart_base';
    const TYPE_PRODUCT_BASE  = 'product_base';
    const TYPE_CATEGORY_BASE = 'category_base';
    const TYPE_CASH_BASE     = 'cash_base';

    const OWNER_SELLER = 'seller';
    const OWNER_YSG = 'ysg';

    const STATUS_ACTIVE = 1; 
    const STATUS_INACTIVE = 0;

    const TYPE_ONLY_ONCE = 1;
    const IS_PUBLISHED = 1;

    public function usages()
    {
        return $this->belongsToMany('App\User', 'coupon_usages');
    }

    public function getNumberOfUsesAttribute()
    {
        return $this->belongsToMany(Order::class, 'coupon_usages')->where('payment_status','paid')->count();
    }

    public function isExpired()
    {
        $now = strtotime('now');
        return !($now <= $this->end_date);
    }
    
    public function inValid()
    {
        $now = strtotime('now');
        return !($this->start_date <= $now);
    } 

    public function shop() {
        return $this->belongsTo('App\Models\Shop', 'seller_id','id' );
    }

    public function getDiscountAmount($price)
    {
        $total = 0;

        if ($this->discount_type == self::TYPE_PERCENTAGE) {
            $total = ($price * $this->discount) / 100;
        } elseif ($this->discount_type == self::TYPE_AMOUNT && $this->discount !== null) {
            $total = $this->discount;
        }

        if ($this->max_discount !== null && $total > $this->max_discount) {
            $total = $this->max_discount;
        }

        return $total > $price ? $price : $total;
    }

    public function getDiscountLabelAttribute()
    {
        if ($this->type === self::TYPE_CASH_BASE) {
            return $this->discount * 100;
        } else if($this->discount_type == self::TYPE_PERCENTAGE) {
            return $this->discount . '%';
        } else {
            return '$' . $this->discount;
        }
    }

    public function getEndDateFormattedAttribute()
    {
        return date('d-m-Y', $this->end_date);
    }

    public function getMinBuyAttribute()
    {
        $details = json_decode($this->details);

        return $details->min_buy;
    }

    public function getTotalCoupon()
    {
        $coupon_det = json_decode($this->details);
        $totalcoupon = 0;
        if (isset($coupon_det->max_coupon)) {
            $totalcoupon = $coupon_det->max_coupon;
        } else {
            $totalcoupon = '-';
        }

        return $totalcoupon;
    }

    public function getShopIdsAttribute() 
    {
        $details = json_decode($this->details);
        $shopIds = isset($details->sellerIds) ? $details->sellerIds : [];

        return $shopIds;
    }

    public function getProductIdsAttribute()
    {
        $productIds = [];
        $details = json_decode($this->details);

        if (isset($details->products)) {
            $productIds = $details->products;
        } elseif (isset($details->info)) {
            foreach($details->info as $item) {
                $productIds[] = $item->product_id;
            }
        }

        return $productIds;
    }

    public function canUse(): bool
    {
        if (null === $this->getTotalCoupon()) {
            return true;
        }

        if ($this->getTotalCoupon() > $this->usages_count) {
            return true;
        }

        return false;
    }

    public function isOnlyOnce() : bool
    {
        return $this->only_once === Coupon::TYPE_ONLY_ONCE;
    }

    public function getUsedUserIds() : array 
    {
        return $this->usages->pluck('id')->toArray();
    }

    public function canUseOnlyOnce(): bool
    {
        $countCouponPaid = Order::where([
            'user_id'           => auth()->user()->id,
            'payment_status'    => Order::PAYMENT_STATUS_PAID,
            'coupon_code'       => $this->code
        ])->count();

        if (!$this->isOnlyOnce()) {
            return true;
        }

        if ($this->isOnlyOnce() && ($countCouponPaid === 0 || !in_array(auth()->user()->id, $this->getUsedUserIds()))){
            return true;
        }

        return false;
    }

    public function isForShop() : bool 
    {
        return 0 !== $this->seller_id || count($this->shopIds) > 0;
        // return !count($this->shopIds);
    }

    public function isYsgCoupon() : bool
    {
        return self::OWNER_YSG === $this->owner;    
    }

    public function getMaxDiscountAttribute()
    {
        $details = json_decode($this->details);
        $max_discount = isset($details->max_discount) ? $details->max_discount : '';

        return $max_discount;
    }
}
