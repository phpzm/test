<?php

namespace Testit\Http;

/**
 * Class Headers
 * @package Testit\Http
 */
abstract class Headers
{
    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @param string $header
     * @param string $value
     * @return $this
     */
    public function set(string $header, string $value)
    {
        $this->headers[$header] = $value;
        return $this;
    }

    /**
     * @param string $header
     * @return string|null
     */
    public function get(string $header)
    {
        return isset($this->headers[$header]) ? $this->headers[$header] : null;
    }

    /**
     * @param string $header
     * @return mixed|null
     */
    public function has(string $header)
    {
        return isset($this->headers[$header]);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->headers;
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $body
     * @return array
     */
    abstract public function configure(string $method, string $endpoint, $body = []): array;

}