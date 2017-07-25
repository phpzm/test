<?php

namespace Testit\Cases;

use stdClass;
use Testit\Scope\Test;
use Psr\Http\Message\ResponseInterface;
use Simples\Helper\JSON;
use Testit\Scope\Memory;
use Tests\Headers\API as Headers;

/**
 * Class API
 * @package Testit\Cases
 */
class API extends Test
{
    /**
     * @var string
     */
    protected $hashKey = '_id';

    /**
     * @var Headers
     */
    protected $headers;

    /**
     * API constructor.
     * @param array $bodies
     */
    public function __construct(array $bodies = [])
    {
        $this->headers = new Headers;

        if (count($bodies)) {
            $this->crud($bodies);
        }
    }

    /**
     * @return string
     */
    public function hashKey()
    {
        return $this->hashKey;
    }

    /**
     * @param array $body
     * @param array|stdClass $data
     * @return array
     */
    protected function compare(array $body, $data)
    {
        $errors = [];
        foreach ($body as $key => $value) {
            if (off($data, $key) === $value) {
                continue;
            }
            $errors[$key] = [
                'expected' => $value,
                'given' => off($data, $key),
            ];
        }
        if (count($errors)) {
            $errors = [
                'data' => $data,
                'body' => $body,
                'errors' => $errors,
            ];
        }
        return $errors;
    }

    /**
     * @param array $bodies
     */
    protected function crud(array $bodies)
    {
        foreach ($bodies as $body) {
            /**
             * search
             */
            $this->get('search', '/', function (ResponseInterface $response) {
                $response = JSON::decode((string)$response->getBody());
                if (is_numeric($response->meta->total)) {
                    return [];
                }
                return [
                    'meta' => [
                        'expected' => 'numeric',
                        'given' => gettype($response->meta->total),
                    ]
                ];
            });

            /**
             * create
             */
            $this->post('create', '/', $body, function (ResponseInterface $response, API $test) use ($body) {
                $response = JSON::decode((string)$response->getBody());

                Memory::push('__id__', off($response->data, $test->hashKey()));

                return $test->compare($body, $response->data);
            });

            /**
             * read
             */
            $this->get('read', '/{__id__}', function (ResponseInterface $response, API $test) use ($body) {
                $response = JSON::decode((string)$response->getBody());

                return $test->compare($body, $response->data[0]);
            });

            /**
             * update
             */
            $this->put('update', '/{__id__}', $body, function (ResponseInterface $response, API $test) use ($body) {
                $response = JSON::decode((string)$response->getBody());

                return $test->compare($body, $response->data);
            });

            /**
             * destroy
             */
            $this->delete('destroy', '/{__id__}', function (ResponseInterface $response, API $test) use ($body) {
                $response = JSON::decode((string)$response->getBody());

                return $test->compare($body, $response->data);
            });
        }
    }
}