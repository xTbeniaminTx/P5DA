<?php


namespace app\services;

use Mailgun\Mailgun;

class Mail
{
    public static function send($to, $subject, $text)
    {

        $mgClient = Mailgun::create('58c3857dff81fe22fb83c0dfeb969b1f-52b6835e-00ee31b6', 'https://api.mailgun.net/v3/sandbox59b38d41a61d40eea897194f451ff653.mailgun.org');
        $domain = 'https://app.mailgun.com/app/sending/domains/sandbox59b38d41a61d40eea897194f451ff653.mailgun.org';
        # Make the call to the client.
        $result = $mgClient->sendMessage($domain, array(
            'from'	=> 'Excited User <mailgun@YOUR_DOMAIN_NAME>',
            'to'	=> $to,
            'subject' => $subject,
            'text'	=> $text
        ));
    }

}