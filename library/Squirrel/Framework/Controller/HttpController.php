<?php

namespace Squirrel\Framework\Controller;

use Squirrel\Http\Request;
use Squirrel\Http\Response;

/**
 * Abstract class for all HTTP controllers having an HTTP request and response.
 *
 * @package Squirrel
 * @author Valérian Galliat
 */
abstract class HttpController extends Controller
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param Request $request HTTP request that has matched current controller.
     * @param Response $response HTTP response to generate.
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}
