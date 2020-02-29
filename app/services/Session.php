<?php

namespace app\services;

class Session
{
    const SUCCESS = "success";
    const INFO = "info";
    const WARNING = "warning";

    /**
     * create session
     *
     * @param  $name
     * @param  $value
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
     *
     * @return string
     */
    public static function get($name): string
    {
        if (false === self::has($name)) {
            return '';
        }

        return $_SESSION[$name];
    }


    /**
     * check if session exists
     *
     * @param  $name
     * @return bool
     */
    public static function has(string $name): bool
    {
        return isset($_SESSION[$name]);
    }

    /**
     * remove the session
     *
     * @param $name
     */
    public static function remove(string $name)
    {
        if (self::has($name)) {
            unset($_SESSION[$name]);
        }
    }


    public static function addMessage($message, $type = 'success')
    {
        if (!isset($_SESSION['flash_notifications'])) {
            $_SESSION['flash_notifications'] = [];
        }

        $_SESSION['flash_notifications'][] = [
            'body' => $message,
            'type' => $type
        ];
    }

    public static function getMessages()
    {
        if (isset($_SESSION['flash_notifications'])) {
            $messages = $_SESSION['flash_notifications'];
            unset($_SESSION['flash_notifications']);

            return $messages;
        }
    }
}
