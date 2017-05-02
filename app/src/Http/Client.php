<?php

namespace Testit\Http;

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
        return parent::request('POST', $uri, $body);
    }
}