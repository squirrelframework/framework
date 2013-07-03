<?php namespace Squirrel;

use Squirrel\Types\Object;

class View extends Object {
    protected static $globals = array();

    public static function bindGlobal($name, $value) {
        static::$globals[$name] = $value;
    }

    protected $file;
    protected $vars;

    public function __construct($name) {
        $file = Squirrel::instance()->find('views', $name);

        if ($file === false) {
            throw new Exception(sprintf(
                'Given argument "%s" is not an existing view', $name));
        }

        $this->file = $file;
        $this->vars = static::$globals;
    }

    public function __toString() {
        ob_start();
        extract($this->vars);
        include $this->file;
        return ob_get_clean();
    }

    public function bind($name, $value) {
        $this->vars[$name] = $value;
        return $this;
    }
}
