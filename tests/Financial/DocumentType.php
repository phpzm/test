<?php

namespace Tests\Financial;

use Testit\Scope\Test;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DocumentType
 * @package Tests\Financial
 */
class DocumentType extends Test
{
    /**
     * @var string
     */
    protected $uri = '/api/v1/document-type';

    /**
     * CursoNatureza constructor.
     */
    public function __construct()
    {
        $body = [
            'tpd_descricao' => 'Teste'
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