<?php

namespace Simples\Test\Scope;

use GuzzleHttp\Exception\BadResponseException;
use JsonSerializable;
use Psr\Http\Message\ResponseInterface;
use Simples\Helper\JSON;
use Simples\Helper\Text;
use Simples\Test\Http\Client;
use Simples\Test\Http\Header;

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
     * Headers to be used in requests
     * @var Header
     */
    protected $header;

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
    protected function add(
        string $method,
        string $name,
        string $path,
        callable $match,
        array $query = [],
        array $body = []
    ) {
        $this->addAssert($name, Assert::make($method, $this->uri, $path, $query, $body, $match));
        return $this;
    }

    /**
     * @param string $method
     * @param string $name
     * @param string $path
     * @param callable $match
     * @param array $query
     * @param array $body
     * @return $this
     */
    protected function raw(
        string $method,
        string $name,
        string $path,
        callable $match,
        array $query = [],
        array $body = []
    ) {
        $this->addAssert($name, Assert::make($method, '', $path, $query, $body, $match));
        return $this;
    }

    /**
     * @param string $method
     * @param string $name
     * @param string $path
     * @param callable $match
     * @param array $query
     * @param array $body
     * @return $this
     */
    protected function raw(
        string $method,
        string $name,
        string $path,
        callable $match,
        array $query = [],
        array $body = []
    ) {
        $this->addAssert($name, Assert::make($method, '', $path, $query, $body, $match));
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
     * @param string $method
     * @param string $endpoint
     * @param mixed $body
     * @return array
     */
    protected function headers(string $method, string $endpoint, $body = []): array
    {
        if (is_null($this->header)) {
            return [];
        }
        return $this->header->configure($method, $endpoint, $body);
    }

    /**
     * @param Client $client
     * @return array
     */
    public function run(Client $client)
    {
        $tests = [];

        /** @var Assert $assert */
        foreach ($this->asserts as $name => $assert) {
            $errors = null;
            $body = null;
            try {
                $headers = $this->headers($assert->getMethod(), $assert->getEndpoint(), $assert->getBody());
                $body = $assert->getBody();

                /** @var ResponseInterface $resolve */
                $resolve = $client->run($headers, $assert->getMethod(), $assert->getEndpoint(), $body);

            } catch (BadResponseException $error) {
                $resolve = $error->getResponse();
                $string = Text::replace((string)$resolve->getBody(), '/', '\\');
                $errors = [
                    'request' => JSON::decode($string, JSON_PRETTY_PRINT)
                ];
            }

            if (is_null($errors)) {
                $errors = $assert->resolve($resolve, $this);
            }

            $status = !count($errors);

            $tests[$name] = [
                'assert' => $status,
                'method' => $assert->getMethod(),
                'endpoint' => $assert->getEndpoint(),
                'status' => $resolve->getStatusCode(),
                'errors' => $errors,
                'headers' => $status ? [] : $resolve->getHeaders(),
//                'message' => $assert->getMessage(),
//                'response' => JSON::decode((string)$resolve->getBody(), JSON_PRETTY_PRINT),
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
    public function jsonSerialize()
    {
        return [
            'uri' => $this->uri,
            'asserts' => array_keys($this->asserts)
        ];
    }
}
