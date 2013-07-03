<?php namespace Squirrel\HTTP;

use Squirrel\Types\Object;
use Squirrel\Types\String;

/**
 * Abstract class providing HTTP requests support,
 * with headers, GET and POST data and stuff.
 *
 * @package Squirrel
 * @author  Valérian
 */
abstract class Request extends Object
{
    /**
     * @var string url
     */
    protected $url;

    /**
     * @var string method
     */
    protected $method;

    /**
     * @var array headers
     */
    protected $headers;

    /**
     * @var array search vars
     */
    protected $search;

    /**
     * @var array post vars
     */
    protected $post;

    /**
     * @var array files
     */
    protected $files;

    /**
     * @var string payload
     */
    protected $payload;

    /**
     * Initializes all class properties with default values
     * or context values if given.
     *
     * @param string url
     * @param array  context
     */
    public function __construct($url, array $context = array())
    {
        $this->setUrl(trim($url, '/'));
        $this->setMethod(isset($context['method'])     ? $context['method']  : 'GET');
        $this->setHeaders(isset($context['headers'])   ? $context['headers'] : array());
        $this->setSearchVars(isset($context['search']) ? $context['search']  : array());
        $this->setPostVars(isset($context['post'])     ? $context['post']    : array());
        $this->setFiles(isset($context['files'])       ? $context['files']   : array());
        $this->setPayload(isset($context['payload'])   ? $context['payload'] : '');
    }

    /**
     * @param  string url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = (string) $url;
        return $this;
    }

    /**
     * @return string url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param  string method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = (string) String::cast($method)->upper();
        return $this;
    }

    /**
     * @return string method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param  array headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return array headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param  string header name
     * @param  string header value
     * @return $this
     */
    public function setHeader($name, $value)
    {
        $this->headers[(string) $name] = (string) $value;
        return $this;
    }

    /**
     * @param  string header name
     * @param  mixed  fallback value
     * @return array  header value
     */
    public function getHeader($name, $default = null)
    {
        return isset($this->headers[$name])
             ? $this->headers[$name]
             : $default;
    }

    /**
     * @param  array search vars
     * @return $this
     */
    public function setSearchVars(array $search)
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @return array search vars
     */
    public function getSearchVars()
    {
        return $this->search;
    }

    /**
     * @param  string search name
     * @param  string search value
     * @return $this
     */
    public function setSearch($name, $value)
    {
        $this->search[(string) $name] = (string) $value;
        return $this;
    }

    /**
     * @param  string search name
     * @param  mixed  fallback value
     * @return string search value
     */
    public function getSearch($name, $default = null)
    {
        return isset($this->search[$name]) ? $this->search[$name] : $default;
    }

    /**
     * @param  array post vars
     * @return $this
     */
    public function setPostVars(array $post)
    {
        $this->post = $post;
        return $this;
    }

    /**
     * @return array post vars
     */
    public function getPostVars()
    {
        return $this->post;
    }

    /**
     * @param  string post name
     * @param  string post value
     * @return $this
     */
    public function setPost($name, $value)
    {
        $this->post[(string) $name] = (string) $value;
        return $this;
    }

    /**
     * @param  string post name
     * @param  mixed  fallback value
     * @return string post value
     */
    public function getPost($name, $default = null)
    {
        return isset($this->post[$name]) ? $this->post[$name] : $default;
    }

    /**
     * @param  array files
     * @return $this
     */
    public function setFiles(array $files)
    {
        $this->files = $files;
        return $this;
    }

    /**
     * @return array files
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param  string name
     * @param  array  file
     * @return $this
     */
    public function setFile($name, $value)
    {
        $this->files[(string) $name] = (string) $value;
    }

    /**
     * @param  string name
     * @param  mixed  fallback value
     * @return array  file
     */
    public function getFile($name, $default = null)
    {
        return isset($this->files[$name]) ? $this->files[$name] : $default;
    }

    /**
     * @param  string payload
     * @return $this
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @return string payload
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Executes current request regarding
     * of request type, and returns an HTTP response.
     *
     * @return Squirrel\HTTP\Response
     */
    public abstract function execute();
}
