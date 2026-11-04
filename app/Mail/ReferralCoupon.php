<?php
namespace App\Mail;

use App\Coupon;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReferralCoupon extends Mailable
{
    use Queueable, SerializesModels;

    protected $coupon;
    protected $user;

    /**
     * Create a new message instance.
     *
     * @param Coupon $coupon
     * @param User $user
     */
    public function __construct(Coupon $coupon, User $user)
    {
        $this->coupon = $coupon;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('emails.referral.coupon')
            ->with([
                'coupon' => $this->coupon,
                'user' => $this->user
            ]);
    }
}
