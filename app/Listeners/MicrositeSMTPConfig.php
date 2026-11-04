<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSending;

class MicrositeSMTPConfig
{
	public function handle(MessageSending $event)
    {
    	if (isMicrosite()) {

    		$microsite = getMicrosite();

    		if (!empty($microsite->mail_username) && !empty($microsite->mail_password)) {

	            $transport = (new \Swift_SmtpTransport($microsite->mail_host, $microsite->mail_port))
                       ->setUsername($microsite->mail_username)
                       ->setPassword($microsite->mail_password)
                       ->setEncryption($microsite->mail_encryption);

				\Mail::setSwiftMailer(new \Swift_Mailer($transport));
				$event->message->setFrom($microsite->mail_from_address, $microsite->mail_from_name);
    		}
            
        }

    	return true;
    }
}