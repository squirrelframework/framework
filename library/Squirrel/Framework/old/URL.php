<?php namespace Squirrel;

class URL
{
    public static function base($url = null)
    {
        static $base = null;

        if ($base === null) {
            if (substr($_SERVER['REQUEST_URI'], 0, strlen($_SERVER['SCRIPT_NAME'])) === $_SERVER['SCRIPT_NAME']) {
                $base = $_SERVER['SCRIPT_NAME'];
            } else {
                $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\\/');
            }
        }

        if ($url === null) {
            return $base;
        }

        return $base . '/' . $url;
    }
}
