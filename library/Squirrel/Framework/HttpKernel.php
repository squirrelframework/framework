<?php

namespace Squirrel\Framework;

use Squirrel\Finder\FinderInterface;
use Squirrel\Routing\Router;
use Squirrel\Http\Request;
use Squirrel\Http\Response;
use Squirrel\Http\Exception\ForbiddenException;
use Squirrel\Http\Exception\NotFoundException;
use Squirrel\Http\Exception\InternalServerErrorException;
use Squirrel\Filesystem\File;
use Squirrel\Finder\Exception\PathNotFoundException;

/**
 * Main kernel class for HTTP applications.
 *
 * @package Squirrel\Framework
 * @author Valérian Galliat
 */
class HttpKernel extends Kernel
{
    /**
     * Handles given request to find a controller and execute it.
     *
     * @throws \RunetimeException If the context is not valid.
     * @throws \Squirrel\Http\Exception\Exception If an HTTP error occurs.
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request)
    {
        if (!$request->hasScriptPath()) {
            throw new NotFoundException('Unable to determine the script path.');
        }

        $this->requireContext();
        $this->context->ensure('finder', 'Squirrel\Finder\FinderInterface');
        $this->context->ensure('router', 'Squirrel\Routing\Router');

        $response = new Response();

        try {
            $file = new File($this->context->get('finder')->findFile('web/' . $request->getScriptPath() . '.php'));
            $body = $file->process();
            $file = (new File($file->getFileName()))->getType();

            return $response
                ->setHeader('Content-Type', $type)
                ->setBody($body);
        } catch (PathNotFoundException $exception) {}

        try {
            $file = new File($this->context->get('finder')->findFile('web/' . $request->getScriptPath()));
            
            if ($file->getExtension() === 'php') {
                throw new ForbiddenException(sprintf('Illegal file type "%s".', $file->getType()));
            }

            return $response
                ->setHeader('Content-Type', $file->getType())
                ->setBody($file->getContent());
        } catch (PathNotFoundException $exception) {}

        $match = $this->context->get('router')->match($request->getScriptPath(), $params);

        if (!$match) {
            throw new NotFoundException('Unable to find a matching route.');
        }
        
        if (!isset($params['controller'])) {
            throw new NotFoundException('No controller found with this route.');
        }

        if (!isset($params['action'])) {
            throw new NotFoundException('No action found with this route.');
        }

        if (!in_array('Squirrel\Framework\Controller\Controller', class_parents($params['controller']))) {
            throw new InternalServerErrorException(sprintf('Found controller "%s" does not exists or is not an controller class.', $params['controller']));
        }

        $controller = new $params['controller']();
        $action = $params['action'] . 'Action';

        if (!is_callable(array($controller, $action))) {
            throw new NotFoundException(sprintf('Found controller does not have matched action "%s" or the action is not public.', $params['action']));
        }

        $request->setParams($params);
        $this->context->set('request', $request);
        $this->context->set('response', $response);
        $controller->setContext($this->context);

        if (is_callable(array($controller, 'preProcess'))) {
            $controller->preProcess();
        }
        
        $controller->$action();

        if (is_callable(array($controller, 'postProcess'))) {
            $controller->postProcess();
        }

        return $response;
    }
}
