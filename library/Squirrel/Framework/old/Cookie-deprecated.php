<?php namespace Squirrel;

class Cookie
{
    public static function set($key, $value)
    {
        $key   = Encryption::md5($key);
        $value = serialize($value);
        $value = Encryption::md5($key . $value) . '; ' . $value;
        $value = Encryption::encode($value);

        setcookie($key, $value, 0, '', '', FALSE, TRUE);
        $_COOKIE[$key] = $value;
    }

    public static function get($key, $default = NULL)
    {
        $key = Encryption::md5($key);

        if (!isset($_COOKIE[$key]))
        {
            return $default;
        }

        $cookie = explode('; ', Encryption::decode($_COOKIE[$key]));

        if (count($cookie) !== 2)
        {
            setcookie($key, NULL);
            unset($_COOKIE[$key]);

            return $default;
        }

        if ($cookie[0] !== Encryption::md5($key . $cookie[1]))
        {
            setcookie($key, NULL);
            unset($_COOKIE[$key]);

            return $default;
        }

        try
        {
            return unserialize($cookie[1]);
        }
        catch (Exception $exception)
        {
            setcookie($key, NULL);
            unset($_COOKIE[$key]);

            return $default;
        }
    }

    public static function delete($key)
    {
        $key = Encryption::md5($key);

        setcookie($key, NULL);
        unset($_COOKIE[$key]);
    }

    public static function clear()
    {
        foreach ($_COOKIE as $key => $value)
        {
            setcookie($key, NULL);
            unset($_COOKIE[$key]);
        }
    }
}
