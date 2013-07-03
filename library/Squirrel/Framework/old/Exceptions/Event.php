<?php namespace Squirrel\Exceptions;

/**
 * Event class for exception handling.
 *
 * @package Squirrel
 * @author  Valérian
 */
class Event extends \Squirrel\Events\Event
{
    /**
     * @param Exception
     */
    public function __construct(\Exception $exception)
    {
        parent::__construct();
        $this->exception = $exception;
    }
}
