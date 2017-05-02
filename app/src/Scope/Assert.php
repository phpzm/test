<?php

namespace Testit\Scope;

/**
 * Class Assert
 * @package Testit\Scope
 */
class Assert
{
    /**
     * Path of resource
     * @var string
     */
    protected $path;

    /**
     * Parameters used in query string
     * @var array
     */
    protected $query = [];

    /**
     * Data used in body
     * @var array
     */
    protected $body = [];

    /**
     * Function match executed in response
     * @var callable
     */
    protected $match;

    /**
     * Message related to this test
     * @var string
     */
    protected $message;

    /**
     * Assert constructor.
     * @param string $message
     * @param string $path
     * @param array $query
     * @param array $body
     * @param callable $match
     */
    public function __construct($message, $path, $query, $body, $match)
    {
        $this->message = $message;
        $this->path = $path;
        $this->query = $query;
        $this->body = $body;
        $this->match = $match;
    }

    /**
     * @param string $message
     * @param array $query
     * @param array $body
     * @param callable $match
     * @return Assert
     */
    public static function make($message, $path, $query, $body, $match)
    {
        return new static($message, $path, $query, $body, $match);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return callable
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param $response
     * @return mixed
     */
    public function resolve($response)
    {
        return call_user_func_array($this->match, [$response]);
    }
}