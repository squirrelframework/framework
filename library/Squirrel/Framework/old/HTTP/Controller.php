<?php namespace Squirrel\HTTP;

/**
 * Base class for all HTTP controllers.
 *
 * @package Squirrel
 * @author  Valérian
 */
abstract class Controller extends \Squirrel\Controller
{
    /**
     * @var Squirrel\HTTP\Request
     */
    protected $request;

    /**
     * @var Squirrel\HTTP\Response
     */
    protected $response;

    /**
     * Sets properties.
     *
     * @param Squirrel\HTTP\Request
     * @param Squirrel\HTTP\Response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }
}
