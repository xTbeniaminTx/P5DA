<?php

namespace app\controllers;

class SecurityController extends AbstractController
{
    //------------------------------------------------------------------------------------------------------------------
    public function __construct()
    {
        $this->loginModel = $this->model('Login');
        $this->chapterModel = $this->model('Chapter');
        $this->commentModel = $this->model('Comment');
    }

    //------------------------------------------------------------------------------------------------------------------

    public function createSession($login)
    {
        $_SESSION['admin_id'] = $login->id;
        $_SESSION['admin_email'] = $login->email;
        header('Location: index.php?action=adminChapters');
    }

    //------------------------------------------------------------------------------------------------------------------

    public function adminLogin()
    {
        if ($this->isLoggedIn()) {
            header('Location: index.php?action=adminChapters');
        } else {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                //process form
                //Sanitaze POST data
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                //init data
                $data = [
                    'email' => trim($_POST['email']),
                    'email_err' => '',
                    'password' => trim($_POST['password']),
                    'password_err' => ''
                ];


                //check if email is entered
                if (empty($data['email'])) {
                    $data['email_err'] = 'Veuillez entre votre email';
                }

                //check if password is entered
                if (empty($data['password'])) {
                    $data['password_err'] = 'Veuillez entre votre mot de passe';
                }

                //check for email
                if ($this->loginModel->findByEmail($data['email'])) {
                    //email found
                } else {
                    $data['email_err'] = 'Aucun utilisateur trouvé';
                }

                //make sure errors are empty
                if (empty($data['email_err']) && empty($data['password_err'])) {
                    //validated
                    //check and set logged user
                    $loggedInAdmin = $this->loginModel->login($data['email'], $data['password']);

                    if ($loggedInAdmin) {
                        //create session
                        $this->createSession($loggedInAdmin);
                    } else {
                        $data['password_err'] = 'Mot de passe invalide';
                        //load view with errors
                        global $twig;
                        $vue = $twig->load('admin.login.html.twig');
                        echo $vue->render($data);
                    }
                } else {
                    //load view with errors
                    global $twig;
                    $vue = $twig->load('admin.login.html.twig');
                    echo $vue->render($data);
                }

            } else {
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
        }

    }

    //------------------------------------------------------------------------------------------------------------------

    public function home()
    {
        $chapters = $this->chapterModel->getChapters();

        global $twig;
        $vue = $twig->load('home.html.twig');
        echo $vue->render([
            'chapters' => $chapters,
        ]);

    }

    //------------------------------------------------------------------------------------------------------------------


    public function register()
    {
        $chapters = $this->chapterModel->getChapters();

        global $twig;
        $vue = $twig->load('register.html.twig');
        echo $vue->render([
            'chapters' => $chapters,
        ]);

    }

    //------------------------------------------------------------------------------------------------------------------

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

    //------------------------------------------------------------------------------------------------------------------

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

    //------------------------------------------------------------------------------------------------------------------

    public function chapters()
    {
        $chapters = $this->chapterModel->getChapters();
        $photoId = rand(10, 50);

        $data = [
            'title' => "Admin Chapters",
            'chapters' => $chapters,
            'photoId' => $photoId
        ];
        global $twig;
        $vue = $twig->load('chapters.html.twig');
        echo $vue->render($data);
    }

    //------------------------------------------------------------------------------------------------------------------

    public function showChapter()
    {

        $comment_message = flash('comment_message');
        $message_comment = <<<EOD
                    $comment_message
EOD;

        //comment add
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {


            //Sanitize the post
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            if (isset($_GET['id'])) {
                $chapter = $this->chapterModel->getChaptersById($_GET['id']);
            }
            $comments = $this->commentModel->getComments();
            $commentsById = $this->commentModel->getCommentsById($_GET['id']);


            $data = [
                'comment_author' => trim($_POST['comment_author']),
                'comment_email' => trim($_POST['comment_email']),
                'comment_content' => trim($_POST['comment_content']),
                'comment_date' => date('Y-m-d H:i:s'),
                'comment_status' => 'newComment',
                'comment_author_err' => null,
                'comment_email_err' => null,
                'comment_content_err' => null,
                'chapter' => $chapter,
                'comment_chapter_id' => $chapter->id,
                'comments' => $comments,
                'commentsById' => $commentsById,
            ];

            //Validate data
            if (empty($data['comment_author'])) {
                $data['comment_author_err'] = 'Veuillez entre un author';
            }
            if (empty($data['comment_email'])) {
                $data['comment_email_err'] = 'Veuillez entre un mail valid';
            }
            if (empty($data['comment_content'])) {
                $data['comment_content_err'] = 'Veuillez entre un contenu pour votre commentaire';
            }

            //make sure errors are empty
            if (empty($data['comment_author_err']) && empty($data['comment_email_err']) && empty($data['comment_content_err'])) {
                //validated
                if ($this->commentModel->addComment($data)) {
                    header('Location: index.php?action=showChapter&id=' . $_GET['id']);
                    flash('comment_message', 'Nouveau commentaire ajouté avec succès');
                } else {
                    die('Impossible de traiter cette demande à l\'heure actuelle.');
                }

            } else {
                //load view with errors
                global $twig;
                $vue = $twig->load('chapter.html.twig');
                echo $vue->render($data);
            }
        } else {
            $chapters = $this->chapterModel->getChapters();
            $chapter = $this->chapterModel->getChaptersById($_GET['id']);
            $comments = $this->commentModel->getComments();
            $commentsById = $this->commentModel->getCommentsById($_GET['id']);
            $photoId = rand(10, 50);
            $adminLogged = isset($_SESSION['admin_id']) ? true : false;

            $data = [
                'adminLogged' => $adminLogged,
                'comment_message' => $message_comment,
                'chapter' => $chapter,
                'chapters' => $chapters,
                'comments' => $comments,
                'id' => 10 + rand(10, 50),
                'photoId' => $photoId,
                'commentsById' => $commentsById,
                'comment_date' => date('Y-m-d H:i:s'),


            ];
            global $twig;
            $vue = $twig->load('chapter.html.twig');
            echo $vue->render($data);
        }
    }

    //------------------------------------------------------------------------------------------------------------------

    public function unapprouve()
    {
        $id = $_GET['comment_id'];
        $idChapter = $_GET['id'];


        if ($this->commentModel->unapprouveStatus($id)) {
            if ($this->isLoggedIn()) {
                header('Location: index.php?action=adminComments');
                flash('comment_message', 'Le commentaire a été désapprouvé');
            } else {
                header('Location: index.php?action=showChapter&id=' . $idChapter);
                flash('comment_message', 'Le commentaire a été désapprouvé');
            }

        } else {
            die('Impossible de traiter cette demande à l\'heure actuelle.');
        }

    }


    //------------------------------------------------------------------------------------------------------------------

    public function isLoggedIn()
    {
        if (isset($_SESSION['admin_id'])) {
            return true;
        } else {
            return false;
        }
    }
    //------------------------------------------------------------------------------------------------------------------
}