<?php

namespace app\services;

class CSRFToken
{
    protected $token;

    public function __construct($token_value = null)
    {
        if ($token_value) {
            $this->token = $token_value;
        }
        try {
            $this->token = bin2hex(random_bytes(16));
        } catch (\Exception $e) {
        }
    }


    /**
     * create CSRF token
     *
     * @return mixed
     * @throws \Exception
     */
    public static function _token()
    {
        if (!Session::has('token')) {
            $randomToken = base64_encode(openssl_random_pseudo_bytes(32));
            Session::add('token', $randomToken);
        }

        return Session::get('token');
    }

    public function getTokenValue()
    {
        return $this->token;
    }

    public function getTokenHash()
    {
        return hash_hmac('sha256', $this->token, SECRET_KEY);
    }

    /**
     * verify CSRF TOKEN
     *
     * @param $requestToken
     * @param bool $regenerate
     *
     * @return bool
     */
    public static function verifyCSRFToken(string $requestToken, $regenerate = true)
    {
        if ($requestToken === Session::get('token')) {
            if ($regenerate) {
                Session::remove('token');
            }

            return true;
        }

        return false;
    }

}