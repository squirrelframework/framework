<?php

namespace Squirrel\Framework\Controller;

use Squirrel\Context\ContextAware;

/**
 * Abstract class for all controllers.
 *
 * @package Squirrel
 * @author Valérian Galliat
 */
abstract class Controller extends ContextAware
{
    /**
     * Instanciates given view class and renders it
     * to return the generated content.
     * 
     * @param \Squirrel\Framework\View\View $viewClassName The class name of the view to render.
     * @param mixed $argument Optional first argument to pass to the constructor.
     * @param mixed $argument,... Optional other arguments. 
     * @return string
     */
    protected function renderView($viewClassName)
    {
        $args = func_get_args();
        array_shift($args);
        $reflection = new \ReflectionClass($viewClassName);
        $view = $reflection->newInstanceArgs($args);
        $view->setContext($this->context);
        return $view->render();
    }
}
