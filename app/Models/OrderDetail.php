<?php
namespace App\Models;

use App\User;
use App\PickupPoint;
use App\RefundRequest;
use App\Models\History;
use App\Models\Traits\Historiable;
use Illuminate\Database\Eloquent\Model;
use App\ObjectModels\ProductInterface;
use Carbon\Carbon;

/**
 * App\Models\OrderDetail
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $split_order_id
 * @property int|null $seller_id
 * @property int $product_id
 * @property string $product_name
 * @property string|null $variation
 * @property float|null $price
 * @property float $tax
 * @property float $shipping_cost
 * @property float $rsp_price
 * @property float|null $discount
 * @property string|null $discount_type
 * @property int|null $quantity
 * @property string $payment_status
 * @property string|null $delivery_status
 * @property string|null $trackingnum
 * @property string|null $shipping_type
 * @property int|null $pickup_point_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail whereDeliveryStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail whereShippingCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail whereVariation($value)
 * @mixin \Eloquent
 */
class OrderDetail extends Model implements ProductInterface
{
    use Historiable;
    protected $guarded = [];

    const DISCOUNT_TYPE_AMOUNT = 'amount';
    const DISCOUNT_TYPE_PERCENT = 'percent';

    const SHIPPING_TYPE_HOME_DELIVERY_KEY = 'home_delivery';
    const SHIPPING_TYPE_SELF_COLLECTION_KEY = 'self_collection';

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function splitOrder()
    {
        return $this->belongsTo(SplitOrder::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function pickup_point()
    {
        return $this->belongsTo(PickupPoint::class);
    }

    public function refund_request()
    {
        return $this->hasOne(RefundRequest::class);
    }

    public function getId(): int
    {
        return $this->product->id;
    }

    public function getName(): string
    {
        return $this->product->name;
    }

    public function getSlug(): string
    {
        return $this->product->slug;
    }

    public function getThumbnailImg(): string
    {
        return $this->product->thumbnail_img;
    }

    public function getTaxType(): ?string
    {
        return $this->product->tax_type;
    }

    public function getTax(): ?float
    {
        return $this->product->tax;
    }

    public function getDiscountAmount()
    {
        if ((int)$this->discount === 0) {
            return 0;
        }

        if ($this->discount_type === self::DISCOUNT_TYPE_AMOUNT) {
            return $this->discount;
        } else {
            return $this->rsp_price * $this->discount / 100;
        }
    }

    public function getPrice(string $variantion = '')
    {
        return $this->rsp_price - $this->getDiscountAmount();
    }

    public function getSellerId()
    {
        return $this->product->user->id;
    }

    public function getShopId()
    {
        return $this->product->user->shop->id;
    }

    public function getShopName()
    {
        return $this->product->user->shop->name;
    }

    public function getShopSlug()
    {
        return $this->product->user->shop->slug;
    }

    public function getCompanyShippingType()
    {
        return $this->product->user->company_shipping;
    }

    public function getFreeShippingAbove()
    {
        return $this->product->user->shop->shipping_cost_above;
    }

    public function getShippingFee()
    {
        return $this->product->user->shop->shipping_cost;
    }

    public function getCategoryId()
    {
        return $this->product->category_id;
    }

    public function getSubCategoryId()
    {
        return $this->product->subcategory_id;
    }

    public function getSubSubCategoryId()
    {
        return $this->product->subsubcategory_id;
    }

    public function isShippingTypeHomeDelivery(): bool
    {
        return $this->shipping_type === self::SHIPPING_TYPE_HOME_DELIVERY_KEY;
    }

    public function isShippingTypeSelfCollection(): bool
    {
        return $this->shipping_type === self::SHIPPING_TYPE_SELF_COLLECTION_KEY;
    }

    public function isShippingTypePickupPoint(): bool
    {
        return $this->shipping_type === 'pickup_point' && $this->pickup_point_id != null;
    }

    public function getSku()
    {
        if ($this->variation) {
            return $this->product->getVariant($this->variation)->sku;
        }

        return $this->product->unit;
    }

    public function getMinQty()
    {
        return $this->product->min_qty;
    }

    public function getMaxQty()
    {
        $maxQtyStart = Carbon::parse($this->product->max_qty_start);
        $maxQtyEnd   = Carbon::parse($this->product->max_qty_end);

        if ($maxQtyStart->isPast() && $maxQtyEnd->isFuture()) {
            return $this->product->max_qty;
        }

        return 0;
    }

    public function histories()
    {
        return $this->morphMany(History::class, 'historiable')->orderBy('created_at', 'desc');
    }

    public function getChoiceOptions() {
        return $this->product->choice_options;
    }

    public function getVariantDetails()
    {
        $results = [];
        $choiceOptions = $this->getChoiceOptions();
        $variation = collect(explode(ProductStock::SPLIT_SYMBOL, $this->variation))
                    ->filter()
                    ->values();

        foreach (json_decode($choiceOptions) as $choiceOption) {
            foreach ($variation as $item) {
                if (in_array($item, $choiceOption->values)) {
                    $results[] = $choiceOption->attribute->text . ": " . $item;
                }
            }
        }

        return $results;
    }

    public function getMaxQtyStart()
    {
        return $this->product->max_qty_start;
    }

    public function getMaxQtyEnd()
    {
        return $this->product->max_qty_end;
    }

    public function getThumbnailImageOrder($variation = ''): string
    {
        $thumbnail = $this->product->thumbnail_img;

        if ($variation) {
            $variant   = $this->product->getVariant($variation);
            $thumbnail = $variant->image ?: $thumbnail;
        }

        if (!$thumbnail) {
            return '';
        }

        if (
            strpos($thumbnail, 'uploads') !== false &&
            strpos($thumbnail, 'products') !== false
        ) {
            return strpos($thumbnail, '/') === 0
                ? $thumbnail
                : '/' . $thumbnail;
        }

        return '/uploads/products/' . $this->id . '/thumbnail/' . $thumbnail;
    }

    public function getOrderType()
    {
        return $this->product->user->shop->order_type;
    }

    public function getDefaultAddress()
    {
        return $this->product->user->getDefaultAddress();
    }

    public function getVariantId(string $variation = '')
    {
        return '' === $variation ? null : $this->product->getVariant($variation)->id;
    }
}
