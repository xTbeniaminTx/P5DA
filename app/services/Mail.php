<?php


namespace app\services;


use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

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
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }

    public static function sendWithSwift()
    {
        if (isset($_POST['btnSubmit'])) {
            //SWIFTMAILER
            // Create the Transport
            $transport = (new Swift_SmtpTransport('smtp.googlemail.com', 465, 'ssl'))
                ->setUsername('beniamin777tolan@gmail.com')
                ->setPassword('rewopi123456');

            // Create the Mailer using your created Transport
            $mailer = new Swift_Mailer($transport);

            // Create a message
            $message = (new Swift_Message('JF Blog Subject'))
                ->setFrom(['noreply@jeanforteroche.me' => 'Blog JF'])
                ->setReplyTo([$_POST['txtEmail'] => $_POST['txtName']])
                ->setTo(['beniamin777tolan@gmail.com' => 'Admin JF'])
                ->setBody('Message: ' . $_POST['txtMsg'])
                ->addPart('<strong>Message:</strong><p> ' . $_POST['txtMsg'] . '</p><br/><strong>Telephone:</strong> ' . $_POST['txtPhone'], 'text/html');


        }
        try {
            // Send the message
            $result = $mailer->send($message);
            header('Location: index.php?action=contact');
            Session::addMessage('Message envoyee avec succès');
            print $result->statusCode() . "\n";
            print_r($result->headers());
            print $result->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }

}