<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ProductStock
 *
 * @property int $id
 * @property int $product_id
 * @property string $variant
 * @property string $sku
 * @property string $image
 * @property float $price
 * @property int $qty
 * @property float|null $discount
 * @property string|null $discount_type
 * @property float $discount_amount
 * @property float $percent_discount
 * @property float $discount_price
 * @property float $final_price
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class ProductStock extends Model
{
    const DISCOUNT_TYPE_AMOUNT = 'amount';
    const DISCOUNT_TYPE_PERCENT = 'percent';
    const SPLIT_SYMBOL = '___';

    protected $fillable = [
        'product_id',
        'variant',
        'sku',
        'image',
        'price',
        'final_price',
        'qty',
        'discount',
        'discount_type',
    ];

    protected $attributes = [
        'price' => 0,
        'qty' => 0,
        'discount' => 0,
        'discount_type' => self::DISCOUNT_TYPE_AMOUNT,
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($stock) {
            if ($stock->isDirty('price') || $stock->isDirty('discount') || $stock->isDirty('discount_type')) {
                $stock->final_price = $stock->calculateFinalPrice();
            }
        });
    }

    protected static function booted()
    {
        static::updated(function ($variant) {
            if (env('ELASTICSEARCH_ENABLED', false)) {
                // Update all products referencing this variant
                $products = Product::where('id', $variant->product_id)->get();

                foreach ($products as $product) {
                    $product->searchable(); // Update Elasticsearch index
                }
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getDiscountAmountAttribute()
    {
        if (!$this->discount || empty($this->discount_type)) {
            return 0;
        }

        if ($this->discount_type === self::DISCOUNT_TYPE_AMOUNT) {
            return $this->discount;
        } else {
            return $this->price * $this->discount / 100;
        }
    }

    public function getDiscountPriceAttribute()
    {
        return $this->price - $this->discount_amount;
    }

    public function getPercentDiscountAttribute()
    {
        if ($this->price == 0) {
            return 0;
        }
        if (!$this->discount || empty($this->discount_type)) {
            return 0;
        }

        if ($this->discount_type === self::DISCOUNT_TYPE_AMOUNT) {
            return ($this->discount * 100) / $this->price;
        } else {
            return $this->discount;
        }
    }

    public function isDiscount(): bool {
        return (float)$this->discount !== 0.0 && !empty($this->discount_type);
    }

    public function calculateFinalPrice()
    {
        return $this->price - $this->discount_amount;
    }

    public function scopeAvailable($query, $variants = null)
    {
        $query->where('qty', '>', 0);

        if (!empty($variants)) {
            $query->whereIn('variant', (array) $variants);
        }

        return $query->get();
    }

    public function setSkuAttribute($value)
    {
        $this->attributes['sku'] = strtoupper($value);
    }
}
