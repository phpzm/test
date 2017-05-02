<?php

namespace Testit\Http;

use GuzzleHttp\Cookie\CookieJar;
use Testit\App;
use GuzzleHttp\Client as Guzzle;

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
     * @param string $uri
     * @param array $body
     * @return mixed
     */
    public function getResponse($uri, array $body = [])
    {
        $cookies = CookieJar::fromArray(App::option('cookies'), App::option('domain'));

        return parent::request('POST', $uri, [
            'cookies' => $cookies,
            'form_params' => $body,
        ]);
    }
}