<?php
namespace App\Models;

use App\User;
use App\Services\CartService;
use App\Models\History;
use App\Models\Traits\Historiable;
use Illuminate\Database\Eloquent\Model;
use Auth;

/**
 * App\Models\SplitOrder
 * @property int $id
 * @property float $sub_total
 * @property float $grand_total
 * @property float $shipping_cost
 * @property string $delivery_status
 * @property float $discount_amount
 * @property string $tracking_number
 * @property int $order_id
 * @property int $seller_id
 * @property string $coupon_code
 */
class SplitOrder extends Model
{
    use Historiable;
    protected $fillable = [
        'id',
        'sub_total',
        'grand_total',
        'shipping_cost',
        'delivery_status',
        'discount_amount',
        'ysg_discount_amount',
        'cash_discount_amount',
        'coupon_code',
        'coupon_value',
        'tracking_number',
        'shipping_type',
        'order_id',
        'seller_id',
        'created_at',
        'updated_at',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_usages');
    }

    public function histories()
    {
        return $this->morphMany(History::class, 'historiable')->orderBy('created_at', 'desc');
    }

    public function getDeliveryStatusTextAttribute()
    {
        return $this->shipping_type == OrderDetail::SHIPPING_TYPE_HOME_DELIVERY_KEY 
            ? Order::getShippingStatusesHomeDelivery()[$this->delivery_status] 
            : Order::getShippingStatusesSelfCollection()[$this->delivery_status];
    }

    public function getShippingAddressAttribute()
    {
        $order = $this->order;
        if ($order) {
            $shipping_address = json_decode($order->shipping_address);
            return $shipping_address->address . ', ' .
                    $shipping_address->city . ', ' .
                    $shipping_address->postal_code . ', ' .
                    $shipping_address->country;
        }
        return null;
    }
}
