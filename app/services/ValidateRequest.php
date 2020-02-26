<?php

namespace app\services;

class ValidateRequest
{

    private static $db;

    public function __construct()
    {
        self::$db = new Database();
    }

    private static $error = [];
    private static $error_messages = [
        'string' => 'Le champ :attribute peut contenir que des lettres',
        'uniqueEmail' => 'Le champ :attribute est déjà utilisé',
        'required' => 'Le champ :attribute est obligatoire',
        'minLength' => 'Le champ :attribute doit avoir minimum :policy caractères',
        'maxLength' => 'Le champ :attribute doit avoir maximum of :policy caractères',
        'mixed' => 'Le champ :attribute peut contenir que des lettres, des chiffres, -- et des espaces',
        'number' => 'Le champ :attribute peut contenir des chiffres',
        'email' => 'Le champ :attribute n\'est pas valide'
    ];

    /**
     * @param array $dataAndValues , column and value to validate
     * @param array $policies , the rules that validation must satisfy
     */
    public function abide(array $dataAndValues, array $policies)
    {
        foreach ($dataAndValues as $column => $value) {
            if (in_array($column, array_keys($policies))) {
                self::doValidation(
                    ['column' => $column, 'value' => $value, 'policies' => $policies[$column]]
                );
            }
        }
    }

    /**
     * Perform validation for the data provider and set error messages
     * @param array $data
     */
    private static function doValidation(array $data)
    {
        $column = $data['column'];
        foreach ($data['policies'] as $rule => $policy) {
            $valid = call_user_func_array([self::class, $rule], [$column, $data['value'], $policy]);
            if (!$valid) {
                self::setError(
                    str_replace(
                        [':attribute', ':policy', '_'],
                        [$column, $policy, ' '],
                        self::$error_messages[$rule]), $column
                );
            }
        }
    }


    protected static function uniqueEmail($column, $value, $policy)
    {
        self::$db->query('SELECT * FROM users WHERE email = :email');
        self::$db->bind(':email', $value);

        $row = self::$db->single();

        $user = Auth::getUser();

        if ($user) {
            if ($user->email == $value) {
                return $row;
            }
        }
        return !$row;
    }

    protected static function required($column, $value, $policy)
    {
        return $value != null && !empty(trim($value));
    }

    protected static function minLength($column, $value, $policy)
    {
        if ($value != null && !empty(trim($value))) {
            return strlen($value) >= $policy;
        }
        return true;
    }

    protected static function maxLength($column, $value, $policy)
    {
        if ($value != null && !empty(trim($value))) {
            return strlen($value) <= $policy;
        }
        return true;
    }

    protected static function email($column, $value, $policy)
    {
        if ($value != null && !empty(trim($value))) {
            return filter_var($value, FILTER_VALIDATE_EMAIL);
        }
        return true;
    }

    protected static function mixed($column, $value, $policy)
    {
        if ($value != null && !empty(trim($value))) {
            if (!preg_match('/^[A-Za-z0-9 .,_~\-!@#\&%\^\'\*\(\)]+$/', $value)) {
                return false;
            }
        }
        return true;
    }

    protected static function string($column, $value, $policy)
    {
        if ($value != null && !empty(trim($value))) {
            if (!preg_match('/^[A-Za-z ]+$/', $value)) {
                return false;
            }
        }
        return true;
    }

    protected static function number($column, $value, $policy)
    {
        if ($value != null && !empty(trim($value))) {
            if (!preg_match('/^[0-9.]+$/', $value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Set specific error
     * @param $error
     * @param null $key
     */
    private static function setError($error, $key = null)
    {
        if ($key) {
            self::$error[$key][] = $error;
        } else {
            self::$error[] = $error;
        }
    }

    /**
     * return true if there is validation error
     * @return bool
     */
    public function hasError()
    {
        return count(self::$error) > 0 ? true : false;
    }

    /**
     * Return all validation errors
     * @return array
     */
    public function getErrorMessages()
    {
        return self::$error;
    }

}