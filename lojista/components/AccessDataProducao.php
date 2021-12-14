<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 13/07/2016
 * Time: 17:44
 */

namespace lojista\components;

use PhpSigep\Model\AccessData;
use PhpSigep\Model\Diretoria;

class AccessDataProducao extends AccessData
{
    /**
     * Atalho para criar uma {@link AccessData} com os dados do ambiente de homologação.
     */
    public function __construct()
    {
        parent::__construct(
            array(
                'usuario' => 'PecaAgora',
                'senha' => 'grnhw8',
                'codAdministrativo' => '16187296',
                'numeroContrato' => '9912398979',
                'cartaoPostagem' => '0072351357',
                'cnpjEmpresa' => '18947338000110', // Obtido no método 'buscaCliente'.
                'anoContrato' => 2016, // Não consta no manual.
                'diretoria' => new Diretoria(Diretoria::DIRETORIA_DR_MINAS_GERAIS), // Obtido no método 'buscaCliente'.
            )
        );
        try {
            \PhpSigep\Bootstrap::getConfig()->setEnv(\PhpSigep\Config::ENV_DEVELOPMENT);
        } catch (\Exception $e) {
        }
    }
}