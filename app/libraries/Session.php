<?php

namespace app\libraries;

class Session
{

    /**
     * create session
     *
     * @param $name
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    public static function add($name, $value)
    {
        if ($name != '' && !empty($name) && $value != '' && !empty($value)) {
            return $_SESSION[$name] = $value;
        }

        throw new \Exception('Name and value required');
    }

    /**
     * get session name
     *
     * @param $name
     * @return mixed
     */
    public static function get($name)
    {
        return $_SESSION[$name];
    }

    /**
     * check if session exists
     *
     * @param $name
     * @return bool
     * @throws \Exception
     */
    public static function has($name)
    {
        if ($name != '' && !empty($name)) {
            return (isset($_SESSION[$name]) ? true : false);
        }

        throw new \Exception('name is required');
    }

    /**
     * remove the session
     *
     * @param $name
     */
    public static function remove($name)
    {
        try {
            if (self::has($name)) {
                unset($_SESSION[$name]);
            }
        } catch (\Exception $e) {
        }
    }
}