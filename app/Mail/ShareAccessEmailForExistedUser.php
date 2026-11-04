<?php
namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShareAccessEmailForExistedUser extends Mailable
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
     * @var User
     */
    private $recipient;

    /**
     * @var bool
     */
    private $isPreview;

    /**
     * Create a new message instance.
     * @param  User  $sender
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
     * @param  User  $recipient
     */
    public function setRecipient(User $recipient): void
    {
        $this->recipient = $recipient;
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
            ->view('emails.referral.share_referral_access_for_existed_user')
            ->with([
                'referralLink' => $this->referralLink,
                'isPreview' => $this->isPreview,
                'sender' => $this->sender,
                'duration' => $this->duration
            ]);
    }
}
