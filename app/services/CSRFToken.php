<?php

namespace app\services;

class CSRFToken
{
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