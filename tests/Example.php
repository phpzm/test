<?php

use Testit\Scope\Test;
use Psr\Http\Message\ResponseInterface;

/**
 * Class CursoNatureza
 * @package MyTests
 */
class Example extends Test
{
    /**
     * URI base
     * @var string
     */
    protected $uri = 'src/academico/post/CursoArea.gen.post.php?action=cursoarea-p';

    /**
     * CursoNatureza constructor.
     */
    public function __construct()
    {
        $body = [
            'cra_descricao' => 'Teste',
            'cra_ativo' => '1',
        ];

        $this->add('create', 'save', $body, function (ResponseInterface $response) {
            if (strpos((string)$response->getBody(), 'Usu&aacute;rio n&atilde;o encontrado') !== false) {
                return false;
            }
            if (strpos((string)$response->getBody(), 'Informe este protocolo ao solicitar atendimento') !== false) {
                return false;
            }
            return true;
        });

        $this->add('destroy', 'remove', $body, function (ResponseInterface $response) {
            if (strpos((string)$response->getBody(), 'Usu&aacute;rio n&atilde;o encontrado') !== false) {
                return false;
            }
            if (strpos((string)$response->getBody(), 'Informe este protocolo ao solicitar atendimento') !== false) {
                return false;
            }
            return true;
        });
    }
}