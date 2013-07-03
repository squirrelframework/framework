<?php namespace Squirrel\Events;

use Squirrel\Types\Object;
use Squirrel\Types\Callback;

/**
 * Provides event interface with multiple callbacks.
 *
 * @package Squirrel
 * @author  Valérian
 */
abstract class Emitter extends Object
{
    /**
     * @var array
     */
    protected $listeners;

    /**
     * Prepares listeners array.
     */
    public function __construct()
    {
        $this->listeners = array();
    }

    /**
     * Adds given callback for given event type.
     *
     * @param  string event type
     * @param  mixed  callback
     * @return $this
     */
    public function on($type, $callback, array $data = null)
    {
        if (!isset($this->listeners[$type]))
        {
            // Init event's array
            $this->listeners[$type] = array();
        }

        // Append callback and data to event's array
        $this->listeners[$type][] = array(
            'callback' => Callback::cast($callback),
            'data'     => $data
        );
        
        return $this;
    }

    /**
     * Fires all callbacks of given event type.
     *
     * @param  string event type
     * @param  Event
     * @return $this
     */
    public function emit($type, Event & $event = null)
    {
        if (!isset($this->listeners[$type]))
        {
            // Nothing to do
            return $this;
        }

        // Set event target
        $event->target = $this;

        foreach ($this->listeners[$type] as $listener)
        {
            // Set event data
            $event->data = $listener['data'];

            // Invoke listener with event
            $listener['callback']->call($event);

            if ($event->prevented)
            {
                // Listener prevented event
                break;
            }
        }

        return $this;
    }
}
