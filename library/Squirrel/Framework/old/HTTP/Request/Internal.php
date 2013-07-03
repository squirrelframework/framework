<?php namespace Squirrel\HTTP\Request;

use Squirrel\HTTP\Request;
use Squirrel\HTTP\Response;
use Squirrel\HTTP\Controller;
use Squirrel\HTTP\Exception;
use Squirrel\HTTP\Router;
use Squirrel\Squirrel;
use Squirrel\Types\String;
use Squirrel\Types\Collection;
use Squirrel\FileSystem\File;

/**
 * Internal request driver, able to
 * make nested internal requests,
 * calling a controller with an action
 * found using routing classes.
 *
 * @package Squirrel
 * @author  Valérian
 */
class Internal extends Request
{
    /**
     * @var array route params
     */
    protected $params;

    /**
     * Intiializes params array.
     *
     * @see Squirrel\HTTP\Request
     */
    public function __construct($url, array $context = array())
    {
        parent::__construct($url, $context);
        $this->params = array();
    }

    /**
     * @return array params
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return string param
     */
    public function getParam($name, $default = null)
    {
        return isset($this->params[$name]) ? $this->params[$name] : $default;
    }

    /**
     * Finds matching route using core router
     * and executes found action, or throws a
     * not found exception if no route is found.
     *
     * @see Squirrel\HTTP\Request
     */
    public function execute()
    {
        // Instanciate responce and try to find a PHP web file
        $response = Response::factory();
        $file     = Squirrel::instance()->find('web', $this->url);

        if ($file !== null)
        {
            // Get generated file contents
            ob_start();
            $file->process();
            $body = ob_get_clean();

            // Get type of previous extension
            $type = File::cast($file->getFileName())->getType();

            // Build response with generated body
            return $response
                ->setHeader('Content-Type', $type)
                ->setBody($body);
        }

        // Try to find a static web file
        $file = Squirrel::instance()->find('web', $this->url, null);

        if ($file !== null)
        {
            if ($file->getExtension() === 'php')
            {
                // Do not allow to download PHP files
                throw new Exception('Illegal file type', null, 404);
            }

            // Sends raw file with its content type
            return $response
                ->setHeader('Content-Type', $file->getType())
                ->setBody($file->getContent());
        }

        // Try to find a matching route
        $route = Router::instance()->find($this->url);

        if ($route === null)
        {
            throw new Exception('No route match this url', null, 404);
        }
        
        // Get params
        $params = $route->match($this->url);

        if (!isset($params['controller']))
        {
            throw new Exception('No controller found with this route', null, 404);
        }

        if (!isset($params['action']))
        {
            throw new Exception('No action found with this route', null, 404);
        }

        // Get controller full class name
        $name = String::factory($params['controller'])->camelize(true);

        if (Controller::isChild('Squirrel\\Controller\\HTTP\\' . $name))
        {
            $name = 'Squirrel\\Controller\\HTTP\\' . $name;
        }
        else if (Controller::isChild('Squirrel\\Controller\\' . $name))
        {
            $name = 'Squirrel\\Controller\\' . $name;
        }
        else
        {
            $message = 'Found controller does not exists '
                     . 'or is not an HTTP controller';

            throw new Exception($message, null, 500);
        }

        // Instanciate controller
        $controller = new $name($this, $response);

        // Get action
        $action = (string) String::cast('action-' . $params['action'])
            ->camelize();

        if (!$controller->hasMethod($action))
        {
            throw new Exception(
                'Found controller does not have matching action', null, 404);
        }

        // Set request params
        $this->params = $params;

        if ($controller->hasMethod('before'))
        {
            // Call before method
            $controller->before();
        }

        // Call matching action
        $controller->$action();

        if ($controller->hasMethod('after'))
        {
            // Call after method
            $controller->after();
        }

        return $response;
    }

    /**
     * Redirects to another page.
     *
     * @param  string url
     * @return void
     */
    public function redirect($url)
    {
        echo Response::factory()->setHeader('Location', $url)->sendHeaders();
        exit;
    }

    /**
     * Terminates request with given status.
     *
     * @param  int status
     * @return void
     */
    public function terminate($status)
    {
        echo Response::factory()->setStatus($status)->sendHeaders();
        exit;
    }
}
