<?php

namespace Squirrel\Framework\View;

use Squirrel\Context\ContextAware;
use Squirrel\Finder\FinderInterface;

/**
 * Main class for all views.
 *
 * @package Squirrel\Framework\View
 * @author Valérian Galliat.
 */
abstract class View extends ContextAware
{
    /**
     * @var mixed[string]
     */
    protected $variables;

    /**
     * @param mixed[string] $variables
     */
    public function __construct(array $variables = null)
    {
        $this->variables = $variables;
    }

    /**
     * Shortcut for render method without throwing exceptions.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $exception) {
            return (string) $exception;
        }
    }

    /**
     * Renders view and gets its content.
     *
     * @return string
     */
    abstract public function render();

    /**
     * Renders given template with view variables and
     * returns its generated content.
     *
     * @throws \Squirrel\Finder\Exception\PathNotFoundException If the template is not found.
     * @param string $path The template path.
     * @param mixed[string] $variables Optional additional variables.
     * @return string
     */
    protected function renderTemplate($path, array $variables = null)
    {
        $this->requireContext();
        $this->context->ensure('finder', 'Squirrel\Finder\FinderInterface');

        $file =  $this->context->get('finder')->find('templates/' . $path . '.php');

        if (isset($this->variables)) {
            extract($this->variables);
        }

        if (isset($variables)) {
            extract($variables);
        }

        ob_start();

        try {
            require $file;
        } catch (\Exception $exception) {
            ob_clean();
            throw $exception;
        }

        return ob_get_clean();
    }

    /**
     * Gets an asset absolute URL.
     *
     * @throws \Squirrel\Finder\Exception\PathNotFoundException If the asset is not found.
     * @param string $path
     * @return string
     */
    protected function getAsset($path)
    {
        $this->requireContext();
        $this->context->ensure('request', 'Squirrel\Http\Request');
        $request = $this->context->get('request');
        $basePath = $request->hasBasePath() ? $request->getBasePath() : '';
        return $basePath . '/' . $path;
    }
}
