<?php

namespace Simples\Test\Http;

use GuzzleHttp\Cookie\CookieJar;
use Simples\Helper\Text;
use Simples\Test\App;
use GuzzleHttp\Client as Guzzle;
use Psr\Http\Message\ResponseInterface;
use Simples\Test\Scope\Memory;
use Simples\Test\Scope\Set;

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
     * Client constructor.
     * @param string $base
     * @param array $defaults
     */
    public function __construct(string $base, array $defaults = [])
    {
        $this->base = $base;

        parent::__construct([
            'timeout' => 10,
            'defaults' => $defaults,
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
     * @param array $body
     * @return ResponseInterface
     */
    public function run(array $headers, string $method, string $uri, array $body = [])
    {
        $cookies = CookieJar::fromArray(App::options('cookies'), App::options('domain'));

        if ($body instanceof Set) {
            /** @noinspection PhpUndefinedMethodInspection */
            $body = $body->body();
        }

        $json = [];
        foreach ($body as $index => $value) {
            if (gettype($value) === TYPE_ARRAY) {
                $value = off($value, 'request');
            }
            $json[$index] = $value;
        }
        return parent::request($method, $this->uri($uri), [
            'headers' => $headers,
            'cookies' => $cookies,
            'json' => $json,
        ]);
    }
}
