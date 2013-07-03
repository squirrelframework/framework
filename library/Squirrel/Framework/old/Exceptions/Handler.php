<?php namespace Squirrel\Exceptions;

use Squirrel\Squirrel;
use Squirrel\Types\Object;
use ErrorException;

/**
 * Basic class to handle PHP errors
 * and exceptions in specific output.
 *
 * Can be extended for specific handling.
 *
 * @package  Squirrel
 * @author   Valérian
 */
class Handler extends Object
{
    /**
     * Main handler for all exceptions and errors.
     *
     * @param  Exception
     * @return void
     */
    public function exception(\Exception $exception)
    {
        $event = new Event($exception);
        
        // Emit exception
        Squirrel::instance()->emit('exception', $event);

        if ($event->prevented)
        {
            // Exception was prevented
            exit;
        }

        // Display exception
        echo $exception;
    }

    /**
     * Handler for PHP error event, converts error in exception
     * and let it propagate so other scripts can catch
     * errors in a try catch block.
     *
     * @param  int    code
     * @param  string message
     * @param  string file
     * @param  int    line
     * @return void
     */
    public function error($code, $message, $file, $line)
    {
        throw new ErrorException($message, $code, 0, $file, $line);
    }

    /**
     * Handler for PHP shotdown event,
     * handle last error if exists.
     *
     * @return void
     */
    public function shutdown()
    {
        if (($error = error_get_last()) !== null)
        {
            $this->exception(new ErrorException(
                $error['message'], $error['type'], 0,
                $error['file'], $error['line']
            ));
        }
    }
}
