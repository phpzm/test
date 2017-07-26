<?php

namespace Tests\Asserts\Registration;

use Testit\Cases\API;
use Testit\Scope\Set;

/**
 * Class Client
 * @package Tests\Asserts\Registration
 */
class Client extends API
{
    /**
     * @var string
     */
    protected $uri = '/v1/registration/client';

    /**
     * Client constructor.
     */
    public function __construct()
    {
        parent::__construct([
            Set::make([
                'body' => [
                    'pss_nome' => [
                        'request' => 'Teste',
                        'response' => 'TESTE',
                    ],
                    'cli_observacao' => 'áàção$',
                ]
            ])
        ]);
    }
}
