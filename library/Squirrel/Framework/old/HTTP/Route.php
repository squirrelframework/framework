<?php namespace Squirrel\HTTP;

class Route
{
    public static function factory($pattern, array $custom = array())
    {
        return new self($pattern, $custom);
    }

    private $pattern;
    private $custom;
    private $defaults;
    private $regex;
    private $keys;

    private function makeRegex()
    {
        $regex = str_replace('/', '\/', $this->pattern);
        $regex = str_replace('(', '(?:', $regex);
        $regex = str_replace(')', ')?', $regex);

        while (preg_match('/<([a-z0-9]+)>/', $regex, $matches))
        {
            if (isset($this->custom[$matches[1]]))
            {
                $replace = '(' . $this->custom[$matches[1]] . ')';
            }
            else
            {
                $replace = '([a-z0-9]+)';
            }

            $regex = str_replace($matches[0], $replace, $regex);
        }

        $this->regex = '/^' . $regex . '$/';
    }

    private function makeKeys()
    {
        preg_match_all('/<([a-z0-9]+)>/', $this->pattern, $matches);
        $this->keys = $matches[1];
    }

    private function params($matches)
    {
        $params = $this->defaults;

        for ($i = 0; $i < count($this->keys); $i++)
        {
            if (isset($matches[$i + 1]))
            {
                $params[$this->keys[$i]] = $matches[$i + 1];
            }
        }

        return $params;
    }

    public function __construct($pattern, array $custom = array())
    {
        $this->pattern  = $pattern;
        $this->custom   = $custom;
        $this->defaults = array();

        $this->makeRegex();
        $this->makeKeys();
    }

    public function defaults(array $defaults)
    {
        $this->defaults = $defaults;
        return $this;
    }

    public function match($url)
    {
        if (preg_match($this->regex, $url, $matches))
        {
            return $this->params($matches);
        }

        return FALSE;
    }

    public function url(array $params = array())
    {
        $url = $this->pattern;

        while (preg_match('/\([^()]+\)/', $url, $matches))
        {
            $search  = $matches[0];
            $replace = substr($matches[0], 1, -1);

            while (preg_match('/<[a-z0-9]+>/', $replace, $matches))
            {
                $param = $matches[0];
                $key   = substr($param, 1, -1);

                if (isset($params[$key]) AND !(isset($this->defaults[$key]) AND $params[$key] === $this->defaults[$key]))
                {
                    $replace = str_replace($param, $params[$key], $replace);
                }
                else
                {
                    $replace = '';
                }
            }

            $url = str_replace($search, $replace, $url);
        }

        while (preg_match('/<[a-z0-9]+>/', $url, $matches))
        {
            $param = $matches[0];
            $key   = substr($param, 1, -1);

            if (!isset($params[$key]))
            {
                throw new \Exception(sprintf('Param "%s" is required to build the url corresponding to this route', $key));
            }

            $url = str_replace($param, $params[$key], $url);
        }

        return URL::base($url);
    }
}
