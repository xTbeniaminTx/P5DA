<?php

namespace app\services;

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

    public static function view($name, $data)
    {
        global $twig;
        $vue = $twig->load($name);
        echo $vue->render($data);

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

    /**
     * // Flash message helper
     * // EXAMPLE - flash('register_success', 'You are now registered');
     * // DISPLAY IN VIEW - echo flash('register_success');
     *
     * @param string $name
     * @param string $message
     * @param string $class
     */
    public static function flash($name = '', $message = '', $class = 'h3 alert alert-success text-center alert-dismissible fade show')
    {
        if (!empty($name)) {
            if (!empty($message) && empty($_SESSION[$name])) {
                if (!empty($_SESSION[$name])) {
                    unset($_SESSION[$name]);
                }

                if (!empty($_SESSION[$name . '_class'])) {
                    unset($_SESSION[$name . '_class']);
                }

                $_SESSION[$name] = $message;
                $_SESSION[$name . '_class'] = $class;
            } elseif (empty($message) && !empty($_SESSION[$name])) {
                $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
                echo '<section><div class="' . $class . '" id="msg-flash" role="alert">' . $_SESSION[$name] . '<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button></div></section>';
                unset($_SESSION[$name]);
                unset($_SESSION[$name . '_class']);
            }
        }
    }
}