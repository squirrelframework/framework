<?php

namespace Squirrel\Framework;

use Squirrel\Framework\Controller\HttpController;
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
     * @throws \Http\Exception\Exception
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request)
    {
        $response = new Response;

        try {
            $file = new File($this->finder->findFile('web/' . $request->getUrl() . '.php'));
            $body = $file->process();
            $file = (new File($file->getFileName()))->getType();

            return $response
                ->setHeader('Content-Type', $type)
                ->setBody($body);
        } catch (PathNotFoundException $exception) {}

        try {
            $file = new File($this->finder->findFile('web/' . $request->getUrl()));
            
            if ($file->getExtension() === 'php') {
                throw new ForbiddenException(sprintf('Illegal file type "%s".', $file->getType()));
            }

            return $response
                ->setHeader('Content-Type', $file->getType())
                ->setBody($file->getContent());
        } catch (PathNotFoundException $exception) {}

        $match = $this->router->match($request->getScriptPath(), $params);

        if (!$match) {
            throw new NotFoundException('Unable to find a matching route.');
        }
        
        if (!isset($params['controller'])) {
            throw new NotFoundException('No controller found with this route.');
        }

        if (!isset($params['action'])) {
            throw new NotFoundException('No action found with this route.');
        }

        if (!HttpController::isChild($params['controller'])) {
            throw new InternalServerErrorException(sprintf('Found controller "%s" does not exists or is not an HTTP controller.', $params['controller']));
        }

        $controller = new $params['controller']($request, $response);
        $action = $params['action'] . 'Action';

        if (!$controller->hasMethod($action)) {
            throw new NotFoundException(sprintf('Found controller does not have matched action "%s".', $params['action']));
        }

        $request->setParams($params);

        if ($controller->hasMethod('preProcess')) {
            $controller->preProcess();
        }
        
        $controller->$action();

        if ($controller->hasMethod('postProcess')) {
            $controller->postProcess();
        }

        return $response;
    }
}
