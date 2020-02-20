<?php


namespace app\models;


use app\services\Auth;
use app\services\CSRFToken;
use app\services\Mail;
use app\services\View;

class User extends Manager
{
    protected $password_reset_token;

    public function addUser($data)
    {
        $this->db->query('INSERT INTO users (first_name, last_name, password, email, role)
                              VALUES(:first_name, :last_name, :password, :email, :role)');
        $this->db->bind(':first_name', $data['first_name']);
        $this->db->bind(':last_name', $data['last_name']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':email', $data['email']);

        //execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function findByEmail($email)
    {
        $this->db->query('SELECT * FROM users WHERE email = :email');

        $this->db->bind(':email', $email);

        $row = $this->db->single();

        //check row
        if ($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }

    }

    public function findById($id)
    {
        $this->db->query('SELECT * FROM users WHERE id = :id');

        $this->db->bind(':id', $id);

        $row = $this->db->single();

        //check row
        if ($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }

    }


    public function login($email, $password)
    {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();
        $password_db = $row->password;
        if (password_verify($password, $password_db)) {
            return $row;
        } else {
            return false;
        }
    }

    public function sendPasswordReset($email)
    {
        $user = $this->findByEmail($email);

        $userX = new User();

        if ($user) {
            if($userX->startPasswordReset($email)){
                $this->sendPasswordResetEmail($email);
            }
        }

    }

    protected function startPasswordReset($email)
    {
        $token = new CSRFToken();
        $hased_token = $token->getTokenHash();
        $this->password_reset_token = $token->getTokenValue();
        $user = $this->findByEmail($email);

        $expiry_timestamp = time() + 60 * 60 * 24 * 30;

        $this->db->query('UPDATE users
        SET pass_reset_hash = :token_hash,
        pass_reset_exp = :expires_at
        WHERE id =:id');

        $this->db->bind(':token_hash', $hased_token);
        $this->db->bind(':id', $user->id);
        $this->db->bind(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp));

        return $this->db->execute();
    }

    protected function sendPasswordResetEmail($email) {
        $token = new CSRFToken();
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/index.php?action=requestReset/' . $this->password_reset_token;

        $text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
        $html = View::getTemplate('Password/reset_email.html', ['url' => $url]);

        Mail::send($email, 'Incognito', 'Votre mot de passe', $text, $html);

    }

}