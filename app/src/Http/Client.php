<?php

namespace Testit\Http;

use GuzzleHttp\Cookie\CookieJar;
use Testit\App;
use GuzzleHttp\Client as Guzzle;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client
 * @package Testit\Http
 */
class Client extends Guzzle
{
    /**
     * Client constructor.
     */
    public function __construct()
    {
        parent::__construct([
            'base_uri' => App::option('url'),
            'timeout' => 2.0,
        ]);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $body
     * @return ResponseInterface
     */
    public function run(string $method, string $uri, array $body = [])
    {
        $cookies = CookieJar::fromArray(App::option('cookies'), App::option('domain'));

        return parent::request($method, $uri, [
            'cookies' => $cookies,
            'form_params' => $body,
        ]);
    }
}