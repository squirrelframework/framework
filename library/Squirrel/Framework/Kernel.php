<?php

namespace Squirrel\Framework;

use Squirrel\Finder\FinderInterface;
use Squirrel\Routing\Router;

/**
 * Main class for framework initialization.
 *
 * @package Squirrel
 * @author Valérian Galliat
 */
class Kernel
{
    const ENVIRONMENT_DEVELOPMENT = 0;
    const ENVIRONMENT_PRODUCTION = 1;

    /**
     * @var int
     */
    protected $environment;

    /**
     * @var string
     */
    protected $timezone;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var Finder\FinderInterface
     */
    protected $finder;

    /**
     * @var Routing\Router
     */
    protected $router;

    /**
     * Sets default values.
     */
    public function __construct(FinderInterface $finder, Router $router)
    {
        $this->setEnvironment(self::ENVIRONMENT_DEVELOPMENT);
        $this->setTimezone('Europe/London');
        $this->setLocale('en_GB');
        $this->finder = $finder;
        $this->router = $router;
    }

    /**
     * @param int $environment
     * @return Kernel
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
     * @param string $timezone
     * @return Kernel
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
     * @return Kernel
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
}
