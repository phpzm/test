<?php

namespace Tests\Asserts\Financial;

use Testit\Cases\API;

/**
 * Class StandardHistory
 * @package Tests\Asserts\Financial
 */
class StandardHistory extends API
{
    /**
     * URI base
     * @var string
     */
    protected $uri = '/v1/financial/standard-history';

    /**
     * StandardHistory constructor.
     */
    public function __construct()
    {
        parent::__construct([
            [
                'hsp_descricao' => 'Teste',
            ]
        ]);
    }
}