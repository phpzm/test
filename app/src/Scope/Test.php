<?php

namespace Testit\Scope;

use JsonSerializable;
use Psr\Http\Message\ResponseInterface;
use Testit\Http\Client;

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
     * @param string $name
     * @param Assert $assert
     */
    protected function addAssert($name, Assert $assert)
    {
        $this->asserts[$name] = $assert;
    }

    /**
     * @param string $method
     * @param string $name
     * @param string $path
     * @param callable|array $match
     * @param array $query
     * @param array $body
     * @return $this
     */
    protected function add(string $method, string $name, string $path, callable $match, array $query = [], array $body = [])
    {
        $this->addAssert($name, Assert::make($method, $this->uri, $path, $query, $body, $match));
        return $this;
    }

    /**
     * @param string $name
     * @param string $path
     * @param array|null $body
     * @param callable|null $match
     * @param array|null $query
     * @return Test
     */
    protected function post(string $name, string $path, array $body = null, callable $match = null, array $query = [])
    {
        return $this->add('POST', $name, $path, $match, $query, $body);
    }

    /**
     * @param string $name
     * @param string $path
     * @param callable|null $match
     * @param array|null $query
     * @return Test
     */
    protected function get(string $name, string $path, callable $match = null, array $query = [])
    {
        return $this->add('GET', $name, $path, $match, $query);
    }

    /**
     * @param string $name
     * @param string $path
     * @param array|null $body
     * @param callable|null $match
     * @param array|null $query
     * @return Test
     */
    protected function put(string $name, string $path, array $body = [], callable $match = null, array $query = [])
    {
        return $this->add('PUT', $name, $path, $match, $query, $body);
    }

    /**
     * @param string $name
     * @param string $path
     * @param callable|null $match
     * @param array|null $query
     * @return Test
     */
    protected function delete(string $name, string $path, callable $match = null, array $query = [])
    {
        return $this->add('DELETE', $name, $path, $match, $query);
    }

    /**
     * @param Client $client
     * @return array
     */
    public function run(Client $client)
    {
        $tests = [];
        foreach ($this->asserts as $name => $assert) {
            /** @var Assert $assert */
            $resolve = $client->run($assert->getMethod(), $assert->getEndpoint(), $assert->getBody());
            $status = !!$assert->resolve($resolve);
            $tests[$name] = [
                'status' => $status,
                'endpoint' => $assert->getEndpoint(),
                'message' => $assert->getMessage()
            ];
        }
        return $tests;
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