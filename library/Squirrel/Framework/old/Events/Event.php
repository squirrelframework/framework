<?php namespace Squirrel\Events;

use Squirrel\Types\Object;

/**
 * Event class to be passed in event emissions.
 *
 * @package Squirrel
 * @author  Valérian
 */
class Event extends Object
{
    /**
     * @var Emitter
     */
    public $target;

    /**
     * @var array
     */
    public $data;

    /**
     * @var bool
     */
    public $prevented;

    /**
     * Initializes properties.
     *
     * @param array event data
     */
    public function __construct()
    {
        $this->prevented = false;
    }
}
