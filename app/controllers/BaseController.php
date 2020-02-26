<?php

namespace app\controllers;

use app\models\Post;
use app\services\Auth;
use app\services\Mail;
use app\services\Redirect;
use app\services\View;


class BaseController
{

    private $postModel;


    public function __construct()
    {

        $this->postModel = new Post();

    }

    public function home()
    {
        $posts = $this->postModel->getPosts();

        View::renderTemplate('home.html.twig', ['posts' => $posts]);
    }

    public function showRegisterForm()
    {
        View::renderTemplate('register.html.twig', []);
    }

    public function showLoginForm()
    {
        //init data
        $data = [
            'email' => '',
            'email_err' => '',
            'password' => '',
            'password_err' => ''
        ];

        //load view
        global $twig;
        $vue = $twig->load('admin.login.html.twig');
        echo $vue->render($data);

    }

    public function contact()
    {
        $contact_message = flash('contact_message');
        $message_contact = <<<EOD
                    $contact_message
EOD;

        global $twig;
        $vue = $twig->load('contact.html.twig');
        echo $vue->render([
            'titre' => "salut",
            'contact_message' => $message_contact
        ]);

    }

    public function sendMail()
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

            // Send the message
            $result = $mailer->send($message);
            header('Location: index.php?action=contact');
            flash('contact_message', 'Message envoyee avec succès');

        } else {
            die('error');
        }
    }



}