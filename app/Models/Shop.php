<?php
namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Shop
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $logo
 * @property string|null $sliders
 * @property string|null $address
 * @property string|null $facebook
 * @property string|null $google
 * @property string|null $twitter
 * @property string|null $youtube
 * @property string|null $instagram
 * @property string|null $slug
 * @property int $company_type
 * @property bool $enable_corporate_domains
 * @property bool $enable_access_code
 * @property string|null $access_code
 * @property string|null $access_code_message
 * @property bool $general_settings
 * @property string $featured_products
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereGoogle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereSliders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop whereYoutube($value)
 * @mixin \Eloquent
 */
class Shop extends Model
{
    const ORDER_TYPE_DELIVERY = 1;
    const ORDER_TYPE_SELF_COLLECTION = 2;
    const ORDER_TYPE_BOTH = self::ORDER_TYPE_DELIVERY + self::ORDER_TYPE_SELF_COLLECTION;

    const ORDER_TYPE_DELIVERY_KEY = 'delivery';
    const ORDER_TYPE_SELF_COLLECTION_KEY = 'self_collection';

    protected $fillable = [
        'user_id',
        'name',
        'logo',
        'sliders',
        'address',
        'facebook',
        'google',
        'twitter',
        'youtube',
        'instagram',
        'slug',
        'company_type',
        'enable_corporate_domains',
        'featured_products',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->user->seller();
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class, 'user_id', 'user_id');
    }

    public function allowedAttributes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Attribute::class);
    }

    public function sellerCoupons(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Coupon::class, 'seller_id', 'id')
            //->leftJoin('coupon_usages', 'coupons.id', '=', 'coupon_usages.coupon_id')
            ->where('owner', Coupon::OWNER_SELLER)
            ->where('is_active', Coupon::STATUS_ACTIVE)
            ->where('published', Coupon::IS_PUBLISHED)
            ->where('start_date', '<=', time())
            ->where('end_date', '>=', time())
            ;
    }

    public function getCompanyTypeAttribute()
    {
        return $this->user->company_type;
    }

    public function getEnableCorporateDomainsAttribute()
    {
        return $this->user->enable_corporate_domains;
    }

    public function getCorporateDomainsAttribute()
    {
        return $this->user->seller->corporate_domains;
    }

    public function blocks(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany('App\Models\CustomPage\Block', 'blockable')->withPivot('sort')->orderBy('pivot_sort');
    }

    public function getEnableAccessCodeAttribute()
    {
        return $this->user->enable_access_code;
    }

    public function getAccessCodeAttribute()
    {
        return $this->user->company_password;
    }

    public function getAccessCodeMessageAttribute()
    {
        return $this->user->message_content;
    }

    public function getGeneralSettingsAttribute($value): \Illuminate\Support\Collection
    {
        $defSettings = [
            'sliders' => [
                'images' => []
            ],
            'featured_products' => []
        ];
        if ($value) {
            $collection = collect(json_decode($value, true));
            return $collection->union($defSettings);
        } else {
            return collect($defSettings);
        }
    }

    public function scopeOnlyUnexpiredSellers($query)
    {
        return $query->whereHas('user', function (Builder $query) {
            $query->where('company_type', User::COMPANY_TYPE_FLAG_SHIP);
            $query->orWhere(function($q){
                $q->where('company_type',User::COMPANY_TYPE_PRIVATE_EVENT);
                $q->whereDate('company_start_date', '<=', Carbon::now());
                $q->whereDate('company_end_date', '>', Carbon::now());
            });
        });
    }

    public function getLogoShop()
    {
        if ($this->logo == null) {
           return 'frontend/images/placeholder.jpg';
        }

        return $this->logo;
    }

    public function isVerificationShop()
    {
        return $this->user->seller->verification_status;
    }

    public function isSellingAdultProducts() 
    {
        $products = $this->products;
        foreach ($products as $product) {
            if ($product->isProductForAdults()) {
                return true;
            }
        }

        return false;
    }

    public function hasDeliveryOrderType() {
        return $this->order_type == self::ORDER_TYPE_DELIVERY || $this->order_type == self::ORDER_TYPE_BOTH;
    }

    public function hasSelfCollectionOrderType() {
        return $this->order_type == self::ORDER_TYPE_SELF_COLLECTION || $this->order_type == self::ORDER_TYPE_BOTH;
    }
}
