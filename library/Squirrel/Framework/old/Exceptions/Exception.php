<?php namespace Squirrel\Exceptions;

use ReflectionClass;
use Squirrel\Types\String;

/**
 * Main class for all exceptions,
 * provides some object functionnalities
 * and a default behavior for
 * initialization function.
 *
 * @package Squirrel
 * @author  Valérian
 */
class Exception extends \Exception
{
    /**
     * Instanciates a new exception with given arguments.
     *
     * @return Squirrel\Exception new exception
     */
    public static function factory()
    {
        // Reflect current class
        $reflection = new ReflectionClass(get_called_class());

        // Make a new instance
        return $reflection->newInstanceArgs(func_get_args());
    }

    /**
     * Initializes class with message, bindings
     * and optional error code.
     *
     * @param  string message
     * @param  array  parameters
     * @param  int    code
     */
    public function __construct($message, array $params = null, $code = 0)
    {
        if (isset($params))
        {
            // Compile message
            $message = (string) String::cast($message)->compile($params);
        }

        parent::__construct($message, $code);
    }
}
