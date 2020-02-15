<?php


namespace app\services;


class Mail
{
    public static function send($to, $to_user_name, $subject, $text, $html)
    {

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("admin@blog.tolan.me", "Blog BT");
        $email->setSubject($subject);
        $email->addTo($to, $to_user_name);
        $email->addContent("text/plain", $text);
        $email->addContent(
            "text/html", $html
        );
        $sendgrid = new \SendGrid(SENDGRID_API_KEY);
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
    }

}