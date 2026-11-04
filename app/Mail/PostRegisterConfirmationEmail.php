<?php
namespace App\Mail;

use App\BusinessSetting;
use App\Coupon;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PostRegisterConfirmationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * Create a new message instance.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $referralData = [
            'referrer' => $this->user->id,
            'type' => BusinessSetting::REFERRAL_TYPE_NORMAL
        ];
        $encryptedData = base64_encode(json_encode($referralData));
        $referralLink = route('user.registration', [
            'ref' => $encryptedData
        ]);
        $referralCouponSetting = BusinessSetting::where('type', 'referral_coupon_id')->first();
        $recipientEnabledSetting = BusinessSetting::where('type', 'referral_recipient_enabled')->first();
        $senderEnabledSetting = BusinessSetting::where('type', 'referral_sender_enabled')->first();
        $recipientAmount = '';
        $senderAmount = '';
        if (
            $referralCouponSetting &&
            $recipientEnabledSetting &&
            $recipientEnabledSetting->value
        ) {
            $recipientCoupon = Coupon::find($referralCouponSetting->value);
            if ($recipientCoupon) {
                $recipientAmount = $recipientCoupon->discount_type === Coupon::TYPE_PERCENTAGE ?
                    $recipientCoupon->discount . '%'
                    : '$' . $recipientCoupon->discount;
            }
        }
        $referrerCouponSetting = BusinessSetting::where('type', 'referrer_coupon_id')->first();
        if (
            $referrerCouponSetting &&
            $senderEnabledSetting &&
            $senderEnabledSetting->value
        ) {
            $senderCoupon = Coupon::find($referrerCouponSetting->value);
            if ($senderCoupon) {
                $senderAmount = $senderCoupon->discount_type === Coupon::TYPE_PERCENTAGE ?
                    $senderCoupon->discount . '%'
                    : '$' . $senderCoupon->discount;
            }
        }
        $discountTitle = __(sprintf('Share %s, Get %s*', $recipientAmount, $senderAmount));
        $discountDescription = __(
            sprintf(
                'Let your friend enjoy %s off their YSG purchase and you\'ll receive %s off* your next YSG purchase simply by sharing your unique referral link',
                $recipientAmount,
                $senderAmount
            )
        );

        return $this
            ->subject(__('Share More, Save More'))
            ->view('emails.referral.post_register_confirmation_template')
            ->with([
                'discountTitle' => $discountTitle,
                'discountDescription' => $discountDescription,
                'recipientAmount' => $recipientAmount,
                'referralLink' => $referralLink
            ]);
    }
}
