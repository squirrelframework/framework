<?php namespace Squirrel\Console;

use Squirrel\System;

/**
 * Base class for all console controllers.
 *
 * @package Squirrel
 * @author  Valérian
 */
abstract class Controller extends \Squirrel\Controller
{
    /**
     * @var Squirrel\Console\Process
     */
    protected $process;

    /**
     * Sets properties.
     *
     * @param Squirrel\Console\Process
     */
    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    /**
     * Main function for application,
     * returns UNIX status.
     *
     * @return int|void UNIX status
     */
    public abstract function main();

    /**
     * Outputs given string and optional new line.
     *
     * @param  string
     * @return $this
     */
    protected function out($data, $line = true)
    {
        static $isWindows;

        if (!isset($isWindows))
        {
            $isWindows = System::instance()->getOS() === System::OS_WINDOWS;
        }

        if ($isWindows)
        {
            // Encode in Windows console format
            $data = iconv('UTF-8', 'CP850', $data);
        }

        echo $data;

        if ($line)
        {
            // Echo final line
            echo PHP_EOL;
        }

        return $this;
    }
}
