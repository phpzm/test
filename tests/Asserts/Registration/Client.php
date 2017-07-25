<?php

namespace Tests\Asserts\Registration;

use Testit\Cases\API;

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
            [
                'pss_nome' => 'Teste',
                'cli_observacao' => 'áàção$',
            ]
        ]);
    }
}