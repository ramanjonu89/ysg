<?php
namespace App\Notifications;

use Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationNotification extends Notification
{
    use Queueable;
    protected $user;
    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail()
    {
        $user = Auth::user();
        if(!empty($this->user)) {
            $user = $this->user;
        } 

        $user->verification_code = encrypt($user->id);
        $user->save();
        $array['view'] = 'emails.verification';
        // $array['subject'] = __('Email Verification');
        $array['subject'] = $user->name;
        $array['content'] = 'Please click the button below to verify your email address.';
        $array['link'] = route('email.verification.confirmation', $user->verification_code);

        return (new MailMessage)
            ->view('emails.verification', ['array' => $array])
            ->subject(__('Email Verification - ') . env('APP_NAME'));
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}