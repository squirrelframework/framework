<?php namespace Squirrel;

/**
 * Reflection class for current system.
 *
 * @package Squirrel
 * @author  Valérian
 */
class System extends Object
{
    // Type constants
    const TYPE_32 = 32;
    const TYPE_64 = 64;

    // OS constants
    const OS_LINUX     = 'linux';
    const OS_WINDOWS   = 'windows';
    const OS_MACINTOSH = 'macintosh';
    const OS_UNKNOWN   = 'unknown';

    /**
     * @var Squirrel\Config
     */
    protected $config;

    /**
     * @var int system type
     */
    protected $type;

    /**
     * @var string system OS
     */
    protected $os;

    /**
     * Loads configuration.
     */
    public function __construct()
    {
        $this->config = Config::instance('system');
    }

    /**
     * Gets system type.
     *
     * @return int type constant
     */
    public function getType()
    {
        if (isset($this->type))
        {
            return $this->type;
        }

        return $this->type = php_uname('m') === 'x86_64'
                           ? self::TYPE_64
                           : self::TYPE_32;
    }

    /**
     * Gets system OS.
     *
     * @return string OS constant
     */
    public function getOS()
    {
        if (isset($this->os))
        {
            return $this->os;
        }

        return $this->os = $this->config->get(PHP_OS, self::OS_UNKNOWN);
    }
}
