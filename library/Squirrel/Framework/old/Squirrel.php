<?php

namespace Squirrel;

use Finder\CascadingFinder;
use Autoloader\PsrAutoloader;
use Debugger\HttpHandler;
use Debugger\Handler;

/**
 * Main class for framework.
 *
 * Provides some environment functions
 * and autoload functionnality.
 *
 * @package Squirrel
 * @author Valérian Galliat
 */
class Squirrel
{
    // Environment constants
    const ENVIRONMENT_DEVELOPMENT = 0;
    const ENVIRONMENT_PRODUCTION = 1;

    // Type constants
    const TYPE_HTTP = 0;
    const TYPE_CONSOLE = 1;

    /**
     * @var int
     */
    protected $environment;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var string
     */
    protected $timezone;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $baseName;

    /**
     * @var string[string]
     */
    protected $modules;

    /**
     * @var Finder\CascadingFinder
     */
    protected $finder;

    /**
     * @var Autoloader\AutoloaderInterface
     */
    protected $autoloader;

    /**
     * @var ErrorHandler\ErrorHandlerInterface
     */
    protected $errorHandler;

    /**
     * Prepares modules array with base directory.
     */
    public function __construct()
    {
        // Set default values
        $this->setEnvironment(self::ENVIRONMENT_DEVELOPMENT);
        $this->setType(self::TYPE_HTTP);
        $this->setTimezone('Europe/London');
        $this->setLocale('en_GB');

        // Prepare modules
        $this->modules = [];
        $baseDirectory = dirname(dirname(__DIR__));
        $this->baseName = basename($baseDirectory);
        $this->modules[$this->baseName] = $baseDirectory;
    }

    /**
     * @return Squirrel
     */
    public static function create()
    {
        return new self;
    }

    /**
     * @param int $environment
     * @return Squirrel
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
        return $this;
    }

    /**
     * @return int
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param int $type
     * @return Squirrel
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->environment;
    }

    /**
     * @param string $timezone
     * @return Squirrel
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
        date_default_timezone_set($this->timezone);
        return $this;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param string $locale
     * @return Squirrel
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        setLocale(LC_ALL, $locale . '.utf-8');
        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Instanciates an autoloader and registers it.
     *
     * @throws \RuntimeException If an autoloader was already created.
     * @return Squirrel
     */
    public function initAutoloader()
    {
        if (isset($this->finder, $this->autoloader)) {
            throw new \RuntimeException('An autoloader was already created.');
        }

        $this->finder = new CascadingFinder();

        foreach ($this->modules as $name => $path) {
            $this->finder->addRoot($path);
        }

        $this->autoloader = new PsrAutoloader($this->finder, 'classes');
        $this->autoloader->register();
        return $this;
    }
    
    /**
     * Instanciates appropriate error handler and registers it
     * to display error handling page.
     *
     * @throws \RuntimeException If an handler was already created.
     * @return Squirrel
     */
    public function initErrorHandler()
    {
        if (isset($this->handler)) {
            throw new \RuntimeException('An handler was already created.');
        }

        if ($this->type === self::TYPE_HTTP) {
            $this->handler = new HttpHandler();
        } else {
            $this->handler = new Handler();
        }

        $this->handler->register();
        return $this;
    }

    /**
     * Adds multiple modules in the cascading filesystem.
     * 
     * @throws \InvalidArgumentException If a module already exists.
     * @throws \InvalidArgumentException If a path is not a valid directory.
     * @param string[string] $modules
     * @return Squirrel
     */
    public function addModules(array $modules)
    {
        foreach ($modules as $name => $path) {
            $this->addModule($name, $path);
        }
    }

    /**
     * Adds a module in the cascading filesystem.
     * 
     * @throws \InvalidArgumentException If the module already exists.
     * @throws \InvalidArgumentException If the path is not a valid directory.
     * @param string $name Module name used for debug.
     * @param string $path Module path to autoload.
     * @return Squirrel
     */
    public function addModule($name, $path)
    {
        if (isset($this->modules[$name])) {
            throw new \InvalidArgumentException('Given module already exists');
        }

        if (!is_dir($path)) {
            throw new \InvalidArgumentException('Given path is not a valid directory');
        }

        $this->modules[$name] = realpath($path);
        return $this;
    }

    /**
     * @return string[string]
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Initializes all modules having an init file.
     *
     * @return Squirrel
     */
    public function initModules()
    {
        foreach ($this->modules as $name => $folder) {
            if ($name === $this->baseName) {
                continue;
            }

            $initFile = $folder . '/init.php';

            if (is_file($initFile)) {
                require $initFile;
            }
        }

        return $this;
    }

    /**
     * Gets console process with actual arguments.
     *
     * @return Squirrel\Console\Process\Internal
     */
    public function getProcess()
    {
        static $process = null;

        if ($process !== null)
        {
            // Process was already instanciated
            return $process;
        }

        // Get console arguments
        global $argv;

        if ($argv === null)
        {
            // Simulate process call
            return $process = Console\Process\Internal::factory(
                $_SERVER['SCRIPT_FILENAME']);
        }

        // Cast arguments as collection
        $arguments = Collection::cast($argv);

        // Get name
        $name = $arguments[0];

        // Get real arguments
        $arguments = $arguments->cut(1);

        // Instanciate process with current data
        return $process = Console\Process\Internal::factory(
            $name, $arguments->asArray());
    }
}
