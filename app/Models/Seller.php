<?php
namespace App\Models;

use App\Payment;
use App\SellerCorporateDomains;
use App\Models\EmailCorporateAccess;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Seller
 *
 * @property int $id
 * @property int $user_id
 * @property int $index
 * @property string|null $image
 * @property int $verification_status
 * @property string|null $verification_info
 * @property boolean $display
 * @property boolean $active_corporate_access_code
 * @property string|null $corporate_access_code
 * @property int $cash_on_delivery_status
 * @property int $sslcommerz_status
 * @property int|null $stripe_status
 * @property int $paypal_status
 * @property string|null $paypal_client_id
 * @property string|null $paypal_client_secret
 * @property string|null $ssl_store_id
 * @property string|null $ssl_password
 * @property string|null $stripe_key
 * @property string|null $stripe_secret
 * @property int $instamojo_status
 * @property string|null $instamojo_api_key
 * @property string|null $instamojo_token
 * @property int $razorpay_status
 * @property string|null $razorpay_api_key
 * @property string|null $razorpay_secret
 * @property float $admin_to_pay
 * @property bool $enable_corporate_domains
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereAdminToPay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereCashOnDeliveryStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereInstamojoApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereInstamojoStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereInstamojoToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller wherePaypalClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller wherePaypalClientSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller wherePaypalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereRazorpayApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereRazorpaySecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereRazorpayStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereSslPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereSslStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereSslcommerzStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereStripeKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereStripeSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereStripeStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereVerificationInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereVerificationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereActiveCorporateAccessCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seller whereCorporateAccessCode($value)
 * @mixin \Eloquent
 */
class Seller extends Model
{
    protected $fillable = [
        'user_id',
        'admin_to_pay',
        'sync_products_enabled',
        'sync_stocks_enabled',
        'sync_orders_enabled'
    ];

    protected $casts = [
        'sync_products_enabled' => 'boolean',
        'sync_stocks_enabled' => 'boolean',
        'sync_orders_enabled' => 'boolean',
    ];

    protected static function booted()
    {
        static::updated(function ($seller) {
            if (env('ELASTICSEARCH_ENABLED', false)) {
                // Update all products referencing this seller
                $products = Product::where('user_id', $seller->user->id)->get();

                foreach ($products as $product) {
                    $product->searchable(); // Update Elasticsearch index
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function corporate_domains()
    {
        return $this->hasMany(SellerCorporateDomains::class);
    }

    public function corporate_emails()
    {
        return $this->hasMany(EmailCorporateAccess::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function microsite()
    {
        return $this->hasOne(Microsite::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function syncOrderDetails()
    {
        return $this->hasMany(OrderDetail::class)
            ->where('delivery_status', '<>', 'cancelled')
            ->where('delivery_status', '<>', 'delivered')
            ->where('payment_status', 'paid')
            ;
    }

    public function getEnableCorporateDomainsAttribute()
    {
        return $this->user->enable_corporate_domains;
    }

    public function scopeWithDateUnexpired($query)
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

    public function getCompanyTypeAttribute()
    {
        if ($this->user->company_type == User::COMPANY_TYPE_FLAG_SHIP) {
            return User::COMPANY_TYPE_FLAG_SHIP;
        }

        if ($this->user->enable_corporate_domains) {
            return User::COMPANY_TYPE_CORPORATE;
        }

        return User::COMPANY_TYPE_PRIVATE_EVENT;
    }

    public function getOmsChannelNameAttribute()
    {
        if (!config('app.enable_multi_channel', FALSE)) {
            return 'ysg-sg';
        }

        switch ($this->company_type) {
            case User::COMPANY_TYPE_FLAG_SHIP:
                return 'ysgfs';
            case User::COMPANY_TYPE_CORPORATE:
                return 'ysgcs';
            case User::COMPANY_TYPE_PRIVATE_EVENT:
                return 'ysgps';
            default:
                return 'ysg-sg';
        }
    }
}
