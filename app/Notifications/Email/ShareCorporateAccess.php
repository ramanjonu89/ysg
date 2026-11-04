<?php
namespace App\Notifications\Email;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShareCorporateAccess extends Notification
{
    use Queueable;

    private $referralLink = '';
    private $isPreview = false;
    /**
     * @var User
     */
    private $sender;
    /**
     * @var string
     */
    private $duration = '00 hour';

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $sender)
    {
        $this->sender = $sender;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
     * Set referralLink.
     * @param $referralLink
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('You‘ve received an exclusive invitation!'))
            ->view(
                'emails.referral.share_referral_access_for_existed_user',
                [
                    'referralLink' => $this->referralLink,
                    'sender' => $this->sender,
                    'duration' => $this->duration
                ]
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
