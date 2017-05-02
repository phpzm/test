<?php

namespace Testit\Scope;

use JsonSerializable;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Testing
 * @package Testit\Test
 */
class Test implements JsonSerializable
{
    /**
     * Endpoint of test
     * @var string
     */
    protected $uri;

    /**
     * List of asserts what will be executed
     * @var array
     */
    protected $asserts = [];

    /**
     * @param string $path
     * @param Assert $assert
     */
    protected function addAssert($path, Assert $assert)
    {
        $this->asserts[$path] = $assert;
    }

    /**
     * @param string $path
     * @param array $body
     * @param callable $match
     * @param array $query
     */
    protected function add($path, $body, $match = null, $query = null)
    {
        if (!$match) {
            $match = function (ResponseInterface $response) use ($body) {
                return ((string)$response->getBody()) === $body;
            };
        }
        return $this->addAssert($path, Assert::make($query, $body, $match));
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return [
            'uri' => $this->uri,
            'asserts' => array_keys($this->asserts)
        ];
    }
}