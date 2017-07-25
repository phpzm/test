<?php

namespace Tests\Asserts\Financial;

use Testit\Cases\API;

/**
 * Class DocumentType
 * @package Tests\Asserts\Financial
 */
class DocumentType extends API
{
    /**
     * @var string
     */
    protected $uri = '/v1/financial/document-type';

    /**
     * DocumentType constructor.
     */
    public function __construct()
    {
        parent::__construct([
            [
                'tpd_descricao' => 'Teste'
            ]
        ]);
    }
}