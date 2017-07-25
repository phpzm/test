<?php

namespace Tests\Financial;

use Testit\Scope\Test;
use Psr\Http\Message\ResponseInterface;

/**
 * Class StandardHistory
 */
class StandardHistory extends Test
{
    /**
     * URI base (without / at the end)
     * @var string
     */
    protected $uri = '/v1/api/standard-history';

    /**
     * CursoNatureza constructor.
     */
    public function __construct()
    {
        $body = [
            'hsp_descricao' => 'Teste'
        ];

        $this->post('create', '/', $body, function (ResponseInterface $response) {
            return (string)$response->getBody();
        });

        $this->get('read', '/{id}', function (ResponseInterface $response) {
            return (string)$response->getBody();
        });

        $this->put('update', '/{id}', $body, function (ResponseInterface $response) {
            return (string)$response->getBody();
        });

        $this->delete('destroy', '/{id}', function (ResponseInterface $response) {
            return (string)$response->getBody();
        });
    }
}