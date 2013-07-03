<?php namespace Squirrel\HTTP;

use Squirrel\Exceptions\Event;
use Squirrel\Events\Emitter;
use Squirrel\Squirrel;
use Squirrel\FileSystem\File;
use Squirrel\View;
use Squirrel\Config;
use ErrorException;

/**
 * Singleton class to handle some PHP
 * error events, providing a debug error page.
 *
 * @package  Squirrel
 * @author   Valérian
 */
class Handler extends \Squirrel\Exceptions\Handler
{
    /**
     * @var bool debug mode
     */
    protected $debug;

    /**
     * @var bool already emitted error
     */
    protected $emitted;

    /**
     * @var bool already shown debug page
     */
    protected $debugged;

    /**
     * Initialize properties.
     */
    public function __construct()
    {
        $environment    = Squirrel::instance()->getEnvironment();
        $this->debug    = $environment === Squirrel::ENVIRONMENT_DEVELOPMENT;
        $this->emitted  = false;
        $this->debugged = false;
    }

    /**
     * Triggers error in core class or shows debugger.
     *
     * @see Squirrel\Handler
     */
    public function exception(\Exception $exception)
    {
        // Clean output buffer
        $this->clean();

        try
        {
            if (!$this->emitted)
            {
                // Emit once
                $this->emitted = true;

                // Prepare event
                $event = new Event($exception);

                // Emit exception
                Squirrel::instance()->emit('exception', $event);

                if ($event->prevented)
                {
                    // Exception was prevented
                    exit;
                }
            }

            if ($this->debug && !$this->debugged)
            {
                // Show debugger
                $this->debugged = true;
                $this->debug($exception);
                exit;
            }

            // This is going really wrong
            throw $exception;
        }
        catch (Exception $exception)
        {
            $this->clean();

            $body = $this->debug ? $exception
                  : Config::instance('http/statuses')->get(500);

            echo Response::factory()
                ->setStatus(500)
                ->setHeader('Content-Type', File::mimeByExtension('txt'))
                ->setBody($body)
                ->sendHeaders();

            exit;
        }
    }

    /**
     * Cleans previously opened output buffers
     * and starts a clean buffer.
     *
     * @return void
     */
    protected function clean()
    {
        while (ob_get_level() > 0)
        {
            ob_end_clean();
        }

        ob_start();
    }

    /**
     * Shows core debug page with stack trace.
     *
     * @param  Exception
     * @return void
     */
    protected function debug(\Exception $exception)
    {
        // Deduce exception status
        $status = $exception instanceof Exception
                ? $exception->getCode()
                : 500;

        // Get some data
        $type    = get_class($exception);
        $code    = $exception->getCode();
        $message = $exception->getMessage();
        $file    = $exception->getFile();
        $line    = $exception->getLine();
        $rows    = $this->rows($file, $line);
        $calls   = array();

        if ($exception instanceof ErrorException)
        {
            // Get corresponding error constant
            $code = Config::instance('php/errors')->get($code, $code);
        }

        // Debug file path
        $file = File::cast($file)->debug();

        foreach ($exception->getTrace() as $trace)
        {
            $call = array();

            if (isset($trace['file'], $trace['line']))
            {
                // Push file informations
                $call['file'] = File::cast($trace['file'])->debug();
                $call['line'] = $trace['line'];
                $call['rows'] = $this->rows($trace['file'], $trace['line']);
            }

            if (isset($trace['function']))
            {
                if (isset($trace['class'], $trace['type']))
                {
                    // Method on class
                    $call['class']    = $trace['class'];
                    $call['operator'] = $trace['type'];
                    $call['method']   = $trace['function'];
                }
                else
                {
                    // Simple function
                    $call['function'] = $trace['function'];
                }
            }

            if (isset($trace['args']) && count($trace['args']))
            {
                // Push function arguments
                $call['arguments'] = $trace['args'];
            }

            $calls[] = $call;
        }

        $view = View::factory('squirrel/exception')
            ->bind('type',    $type)
            ->bind('code',    $code)
            ->bind('message', $message)
            ->bind('file',    $file)
            ->bind('line',    $line)
            ->bind('rows',    $rows)
            ->bind('calls',   $calls);

        echo Response::factory()
            ->setStatus($status)
            ->setBody($view)
            ->sendHeaders();
    }

    /**
     * Get file lines surrounding error line.
     *
     * @param  string file
     * @param  int    line
     * @return array  rows
     */
    protected function rows($file, $line)
    {
        $lines   = file($file, FILE_IGNORE_NEW_LINES);
        $lines[] = '';
        $rows    = array();
        $length  = 11;
        $offset  = $line - 6;

        if ($offset < 0)
        {
            $length += $offset;
            $offset  = 0;
        }

        for ($i = $offset; $i < $offset + $length; $i++)
        {
            if (isset($lines[$i]))
            {
                $rows[] = array('line' => $i + 1, 'content' => $lines[$i]);
            }
        }

        return $rows;
    }
}
