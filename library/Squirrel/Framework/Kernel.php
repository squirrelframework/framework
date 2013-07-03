<?php

namespace Squirrel\Framework;

use Squirrel\Context\ContextAware;

/**
 * Main class for framework initialization.
 *
 * @package Squirrel
 * @author Valérian Galliat
 */
class Kernel extends ContextAware
{
    const ENVIRONMENT_DEVELOPMENT = 0;
    const ENVIRONMENT_PRODUCTION = 1;

    /**
     * @var integer
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
     * Sets default values.
     */
    public function __construct()
    {
        $this->setEnvironment(self::ENVIRONMENT_DEVELOPMENT);
        $this->setTimezone('Europe/London');
        $this->setLocale('en_GB');
    }

    /**
     * @param integer $environment
     * @return Kernel
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
        return $this;
    }

    /**
     * @return integer
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
