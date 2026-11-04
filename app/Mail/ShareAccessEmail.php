<?php
namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShareAccessEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    private $referralLink;
    /**
     * @var User
     */
    private $sender;
    /**
     * @var string
     */
    private $duration;
    /**
     * @var bool
     */
    private $isPreview;

    /**
     * Create a new message instance.
     * @param  User  $sender
     * @param  bool  $isPreview
     */
    public function __construct(Authenticatable $sender, string $duration, bool $isPreview = false)
    {
        $this->isPreview = $isPreview;
        $this->sender = $sender;
        $this->duration = $duration;
    }

    /**
     * @param  mixed  $referralLink
     */
    public function setReferralLink($referralLink): void
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
            ->subject(__('You‘ve received an exclusive invitation!'))
            ->view('emails.referral.share_referral_access_template')
            ->with([
                'referralLink' => $this->referralLink,
                'isPreview' => $this->isPreview,
                'sender' => $this->sender,
                'duration' => $this->duration
            ]);
    }
}
