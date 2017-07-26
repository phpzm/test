<?php

namespace Simples\Test\Scope;

use Psr\Http\Message\ResponseInterface;
use Simples\Helper\Text;

/**
 * Class Assert
 * @package Testit\Scope
 */
class Assert
{
    /**
     * Name to be outputed
     * @var string
     */
    private $name = '';

    /**
     * Method what will be used to make the request
     * @var string
     */
    private $method = '';

    /**
     * URI base to generate the endpoint
     * @var string
     */
    private $uri = '';

    /**
     * Path of resource
     * @var string
     */
    private $path;

    /**
     * Parameters used in query string
     * @var array
     */
    private $query = [];

    /**
     * Data used in body
     * @var array
     */
    private $body = [];

    /**
     * Function match executed in response
     * @var callable
     */
    private $match;

    /**
     * Message related to this test
     * @var string
     */
    private $message;

    /**
     * Assert constructor.
     * @param string $name
     * @param string $method
     * @param string $uri
     * @param string $path
     * @param array $query
     * @param array $body
     * @param callable $match
     * @param string $message
     */
    public function __construct(string $name, string $method, string $uri, string $path, array $query, array $body, $match, string $message)
    {
        $this->name = $name;
        $this->method = $method;
        $this->uri = $uri;
        $this->path = $path;
        $this->query = $query;
        $this->body = $body;
        $this->match = $match;
        $this->message = $message;
    }

    /**
     * @param string $name
     * @param string $method
     * @param string $uri
     * @param string $path
     * @param array $query
     * @param array $body
     * @param callable $match
     * @return Assert
     * @internal param string $message
     */
    public static function make(string $name, string $method, string $uri, string $path, array $query, array $body, $match)
    {
        $message = "Test `{$path}` in `{$uri}`";
        return new static($name, $method, $uri, $path, $query, $body, $match, $message);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @return callable
     */
    public function getMatch(): callable
    {
        return $this->match;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        $uri = $this->uri;
        if ($this->path) {
            $uri = $uri . '/' . $this->path();
        }
        return Text::replacement($uri, Memory::all());
    }

    /**
     * @return string
     */
    private function path()
    {
        if (substr($this->path, 0, 1) === '/') {
            return substr($this->path, 1);
        }
        return $this->path;
    }

    /**
     * @param ResponseInterface $response
     * @param Test $test
     * @return array
     */
    public function resolve(ResponseInterface $response, Test $test): array
    {
        return call_user_func_array($this->match, [$response, $test]);
    }
}