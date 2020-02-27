<?php


namespace app\models;


use app\services\Auth;
use app\services\CSRFToken;
use app\services\Mail;
use app\services\ValidateRequest;
use app\services\View;

class User extends Manager
{


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

    public function updateUser($data)
    {

        $this->db->query('UPDATE users 
                                SET first_name = :first_name,
                                    last_name =:last_name, 
                                    password = :password, 
                                    email = :email,
                                    user_photo_path = :user_photo_path, 
                                    role = :role
                                WHERE id = :id');
        $this->db->bind(':first_name', $data['first_name']);
        $this->db->bind(':last_name', $data['last_name']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':user_photo_path', $data['user_photo_path']);
        $this->db->bind(':id', $data['id']);

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

        if (false === $user) {
            return;
        }

        if ($this->startPasswordReset($email)) {
            $this->sendPasswordResetEmail($email);
        }
    }

    protected function startPasswordReset($email)
    {

        $hased_token = $this->token->getTokenHash();

        $user = $this->findByEmail($email);

        $expiry_timestamp = time() + 60 * 60;

        $this->db->query('UPDATE users
        SET pass_reset_hash = :token_hash,
            pass_reset_exp = :expires_at
        WHERE id =:id');

        $this->db->bind(':token_hash', $hased_token);
        $this->db->bind(':id', $user->id);
        $this->db->bind(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp));

        return $this->db->execute();
    }

    protected function sendPasswordResetEmail($email)
    {

        $user = $this->findByEmail($email);

        $password_reset_token = $this->token->getTokenValue();
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/index.php?action=resetPass&token=' . $password_reset_token . '&email=' . $email;


        $text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
        $html = View::getTemplate('Password/reset_email.html', ['url' => $url]);

        Mail::send($email, $user->first_name, 'Votre mot de passe', $text, $html);

    }

    public function findByPasswordReset($token)
    {
        $tokenValue = new CSRFToken($token);

        $hashed_token = $tokenValue->getTokenHash();

        $this->db->query('SELECT * FROM users WHERE pass_reset_hash = :token_hash');

        $this->db->bind(':token_hash', $hashed_token);

        $row = $this->db->single();

        //check row
        if ($this->db->rowCount() > 0) {
            if (strtotime($row->pass_reset_exp) > time()) {

                return $row;
            }
        }

        return false;

    }

    public function resetPassword($password, $user)
    {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $this->db->query
        ('UPDATE users
                SET password = :password,
                    pass_reset_hash = NULL,
                    pass_reset_exp = NULL
                WHERE id =:id');

        $this->db->bind(':password', $password_hash);
        $this->db->bind(':id', $user->id);

        return $this->db->execute();
    }

}