<?php namespace Squirrel\HTTP;

use Squirrel\Types\Object;

class Router extends Object {
    protected $routes;

    public function __construct() {
        $this->routes = array();
    }

    public function set($name, Route $route) {
        $this->routes[$name] = $route;
    }

    public function get($name) {
        if (isset($this->routes[$name])) {
            return $this->routes[$name];
        }

        return null;
    }

    public function find($url) {
        foreach ($this->routes as $route) {
            if ($route->match($url)) {
                return $route;
            }
        }

        return null;
    }
}
