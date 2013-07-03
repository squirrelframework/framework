<?php namespace Squirrel\Console;

use Squirrel\Types\Object;

/**
 * Abstract class allowing to manipulate
 * console processes with arguments.
 *
 * @package Squirrel
 * @author  Valérian
 */
abstract class Process extends Object
{
    /**
     * @var string script name
     */
    protected $name;

    /**
     * @var array arguments
     */
    protected $arguments;

    /**
     * Initializes all class properties with default values
     * or context values if given.
     *
     * @param string name
     * @param array  arguments
     */
    public function __construct($name, array $arguments = array())
    {
        $this->setName($name);
        $this->setArguments($arguments);
    }

    /**
     * @param  string name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  array arguments
     * @return $this
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * @return array arguments
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Executes current process with arguments.
     *
     * @return void
     */
    public abstract function execute();
}
