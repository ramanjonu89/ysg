<?php
namespace App;

use App\Models\Address;
use App\Models\Cart;
use App\Models\MicrositeUser;
use App\Models\Seller;
use App\Models\Shop;
use App\Models\UserTask;
use App\Models\FcmToken;
use App\Notifications\EmailVerificationNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * App\User
 *
 * @property int $id
 * @property string $email
 * @property bool $enable_corporate_domains
 * @property string $company_id
 * @property string $api_key
 *
 */

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasApiTokens, SoftDeletes;

    const COMPANY_TYPE_FLAG_SHIP = 1;
    const COMPANY_TYPE_PRIVATE_EVENT = 2; // private sale
    const COMPANY_TYPE_CORPORATE = 3;

    const COMPANY_SHIPPING_TYPE_INTERNAL = 1; // YCH Combine
    const COMPANY_SHIPPING_TYPE_EXTERNAL = 2; // Personal Shipping

    const TYPE_ADMIN = 'admin';
    const TYPE_SELLER = 'seller';
    const TYPE_STAFF = 'staff';
    const TYPE_CUSTOMER = 'customer';

    const SALE_REPORT_TYPE = 'sale_report';
    const FULFILMENT_REPORT_TYPE = 'fulfilment_report';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'address', 'city', 'postal_code', 'phone', 'country', 'provider_id', 'email_verified_at', 'verification_code', 'company_id',
        'enable_access_code', 'message_content', 'enable_corporate_domains', 'corporate_access_domain', 'corporate_access_expire_at', 'api_key', 'user_type',
        'receive_special_offers', 'avatar_original', 'last_login', 'country_code'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $dates = ['corporate_access_expire_at'];

    public function sendEmailVerificationNotification()
    {
        $this->notify(new EmailVerificationNotification([]));
    }
    
    public function routeNotificationForFirebase()
    {
        return $this->id;
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class)->join('products', 'products.id', '=', 'wishlists.product_id')->where('published', 1);
        //return $this->hasMany(Wishlist::class);
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function seller()
    {
        return $this->hasOne(Seller::class);
    }

    public function affiliate_user()
    {
        return $this->hasOne(AffiliateUser::class);
    }

    public function affiliate_withdraw_request()
    {
        return $this->hasMany(AffiliateWithdrawRequest::class);
    }

    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserTask::class);
    }

    public function saleReportTask()
    {
        return $this->tasks()->where('type', self::SALE_REPORT_TYPE)->first();
    }

    public function getSaleReportFrequencyAttribute()
    {
        return $this->saleReportTask() !== null ? $this->saleReportTask()->frequency : '';
    }

    public function getSaleFrequencyOptionsAttribute()
    {
        return $this->saleReportTask() !== null ? $this->saleReportTask()->options : '';
    }

    public function fulfilmentReportTask()
    {
        return $this->tasks()->where('type', self::FULFILMENT_REPORT_TYPE)->first();
    }

    public function getFulfilmentReportFrequencyAttribute()
    {
        return $this->fulfilmentReportTask() !== null ? $this->fulfilmentReportTask()->frequency : '';
    }

    public function getFulfilmentFrequencyOptionsAttribute()
    {
        return $this->fulfilmentReportTask() !== null ? $this->fulfilmentReportTask()->options : '';
    }

    public function products()
    {
        return $this->hasMany(Product::class)->withCount('reviews');
    }

    public function splitOrders()
    {
        return $this->hasMany(SplitOrder::class, 'seller_id', 'id');
    }

    public function productReviews(): HasManyThrough 
    {
        return $this->hasManyThrough(Review::class, Product::class);
    }

    public function getSumReviewRatingAttribute()
    {
        return $this->productReviews->sum('rating');
    }

    public function getProductReviewsCountAttribute()
    {
        return $this->productReviews->count();
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class)->orderBy('created_at', 'desc');
    }

    public function club_point()
    {
        return $this->hasOne(ClubPoint::class);
    }

    public function customer_package()
    {
        return $this->belongsTo(CustomerPackage::class);
    }

    public function customer_package_payments()
    {
        return $this->hasMany(CustomerPackagePayment::class);
    }

    public function customer_products()
    {
        return $this->hasMany(CustomerProduct::class);
    }

    public function seller_package_payments()
    {
        return $this->hasMany(SellerPackagePayment::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function getDefaultAddress() {
        if ($this->addresses()->count() == 0) {
            return null;
        }
        if ($this->addresses()->where('set_default', true)->count() == 0) {
            return $this->addresses()->first();
        }
        return $this->addresses()->where('set_default', true)->first();
    }

    public function getPhoneNumbersAttribute()
    {
        return implode(",", $this->addresses()->get()->pluck('phone')->toArray());
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('active', true);
    }

    /**
     * @return Model|HasMany|object|null
     */
    public function getActiveCorporateAccess()
    {
        return $this->corporateAccesses()
            ->where('is_active', true)
            ->where('expire_at', '>', Carbon::now())
            ->first();
    }

    /**
     * @return HasMany
     */
    public function corporateAccesses()
    {
        return $this->hasMany(UserCorporateAccess::class, 'user_id', 'id');
    }

    public function getUnusedCorporateAccess()
    {
        return $this->corporateAccesses()
            ->where('is_active', false)
            ->get();
    }

    public function micrositeUsers(): HasOne
    {
        return $this->hasOne(MicrositeUser::class);
    }

    public function getMicrositeAttribute()
    {
        $model = $this->micrositeUsers;
        return is_null($model)?null:$model->microsite;
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'seller_id', 'id');
    }

    public function syncOrderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'seller_id', 'id')
            ->where('delivery_status', '<>', 'cancelled')
            ->where('delivery_status', '<>', 'delivered')
            ->where('payment_status', 'paid')
            ;
    }

    /**
     * @return string
     */
    public function getDomainAttribute(): string
    {
        $parts = explode("@", $this->email);
        return '@' . $parts[1];
    }

    public function isExpired(): bool
    {
        if ($this->company_type === self::COMPANY_TYPE_PRIVATE_EVENT) {
            return !(Carbon::parse($this->company_start_date)->format('Y-m-d 00:00:00') <= Carbon::now() &&
            Carbon::now() < Carbon::parse($this->company_end_date)->format('Y-m-d 23:59:59'));
        }

        return false;
    }

    public function isCommingSoon(): bool
    {
        if ($this->company_type == self::COMPANY_TYPE_PRIVATE_EVENT) {
            return Carbon::parse($this->company_start_date)->format('Y-m-d 00:00:00') >= Carbon::now();
        }

        return false;
    }

    public function isPrivateSale(): bool
    {
        return $this->company_type == self::COMPANY_TYPE_PRIVATE_EVENT && $this->enable_access_code == 1;
    }

    public function isSeller(): bool
    {
        return self::TYPE_SELLER === $this->user_type;
    }

    public function isStaff(): bool
    {
        return self::TYPE_STAFF === $this->user_type;
    }

    public function fullPhone(): string
    {
        if (!empty($this->country_code) && !empty($this->phone)) {
            return $this->country_code . $this->phone;
        }
        return '';
    }

    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }

    public function activeFcmTokens()
    {
        return $this->hasMany(FcmToken::class)->where('is_active', true);
    }
}
