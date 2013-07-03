<?php namespace Squirrel\Console\Process;

use Squirrel\Console\Process;
use Squirrel\Console\Controller;
use Squirrel\Console\Exception;

/**
 * Internal process driver, able to
 * simulate nested internal processes,
 * calling a controller with an action
 * found using routing classes.
 *
 * @package Squirrel
 * @author  Valérian
 */
class Internal extends Process
{
    /**
     * Finds main console controller and
     * executes it.
     *
     * @see Squirrel\Console\Process
     */
    public function execute()
    {

        if (Controller::isChild('Squirrel\\Controller\\Console\\Main'))
        {
            $name = 'Squirrel\\Controller\\Console\\Main';
        }
        else if (Controller::isChild('Squirrel\\Controller\\Main'))
        {
            $name = 'Squirrel\\Controller\\Main';
        }
        else
        {
            $message = 'Main controller does not exists '
                     . 'or is not a console controller';

            throw new Exception($message);
        }

        // Instanciate controller
        $controller = new $name($this);

        if ($controller->hasMethod('before'))
        {
            // Call before method
            $controller->before();
        }

        // Call main method and get status
        $status = $controller->main();

        if ($controller->hasMethod('after'))
        {
            // Call after method
            $controller->after();
        }

        // Terminate process
        $this->terminate($status);
    }

    /**
     * Terminates process with given status.
     *
     * @param  int status
     * @return void
     */
    public function terminate($status = null)
    {
        if ($status === null)
        {
            exit;
        }

        exit($status);
    }
}
