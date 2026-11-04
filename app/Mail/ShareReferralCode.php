<?php

namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class ShareReferralCode extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    private $sender;

    /**
     * @var string
     */
    protected $referralLink;

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
        return $this
            ->subject(__('You‘ve received a referral code!!'))
            ->view('emails.referral.share_referral_code')
            ->with([
                'referralLink' => $this->referralLink,
                'sender' => $this->sender,
            ]);
    }
}
