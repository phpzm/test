<?php

namespace Tests\Headers;

use Testit\Http\Headers;
use Testit\Scope\Environment;

/**
 * Class API
 * @package Headers
 */
class API extends Headers
{
    /**
     * @var array
     */
    protected $headers = [
        'Content-Type' => 'application/json',
        'Access-Control-Request-Headers' => 'content-type, x-custom-header',
        'Access-Control-Request-Method' => '',
        'X-Auth-Token' => '',
    ];

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $body
     * @return array
     * @SuppressWarnings("Unused")
     */
    public function configure(string $method, string $endpoint, $body = []): array
    {
        $this->set('Access-Control-Request-Method', $method);
        $this->set('X-Auth-Token', Environment::get('token'));

        return $this->all();
    }
}