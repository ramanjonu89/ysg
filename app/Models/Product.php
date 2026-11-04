<?php
namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\ObjectModels\ProductInterface;
use Carbon\Carbon;
use App\Models\Traits\TranslatableEvents;
use App\Models\Interfaces\TranslatableInterface;
use App\Models\Traits\Historiable;
use App\Models\History;
use ElasticScoutDriverPlus\Searchable;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $name
 * @property string $added_by
 * @property int $user_id
 * @property int $category_id
 * @property int|null $subcategory_id
 * @property int|null $subsubcategory_id
 * @property int $brand_id
 * @property string|null $photos
 * @property string|null $thumbnail_img
 * @property string|null $featured_img
 * @property string|null $flash_deal_img
 * @property string|null $video_provider
 * @property string|null $video_link
 * @property string|null $tags
 * @property string|null $description
 * @property float $unit_price
 * @property float $min_price
 * @property float $min_final_price
 * @property float $max_price
 * @property float $max_final_price
 * @property float $purchase_price
 * @property string|null $choice_options
 * @property string|null $colors
 * @property string $variations
 * @property int $todays_deal
 * @property int $published
 * @property int $featured
 * @property int $current_stock
 * @property string|null $unit
 * @property int $min_qty
 * @property int $max_qty
 * @property \Illuminate\Support\Carbon $max_qty_start
 * @property \Illuminate\Support\Carbon $max_qty_end
 * @property float|null $discount
 * @property string|null $discount_type
 * @property float $discount_amount
 * @property float $discount_price
 * @property float|null $tax
 * @property string|null $tax_type
 * @property string $shipping_type
 * @property float|null $shipping_cost
 * @property int $num_of_sale
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_img
 * @property string|null $pdf
 * @property string $slug
 * @property float $rating
 * @property int $frontend_sorting
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Brand $brand
 * @property-read \App\Models\Category $category
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereChoiceOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereColors($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereCurrentStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereFeaturedImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereFlashDealImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereMetaImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereNumOfSale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product wherePdf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product wherePhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereShippingCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereShippingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereSubcategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereSubsubcategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereTaxType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereThumbnailImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereTodaysDeal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereVariations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereVideoLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product whereVideoProvider($value)
 */
class Product extends Model implements ProductInterface, TranslatableInterface
{
    use TranslatableEvents, Historiable, Searchable;

    protected $guarded = [];
    
    protected $with = ['flashDeals','translation', 'category', 'stocks'];

    protected $ignoreLoggingFields = [
        'colors',
        'refundable'
    ];

    const ADD_BY_SELLER = 'seller';
    const ADD_BY_ADMIN = 'admin';

    const PRE_FIX_CA_NAME = '[CORPORATE]';
    const PRE_FIX_CA_UNIT = 'CSS';

    const DISCOUNT_TYPE_AMOUNT = 'amount';
    const DISCOUNT_TYPE_PERCENT = 'percent';

    const TAX_TYPE_AMOUNT = 'amount';
    const TAX_TYPE_PERCENT = 'percent';

    const FREE_SHIPPING_ABOVE = 80;

    const SORT_BY_TOP_SALES = '1';
    const SORT_BY_LATEST = '2';
    const SORT_BY_PRICE_LOW_TO_HIGH = '3';
    const SORT_BY_PRICE_HIGH_TO_LOW = '4';
    const MAX_QTY_START = 'max_qty_start';
    const MAX_QTY_END = 'max_qty_end';

    protected $fillable = [
        'name',
        'added_by',
        'user_id',
        'category_id',
        'subcategory_id',
        'subsubcategory_id',
        'brand_id',
        'photos',
        'thumbnail_img',
        'video_provider',
        'video_link',
        'tags',
        'description',
        'unit_price',
        'min_price',
        'min_final_price',
        'max_price',
        'max_final_price',
        'purchase_price',
        'variant_product',
        'attributes',
        'choice_options',
        'colors',
        'variations',
        'todays_deal',
        'published',
        'featured',
        'current_stock',
        'unit',
        'min_qty',
        'max_qty',
        'max_qty_start',
        'max_qty_end',
        'discount',
        'discount_type',
        'tax',
        'tax_type',
        'shipping_type',
        'shipping_cost',
        'shipping_cost_above',
        'num_of_sale',
        'meta_title',
        'meta_description',
        'meta_img',
        'pdf',
        'slug',
        'refundable',
        'rating',
        'barcode',
        'digital',
        'file_name',
        'file_path',
        'created_at',
        'updated_at',
        'featured_sorting',
        'only_microsite',
        'frontend_sorting',
    ];

    protected $attributes = [
        'unit_price' => 0,
        'current_stock' => 0,
        'discount' => 0,
        'discount_type' => self::DISCOUNT_TYPE_AMOUNT,
    ];

    public function __construct(array $attributes = [])
    {
        $this->translatableFields = ['name', 'description', 'meta_title', 'meta_description'];
        $this->idField = 'product_id';

        parent::__construct($attributes);
    }
    
    public function shouldBeSearchable()
    {
        return count($this->toSearchableArray()) > 0;
    }


    public function toSearchableArray()
    {
        if (!config('app.elasticsearch_enabled', false)) {
            return [];
        }
        $data = $this->toArray();

        $data['todays_deal'] = (bool)$data['todays_deal'];
        $data['published'] = (bool)$data['published'];
        $data['featured'] = (bool)$data['featured'];
        $data['only_microsite'] = (bool)$data['only_microsite'];

        $data['final_price'] = $this->final_price;
        $data['discount_amount'] = $this->discount_amount;

        $data['reviews'] = $this->reviews->map(function ($review) {
            $review['status'] = (bool) $review['status'];
            return $review;
        });

        $data['seller'] = [
            'id' => $this->user->seller->id,
            'verification_status' => $this->user->seller->verification_status,
        ];

        $cat = $this->category;

        $data['category'] = [
            'id' => $cat->id,
            'name' => $cat->name,
            'is_age_restricted' => $cat->is_age_restricted,
            'featured' => $cat->featured,
            'status' => (bool) $cat->status,
        ];

        $subCat = $this->subCategory;

        if (!empty($subCat)) {
            $data['subcategory'] = [
                'id' => $subCat->id,
                'name' => $subCat->name,
                'status' => (bool) $subCat->status,
            ];
        }

        $subsubCat = $this->subsubcategory;
        if (!empty($subsubCat)) {
            $data['subsubcategory'] = [
                'id' => $subsubCat->id,
                'name' => $subsubCat->name,
                'status' => (bool) $subsubCat->status,
            ];
        }

        $brand = $this->brand;
        if (!empty($brand)) {
            $data['brand'] = [
                'id' => $brand->id,
                'name' => $brand->name,
                'status' => (bool) $brand->status,
            ];
        }

        unset($data['choice_options']);

        $data['variants'] = $this->stocks;

        return $data;
    }

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope('published', function(Builder $builder) {
    //         $builder->where('published', 1);
    //     });
    // }

    /**
     * Scope a query to only include published products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query): Builder
    {
        return $query->where('published', 1);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withCount('productReviews');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function flashDeals()
    {
        $now = strtotime('now');
        return $this->belongsToMany(FlashDeal::class, 'flash_deal_products')
            ->where('status', 1)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->withPivot('discount', 'discount_type')
            ;
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function histories()
    {
        return $this->morphMany(History::class, 'historiable')->orderBy('created_at', 'desc');
    }

    /**
     * Check if is CA product by PreFix name and PreFix unit
     * @return bool
     */
    public function getIsCAAttribute(): bool
    {
        $checkName = strpos($this->name, self::PRE_FIX_CA_NAME);
        $checkUnit = strpos($this->unit, self::PRE_FIX_CA_UNIT);

        return ($checkName === 0 && $checkUnit === 0);
    }

    public function subsubcategory()
    {
        return $this->belongsTo(SubSubCategory::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function soldQuantity()
    {
        return $this->orderDetails()->where('payment_status', Order::PAYMENT_STATUS_PAID)->count();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('status', 1)->with('reviewCategories');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function getDiscountTypeAttribute($value)
    {
        if ($this->flashDeals->isEmpty()) {
            return $value;
        }

        return $this->flashDeals()->first()->pivot->discount_type;
    }

    public function getDiscountAttribute($value)
    {
        if ($this->flashDeals->isEmpty()) {
            return $value;
        }

        return $this->flashDeals()->first()->pivot->discount;
    }

    public function getDiscountPriceAttribute()
    {
        return $this->unit_price - $this->discount_amount;
    }

    public function getChoiceOptionsAttribute($value) {
        return $value === null ? [] : $value;
    }

    public function getVariantsAttribute()
    {
        return json_decode($this->choice_options);
    }

    public function getStock(string $variantSku = '')
    {
        return $variantSku === '' ? $this->current_stock : $this->getVariantStock($variantSku);
    }

    public function getVariant(string $variantSku)
    {
        $result = null;
        foreach ($this->stocks as $stock) {
            if ($stock->variant == $variantSku || $stock->variant == str_replace(' ', '', $variantSku)) {
                $result = $stock;
                break;
            }
        }
        // return $this->stocks()->where('variant', $variantSku)->orWhere('variant', str_replace(' ', '', $variantSku))->first();
        return $result;
    }

    public function getVariantStock(string $variantSku)
    {
        return $this->getVariant($variantSku)->qty;
    }

    public function getDiscount(string $variantSku = '')
    {
        return $variantSku === '' ? $this->discount : $this->getVariant($variantSku)->discount;
    }

    public function getDiscountType(string $variantSku = '')
    {
        return $variantSku === '' ? $this->discount_type : $this->getVariant($variantSku)->discount_type;
    }

    /**
     * @TODO - add flash deal discount
     * @return float|int|mixed|null
     */
    public function getMaxPercentDiscountAttribute() {
        $highestPercent = 0;
        $stocks = $this->stocks;
        if ($stocks->count() > 0) {
            foreach ($stocks as $stock) {
                $highestPercent = max($highestPercent, $stock->percent_discount);
            }
        } else if ($this->discount > 0) {
            if ($this->discount_type === self::DISCOUNT_TYPE_AMOUNT) {
                $highestPercent = (($this->discount * 100) / $this->unit_price);
            } else {
                $highestPercent = $this->discount;
            }
        }
        return $highestPercent;
    }

    public function getDiscountBadgeAttribute($value): string
    {
        $price = $this->max_percent_discount;

//        foreach ($this->flashDeals as $flashDeal) {
//            $flashDealProduct = $flashDeal->flashDealProducts()->where('product_id', $this->id)->first();
//            if (isset($flashDealProduct) && $flashDeal->isExpired()) {
//                $price = $this->unit_price;
//                if ($flashDealProduct->discount_type == Product::DISCOUNT_TYPE_AMOUNT) {
//                    $price = ($flashDealProduct->discount * 100) / $price;
//                } else if ($flashDealProduct->discount_type == Product::DISCOUNT_TYPE_PERCENT) {
//                    $price = $flashDealProduct->discount;
//                }
//                break;
//            }
//        }

        return "-" . round($price) . "%";
    }

    public function getDiscountAmountAttribute()
    {
        if (!$this->has_discount) {
            return 0;
        }

        if ($this->discount_type === self::DISCOUNT_TYPE_AMOUNT) {
            return $this->discount;
        } else {
            return $this->unit_price * $this->discount / 100;
        }
    }

    public function getHasDiscountAttribute()
    {
        if (isset($this->flashDeals)) {
            foreach ($this->flashDeals as $flashDeal) {
                $flashDealProduct = $flashDeal->flashDealProducts()->where('product_id', $this->id)->first();
                if (isset($flashDealProduct) && !$flashDeal->isExpired() && $flashDealProduct->isDiscount()) {
                    return true;
                }
            }
        }
        if ($this->stocks->count()) {
            foreach ($this->stocks as $stock) {
                if($stock->isDiscount()) {
                    return true;
                }
            }
        } else if ($this->isDiscount()) {
            return true;
        }

        return false;
    }

    public function isDiscount(): bool {
        return (float)$this->discount !== 0.0;
    }

    public function getFinalPriceAttribute()
    {
        return $this->unit_price - $this->discount_amount;
    }

    public function getPriceAttribute()
    {
        return $this->has_discount ? $this->final_price : $this->unit_price;
    }

    public function getFeaturedAttribute()
    {
        $shop = $this->user->shop;
        $featuredProductIds = explode(',', $shop->featured_products);
        return in_array($this->id, $featuredProductIds);
    }

    /**
     * Scope a query to filter microsite products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMicrosite($query)
    {
        if (!isMicrosite()) {
            return $query->where('only_microsite', 0);
        }
        return $query;
    }

    public function scopeOnlyUnexpiredSellers($query)
    {
        return $query->whereHas('user', function (Builder $query) {
            $query->where('company_type', User::COMPANY_TYPE_FLAG_SHIP);
            $query->orWhere(function($q){
                $q->where('company_type', User::COMPANY_TYPE_PRIVATE_EVENT);
                $q->whereDate('company_start_date', '<=', Carbon::now());
                $q->whereDate('company_end_date', '>', Carbon::now());
            });
        });
    }

    public function scopeCategoryEnabled($query)
    {
        return $query->whereHas('category', function (Builder $query) {
            $query->where('status','=', 1);
        });
    }

    public function scopeBrandEnabled($query)
    {
        return $query->whereHas('brand', function (Builder $query) {
            $query->where('status','=', 1);
        });
    }

    public function scopeSubCategoryEnabled($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('subcategory_id')
                    ->orWhere('subcategory_id', 0);
                })->orWhereHas('subCategory', function (Builder $query) {
                    $query->where('status', '=', 1);
            });
    }

    public function scopeSubSubCategoryEnabled($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('subsubcategory_id')
                ->orWhere('subsubcategory_id', 0);
            })->orWhereHas('subsubcategory', function (Builder $query) {
                $query->where('status', '=', 1);
        });
    }

    public function scopeWithPublished($query)
    {
        return $query->where('published', 1);
    }

    //Implements ProductInterface to support Cart
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getThumbnailImg(): string
    {
        if (!empty($this->thumbnail_img)) {
            return $this->thumbnail_img;
        }
        return 'frontend/images/placeholder.jpg';
    }

    public function getTaxType(): ?string
    {
        return $this->tax_type;
    }

    public function getTax(): ?float
    {
        return $this->tax;
    }

    public function getTaxAmountAttribute(): ?float{
        if($this->tax_type === self::TAX_TYPE_AMOUNT) {
            return $this->tax;
        } else {
            return  $this->price * $this->tax / 100 ; 
        }
    }

    public function getPrice(string $variation = '')
    {
        return '' === $variation ? $this->final_price : $this->getVariantPrice($variation);
    }

    public function getVariantPrice(string $variantSku = '')
    {
        $variant = $this->getVariant($variantSku);

        return !$variant ? 0 : $variant->final_price;
    }

    public function getVariantId(string $variation = '')
    {
        return '' === $variation ? null : $this->getVariant($variation)->id;
    }

    public function getSellerId()
    {
        return $this->user_id;
    }

    public function getMinQty()
    {
        return $this->min_qty;
    }

    public function getMaxQty()
    {
        $maxQtyStart = Carbon::parse($this->max_qty_start);
        $maxQtyEnd   = Carbon::parse($this->max_qty_end);

        if ($maxQtyStart->isPast() && $maxQtyEnd->isFuture()) {
            return $this->max_qty;
        }

        return 0;
    }

    public function getMaxQtyStart()
    {
        return $this->max_qty_start;
    }

    public function getMaxQtyEnd()
    {
        return $this->max_qty_end;
    }

    public function getShopId()
    {
        return $this->user->shop->id;
    }

    public function getShopName()
    {
        return $this->user->shop->name;
    }

    public function getShopSlug()
    {
        return $this->user->shop->slug;
    }

    public function getCompanyShippingType()
    {
        return $this->user->company_shipping;
    }

    public function getFreeShippingAbove()
    {
        return $this->user->shop->shipping_cost_above;
    }

    public function getShippingFee()
    {
        return $this->user->shop->shipping_cost;
    }

    public function getOrderType()
    {
        return $this->user->shop->order_type;
    }

    public function getDefaultAddress()
    {
        return $this->user->getDefaultAddress();
    }

    public function productTranslation($languageId = 1){
        return $this->hasOne(ProductTranslation::class)->where(['language_id' => $languageId])->first();
    }


    public function translation()
    {
        $languageId = session('sessionLanguageId');
        return $this->hasMany(ProductTranslation::class)->where('language_id', $languageId);
    }

    public function translationByLangId($languageId)
    {
        return $this->hasMany(ProductTranslation::class)->where('language_id', $languageId)->first();
    }

    public function translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function getTranslationNameAttribute()
    {
        return $this->translation->isEmpty() ? $this->name : $this->translation->first()->name;
    }

    public function getTranslationMetaTitleAttribute()
    {
        return $this->translation->isEmpty() ? $this->meta_title : $this->translation->first()->meta_title;
    }

    public function getTranslationMetaDescriptionAttribute()
    {
        return $this->translation->isEmpty() ? $this->meta_description : $this->translation->first()->meta_description;
    }

    public function getTranslationDescriptionAttribute()
    {
        return $this->translation->isEmpty() ? $this->description : $this->translation->first()->description;
    }

    public function getCategoryId()
    {
        return $this->category_id;
    }

    public function getSubCategoryId()
    {
        return $this->subcategory_id;
    }

    public function getSubSubCategoryId()
    {
        return $this->subsubcategory_id;
    }

    public function getIsEnableProductAttribute()
    {
        return $this->enableBrand() && $this->enableCategory() && 
                $this->enableSubCategory() && $this->enableSubSubCategory();
    }

    private function enableBrand()
    {
        return $this->brand->status === 1;
    }

    private function enableCategory()
    {
        return $this->category->status === 1;
    }

    private function enableSubCategory()
    {
        if ($this->subCategory === null) {
            return true;
        }
        return $this->subCategory->status === 1;
    }

    private function enableSubSubCategory()
    {
        if ($this->subsubcategory === null) {
            return true;
        }
        return $this->subsubcategory->status === 1;
    }

    public function getRangeUnitPriceAttribute()
    {
        $lowestPrice = $this->unit_price;
        $highestPrice = $this->unit_price;

        if ($this->stocks->count() > 0) {
            $lowestPrice = PHP_FLOAT_MAX;
            $highestPrice = 0;
            foreach ($this->stocks as $stock) {
                $lowestPrice = min($lowestPrice, $stock->price);
                $highestPrice = max($highestPrice, $stock->price);
            }
        }

        $lowestPrice = convert_price($lowestPrice);
        $highestPrice = convert_price($highestPrice);
        if ($lowestPrice == $highestPrice) {
            return format_price($lowestPrice);
        }

        return format_price($lowestPrice) . ' - ' . format_price($highestPrice);
    }

    public function getQuantityAttribute(): int
    {
        $quantity = 0;
        if($this->stocks()->count()){
            foreach ($this->stocks as $stock) {
                $quantity += $stock->qty;
            }
        } else {
            $quantity = $this->current_stock;
        }

        return $quantity;
    }

    public function getAgeRestricted() {
        return $this->category->is_age_restricted;
    }

    public function isProductForAdults() {
        return $this->category->is_age_restricted == AgeRestrictedSetting::TWENTYONE_AND_ABOVE;
    }

    public function getThumbnailImgAttribute(): string
    {
        if (!isset($this->attributes['thumbnail_img']) || $this->attributes['thumbnail_img'] == '') {
            return '';
        }

        if (strpos($this->attributes['thumbnail_img'], 'uploads') !== false && strpos($this->attributes['thumbnail_img'], 'products') !== false) {
            return $this->attributes['thumbnail_img'];
        }

        return "/uploads/products/$this->id/" . $this->attributes['thumbnail_img'];
    }

    public function getPhotosAttribute(): string
    {
        $data = is_array($this->attributes['photos'])
                ?$data = $this->attributes['photos']
                : json_decode($this->attributes['photos']);
        $newData = [];
        try {
            if ($data && count($data) > 0) {
                foreach($data as $image) {
                    if (is_array($image)) {
                        continue;
                    }
                    if (strpos($image, 'uploads') !== false && strpos($image, 'products') !== false) {
                        $newData[] = $image;
                    } else {
                        $newData[] = "/uploads/products/$this->id/" . $image;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error getting photos attribute, product id: ' . $this->id . ' - ' . $e->getMessage());
        }

        return json_encode($newData);
    }

    public function setUnitAttribute($value)
    {
        $this->attributes['unit'] = strtoupper($value);
    }

    public function getThumbnailImageOrder($variation = ''): string
    {
        $thumbnail = $this->thumbnail_img;

        if ($variation) {
            $variant    = $this->getVariant($variation);
            
            if ($variant && $variant->image) {
                $thumbnail = '/' . $variant->image;
            }
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

    public function getMinFinalPrice() : float
    {
        return $this->min_final_price;
    }

    public function getIsFreeLocalDeliverys(): bool
    {
        return $this->getMinFinalPrice() >= $this->getFreeShippingAbove();
    }
}