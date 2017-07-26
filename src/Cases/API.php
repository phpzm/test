<?php

namespace Simples\Test\Cases;

use stdClass;
use Simples\Test\Scope\Set;
use Simples\Test\Scope\Test;
use Psr\Http\Message\ResponseInterface;
use Simples\Helper\JSON;
use Simples\Test\Scope\Memory;
use Simples\Test\Headers\API as Headers;

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
     * @param array|Set $body
     * @param array|stdClass $data
     * @return array
     */
    protected function compare($body, $data)
    {
        $errors = [];
        if ($body instanceof Set) {
            /** @noinspection PhpUndefinedMethodInspection */
            $body = $body->body();
        }
        foreach ($body as $key => $value) {

            if (gettype($value) === TYPE_ARRAY) {
                $value = off($value, 'response');
            }

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
            $set = $body;
            if ($body instanceof Set) {
                $body = $body->body();
            }

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
            $this->post('create', '/', $body, function (ResponseInterface $response, API $test) use ($set) {
                $response = JSON::decode((string)$response->getBody());

                Memory::push('__id__', off($response->data, $test->hashKey()));

                return $test->compare($set, $response->data);
            });

            /**
             * read
             */
            $this->get('read', '/{__id__}', function (ResponseInterface $response, API $test) use ($set) {
                $response = JSON::decode((string)$response->getBody());

                return $test->compare($set, $response->data[0]);
            });

            /**
             * update
             */
            $this->put('update', '/{__id__}', $body, function (ResponseInterface $response, API $test) use ($set) {
                $response = JSON::decode((string)$response->getBody());

                return $test->compare($set, $response->data);
            });

            /**
             * destroy
             */
            $this->delete('destroy', '/{__id__}', function (ResponseInterface $response, API $test) use ($set) {
                $response = JSON::decode((string)$response->getBody());

                return $test->compare($set, $response->data);
            });
        }
    }
}