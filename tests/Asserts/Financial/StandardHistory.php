<?php

namespace Tests\Asserts\Financial;

use Psr\Http\Message\ResponseInterface;
use Simples\Helper\JSON;
use Testit\Scope\Memory;
use Testit\Scope\Test;
use Tests\Headers\API;

/**
 * Class StandardHistory
 */
class StandardHistory extends Test
{
    /**
     * URI base (without / at the end)
     * @var string
     */
    protected $uri = '/v1/financial/standard-history';

    /**
     * CursoNatureza constructor.
     */
    public function __construct()
    {
        $this->headers = new API;

        $body = [
            'hsp_descricao' => 'Teste'
        ];
        $hashKey = '_id';

        /**
         * search
         */
        $this->get('search', '/', function (ResponseInterface $response) {
            $response = JSON::decode((string)$response->getBody());
            return is_numeric($response->meta->total);
        });

        /**
         * create
         */
        $this->post('create', '/', $body, function (ResponseInterface $response, &$log) use ($body, $hashKey) {
            $response = JSON::decode((string)$response->getBody());

            $log[] = $response->data;
            Memory::push('__id__', off($response->data, $hashKey));

            foreach ($body as $key => $value) {
                if (off($response->data, $key) !== $value) {
                    return false;
                }
            }
            return true;
        });

        /**
         * read
         */
        $this->get('read', '/{__id__}', function (ResponseInterface $response) use ($body) {
            $response = JSON::decode((string)$response->getBody());

            foreach ($body as $key => $value) {
                if (off($response->data, $key) !== $value) {
                    return false;
                }
            }
            return true;
        });

        /**
         * update
         */
        $this->put('update', '/{__id__}', $body, function (ResponseInterface $response) use ($body) {
            $response = JSON::decode((string)$response->getBody());

            foreach ($body as $key => $value) {
                if (off($response->data, $key) !== $value) {
                    return false;
                }
            }
            return true;
        });

        /**
         * destroy
         */
        $this->delete('destroy', '/{__id__}', function (ResponseInterface $response) use ($body) {
            $response = JSON::decode((string)$response->getBody());

            foreach ($body as $key => $value) {
                if (off($response->data, $key) !== $value) {
                    return false;
                }
            }
            return true;
        });
    }
}