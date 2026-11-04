<?php
namespace App\Models;

use App\PickupPoint;
use App\RefundRequest;
use App\User;
use App\Services\CartService;
use App\Models\History;
use App\Models\Traits\Historiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $guest_id
 * @property string|null $shipping_address
 * @property string|null $payment_type
 * @property string|null $payment_status
 * @property string|null $payment_details
 * @property float|null $grand_total
 * @property float $coupon_discount
 * @property string $coupon_code
 * @property string $coupon_value
 * @property float $total_shipping_cost
 * @property string|null $code
 * @property int $date
 * @property int $viewed
 * @property int $delivery_viewed
 * @property int $payment_status_viewed
 * @property int $commission_calculated
 * @property PaymentTransaction|null $paymentTransaction
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCouponDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereGrandTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereGuestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePaymentDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereShippingAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereViewed($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    use Historiable;
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_UNPAID = 'unpaid';

    const PAYMENT_TYPE_NONE = 'none';
    const PAYMENT_TYPE_STRIPE = 'stripe';
    const PAYMENT_TYPE_OCTIFI = 'octifi';
    const PAYMENT_TYPE_CYBERSOURCE = 'cybersource';
    const PAYMENT_TYPE_NETS = 'nets';
    const PAYMENT_TYPE_CASH_ON_DELIVERY = 'cash_on_delivery';

    const SHIPPING_STATUS_PENDING = 'pending';
    const SHIPPING_STATUS_ON_REVIEW = 'on_review';
    const SHIPPING_STATUS_ON_DELIVERY = 'on_delivery';
    const SHIPPING_STATUS_PARTIAL = 'partial';
    const SHIPPING_STATUS_DELIVERED = 'delivered';
    const SHIPPING_STATUS_CANCELED = 'cancelled';

    const ORDER_TYPE_DELIVERY = 'delivery';
    const ORDER_TYPE_SELF_COLLECTION = 'self_collection';

    public static function getShippingStatusesHomeDelivery()
    {
        return [
            static::SHIPPING_STATUS_PENDING => __('Pending'),
            static::SHIPPING_STATUS_ON_REVIEW => __('Ready to Ship'),
            static::SHIPPING_STATUS_ON_DELIVERY => __('In Transit'),
            static::SHIPPING_STATUS_PARTIAL => __('Partially Delivered'),
            static::SHIPPING_STATUS_DELIVERED => __('Delivered'),
            static::SHIPPING_STATUS_CANCELED => __('Cancelled')
        ];
    }

    public static function getShippingStatusesSelfCollection()
    {
        return [
            static::SHIPPING_STATUS_PENDING => __('Pending'),
            static::SHIPPING_STATUS_ON_REVIEW => __('Preparing Order'),
            static::SHIPPING_STATUS_ON_DELIVERY => __('Ready for Self Collection'),
            static::SHIPPING_STATUS_DELIVERED => __('Collected'),
            static::SHIPPING_STATUS_CANCELED => __('Cancelled'),
        ];
    }

    protected $guarded = [];

    protected $fillable = [
        'id',
        'user_id',
        'guest_id',
        'shipping_address',
        'payment_type',
        'payment_status',
        'payment_details',
        'grand_total',
        'coupon_discount',
        'coupon_code',
        'coupon_value',
        'total_shipping_cost',
        'code',
        'date',
        'viewed',
        'delivery_viewed',
        'payment_status_viewed',
        'commission_calculated',
        'restock_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_usages');
    }

    public function refund_requests()
    {
        return $this->hasMany(RefundRequest::class);
    }

    public function pickup_point()
    {
        return $this->belongsTo(PickupPoint::class);
    }

    public function paymentTransaction()
    {
        return $this->hasOne(PaymentTransaction::class);
    }

    public function splitOrders()
    {
        return $this->hasMany(SplitOrder::class);
    }

    public function getPaymentReferentIdAttribute()
    {
        if ($this->payment_status == self::PAYMENT_STATUS_PAID) {
            if ($details = json_decode($this->payment_details)) {
                if ($this->payment_type == self::PAYMENT_TYPE_STRIPE) {
                    return $details->id;
                } else if ($this->payment_type == self::PAYMENT_TYPE_OCTIFI) {
                    return $details->create->data->order_payment_details->data->txn_number ?? '';
                } else if ($this->payment_type == self::PAYMENT_TYPE_CYBERSOURCE) {
                    return $details->transaction_id ?? '';
                }
            }
        }
        return '';
    }

    // public function getDeliveryStatusAttribute()
    // {
    //     $isCancelled = true;

    //     if (count($orderDetails = $this->orderDetails) > 0) {
    //         foreach($orderDetails as $item) {
    //             if ($item->delivery_status !== self::SHIPPING_STATUS_CANCELED) {
    //                 $isCancelled = false;
    //             }
    //             if ($item->delivery_status === self::SHIPPING_STATUS_PENDING) {
    //                 return self::SHIPPING_STATUS_PENDING;
    //             }
    //             if ($item->delivery_status === self::SHIPPING_STATUS_ON_REVIEW) {
    //                 return self::SHIPPING_STATUS_ON_REVIEW;
    //             }
    //             if ($item->delivery_status === self::SHIPPING_STATUS_ON_DELIVERY) {
    //                 return self::SHIPPING_STATUS_ON_DELIVERY;
    //             }
    //         }
    //         if ($isCancelled) {
    //             return self::SHIPPING_STATUS_CANCELED;
    //         }
    //         return self::SHIPPING_STATUS_DELIVERED;
    //     }
    //     return self::SHIPPING_STATUS_CANCELED;
    // }

    public function getDeliveryStatusAttribute()
    {
        $orderDetails = $this->orderDetails;

        if ($orderDetails->isEmpty()) {
            return $this->mapDeliveryStatusText(self::SHIPPING_STATUS_CANCELED, null);
        }

        $orderType = $this->isHomeDelivery() ? self::ORDER_TYPE_DELIVERY : self::ORDER_TYPE_SELF_COLLECTION;

        // Priority mapping
        $priorityMap = [
            self::SHIPPING_STATUS_PENDING        => 1,
            self::SHIPPING_STATUS_ON_REVIEW      => 2,
            self::SHIPPING_STATUS_ON_DELIVERY    => 3,
            self::SHIPPING_STATUS_PARTIAL        => 4,
            self::SHIPPING_STATUS_DELIVERED      => 5,
            self::SHIPPING_STATUS_CANCELED       => 6,
        ];

        $minPriority = 999;
        $hasNonCancel = false;

        $deliveredCount = 0;
        $cancelledCount = 0;
        $otherCount = 0;

        foreach ($orderDetails as $orderDetail) {
            $status = $orderDetail->delivery_status;

            if ($status === self::SHIPPING_STATUS_DELIVERED) {
                $deliveredCount++;
            } elseif ($status === self::SHIPPING_STATUS_CANCELED) {
                $cancelledCount++;
            } else {
                $otherCount++;
            }

            if ($status !== self::SHIPPING_STATUS_CANCELED) {
                $hasNonCancel = true;
            }

            if (isset($priorityMap[$status])) {
                $minPriority = min($minPriority, $priorityMap[$status]);
            }
        }

        if ($deliveredCount > 0 && $cancelledCount > 0 && $otherCount === 0) {
            return $this->mapDeliveryStatusText(self::SHIPPING_STATUS_PARTIAL, $orderType);
        }

        if (!$hasNonCancel) {
            return $this->mapDeliveryStatusText(self::SHIPPING_STATUS_CANCELED, $orderType);
        }

        foreach ($priorityMap as $status => $priority) {
            if ($priority === $minPriority) {
                return $this->mapDeliveryStatusText($status, $orderType);
            }
        }

        return $this->mapDeliveryStatusText(self::SHIPPING_STATUS_PENDING, $orderType);
    }

    private function mapDeliveryStatusText($status, $orderType)
    {
        if ($orderType === self::ORDER_TYPE_DELIVERY) {
            $map = self::getShippingStatusesHomeDelivery();
        } else {
            $map = self::getShippingStatusesSelfCollection();
        }

        return $map[$status] ?? $status;
    }

    public function getGrandTotalBySeller()
    {

        $user = Auth::user();
        $cart = CartService::createCartFromOrder($this);

        return $cart->getSubTotalAfterDiscountByShopId($user->shop->id);
    }

    public function getListCouponNameAttribute()
    {
        return implode('; ',$this->coupons->pluck('code')->toArray());
    }

    public function isPaid(): bool
    {
        return $this->payment_status == self::PAYMENT_STATUS_PAID;
    }

    public function histories()
    {
        return $this->morphMany(History::class, 'historiable')->orderBy('created_at', 'desc');;
    }

    public function isHomeDelivery()
    {
        foreach ($this->orderDetails as $orderDetail) {
            if ($orderDetail->shipping_type === OrderDetail::SHIPPING_TYPE_HOME_DELIVERY_KEY) {
                return true;
            }
        }
        return false;
    }
}
