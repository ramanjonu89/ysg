<?php
namespace App\Mail;

use App\Coupon;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShareReferralEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $referralLink;
    /**
     * @var Coupon
     */
    private $coupon;
    /**
     * @var User
     */
    private $sender;

    /**
     * Create a new message instance.
     * @param User $sender
     */
    public function __construct(Authenticatable $sender)
    {
        $this->sender = $sender;
    }

    /**
     * Set referralLink.
     * @param $coupon
     */
    public function setCoupon($coupon)
    {
        $this->coupon = $coupon;
    }

    /**
     * Set referralLink.
     * @param $referralLink
     */
    public function setReferralLink($referralLink)
    {
        $this->referralLink = $referralLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $couponType = $this->coupon->discount_type;
        $discountText = $couponType === 'percent' ? $this->coupon->discount . '%' : '$' . $this->coupon->discount;

        return $this
            ->subject(__('Redeem Your Voucher Now!'))
            ->view('emails.referral.share_referral_template')
            ->with([
                'referralLink' => $this->referralLink,
                'discountText' => $discountText,
                'sender' => $this->sender
            ]);
    }
}
