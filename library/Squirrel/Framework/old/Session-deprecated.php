<?php namespace Squirrel;

class Session extends Object {
    public function start() {
        session_start();
        return $this;
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
        return $this;
    }

    public function delete($key) {
        unset($_SESSION[$key]);
        return $this;
    }

    public function get($key, $default = null) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    public function clear() {
        session_destroy();
        return $this;
    }
}
