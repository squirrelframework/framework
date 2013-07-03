<?php namespace Squirrel;

use Squirrel\Types\Collection;

/**
 * Wrapper for configuration files.
 *
 * @package Squirrel
 * @author  Valérian
 */
class Config extends Collection
{
    /**
     * Searchs for config file and gets data.
     *
     * @throws Squirrel\Exception
     * @param  string name
     */
    public function __construct($name)
    {
        // Find config file
        $file = Squirrel::instance()->find('config', $name);

        if (!$file->exists())
        {
            throw new Exception(
                'Unable to find given config file \':name\'', 
                array(':name' => $name)
            );
        }

        // Get file return value
        $array = $file->process();

        if (!is_array($array))
        {
            throw new Exception(
                'Config file \':name\' musts return an array', 
                array(':name' => $name)
            );
        }

        $this->array = $array;
    }
}
