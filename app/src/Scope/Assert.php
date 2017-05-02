<?php

namespace Testit\Scope;

/**
 * Class Assert
 * @package Testit\Scope
 */
class Assert
{
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
     * Assert constructor.
     * @param array $query
     * @param array $body
     * @param callable $match
     */
    public function __construct($query, $body, $match)
    {
        $this->query = $query;
        $this->body = $body;
        $this->match = $match;
    }

    /**
     * @param array $query
     * @param array $body
     * @param callable $match
     * @return Assert
     */
    public static function make($query, $body, $match)
    {
        return new static($query, $body, $match);
    }
}