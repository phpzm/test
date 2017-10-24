<?php

namespace Simples\Test\Http;

use function array_merge_recursive;
use GuzzleHttp\Cookie\CookieJar;
use Simples\Helper\Text;
use Simples\Test\App;
use GuzzleHttp\Client as Guzzle;
use Psr\Http\Message\ResponseInterface;
use Simples\Test\Scope\Memory;
use Simples\Test\Scope\Set;
use function stop;

/**
 * Class Client
 * @package Testit\Http
 */
class Client extends Guzzle
{
    /**
     * @var string
     */
    protected $base;

    /**
     * @var array
     */
    protected $defaults;

    /**
     * Client constructor.
     * @param string $base
     * @param array $defaults
     */
    public function __construct(string $base, array $defaults = [])
    {
        $this->base = $base;
        $this->defaults = $defaults ? $defaults : [];

        parent::__construct([
            'timeout' => 10,
            'defaults' => $this->defaults,
        ]);
    }

    /**
     * @param string $uri
     * @return string
     */
    protected function uri(string $uri)
    {
        if (substr($uri, 0, 1) === '/') {
            $uri = substr($uri, 1);
        }
        return Text::replacement($this->base . '/' . $uri, Memory::all());
    }

    /**
     * @param array $headers
     * @param string $method
     * @param string $uri
     * @param string $type
     * @param array $body
     * @param array $query
     * @param bool $debug
     * @return ResponseInterface
     */
    public function run(array $headers, string $method, string $uri, string $type, array $body = [], array $query = [], $debug = false)
    {
        $cookies = CookieJar::fromArray(App::options('cookies'), App::options('domain'));

        if ($body instanceof Set) {
            /** @noinspection PhpUndefinedMethodInspection */
            $body = $body->body();
        }

        $data = [];
        foreach ($body as $index => $value) {
            if (gettype($value) === TYPE_ARRAY) {
                $value = off($value, 'request');
            }
            $data[$index] = $value;
        }
        $settings = [
            'headers' => $headers,
            'query' => $query,
            'cookies' => $cookies,
            $type => $data,
            'debug' => $debug
        ];
        $options = array_merge_recursive($this->defaults, $settings);
        return parent::request($method, $this->uri($uri), $options);
    }
}
