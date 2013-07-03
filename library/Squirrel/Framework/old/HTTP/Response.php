<?php namespace Squirrel\HTTP;

use Squirrel\Types\Object;
use Squirrel\Config;

/**
 * Global HTTP response providing access
 * to status, headers and body.
 *
 * @package Squirrel
 * @author  Valérian
 */
class Response extends Object
{
    /**
     * @var int status code
     */
    protected $status;

    /**
     * @var array headers
     */
    protected $headers;

    /**
     * @var string body
     */
    protected $body;

    /**
     * Instanciates a new response with
     * optional status and headers.
     *
     * @param  int   status
     * @param  array headers
     */
    public function __construct($status = null, array $headers = null)
    {
        $this->setStatus($status !== null ? $status : 200);
        $this->setHeaders($headers !== null ? $headers : array());
        $this->setBody('');
    }

    /**
     * Gets response body strictly casted as string.
     */
    public function __toString()
    {
        return (string) $this->body;
    }

    /**
     * @param  int status
     * @return $this
     */
    public function setStatus($status)
    {
        if (Config::instance('http/statuses')->get($status) === null)
        {
            throw new Exception(sprintf(
                'Given status \'%s\' is not a valid HTTP status', $status));
        }

        $this->status = (int) $status;
        return $this;
    }

    /**
     * @return int status
     */
    public function getStatus()
    {
        return $this->status;
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
     * @return string header value
     */
    public function getHeader($name, $default = null)
    {
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

    /**
     * @param  string body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Sends response headers to PHP.
     *
     * @return $this
     */
    public function sendHeaders()
    {
        // Get server protocol
        $protocol = isset($_SERVER['SERVER_PROTOCOL'])
                  ? $_SERVER['SERVER_PROTOCOL']
                  : 'HTTP/1.1';

        // Prepare base header
        $header = $protocol . ' ' . $this->status . ' '
                . Config::instance('http/statuses')->get($this->status, 200);

        header($header);

        foreach ($this->headers as $name => $value)
        {
            header($name . ': ' . $value);
        }
        
        return $this;
    }
}
