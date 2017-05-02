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
     * @param string $name
     * @param string $path
     * @param array $body
     * @param callable $match
     * @param array $query
     */
    protected function add($name, $path, $body, $match = null, $query = null)
    {
        if (!$match) {
            $match = function (ResponseInterface $response) use ($body) {
                return ((string)$response->getBody()) === $body;
            };
        }
        return $this->addAssert($name, Assert::make("Test `{$path}` in `{$this->uri}`", $path, $query, $body, $match));
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
            $status = !!$assert->resolve(
                $client->getResponse($this->path($assert->getPath()), $assert->getBody())
            );
            $tests[$name] = [
                'status' => $status,
                'message' => $assert->getMessage()
            ];
        }
        return $tests;
    }

    /**
     * @param string $path
     * @return string
     */
    private function path($path)
    {
        return $this->uri . '-' . $path;
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