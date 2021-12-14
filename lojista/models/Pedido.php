<?php

namespace lojista\models;

use lojista\components\AccessDataProducao;
use PhpSigep\Config;
use PhpSigep\Model\AccessData;
use PhpSigep\Model\Etiqueta;
use PhpSigep\Model\RastrearObjeto;

/**
 * Class Pedido
 * @package lojista\models
 */
class Pedido extends \common\models\Pedido
{
    /**
     * @param $ambiente
     * @return AccessDataProducao|null|\PhpSigep\Model\AccessDataHomologacao
     */
    public static function getAccessData($ambiente)
    {
        if ($ambiente == \PhpSigep\Config::ENV_DEVELOPMENT) {
            return new \PhpSigep\Model\AccessDataHomologacao();
        }
        if ($ambiente == \PhpSigep\Config::ENV_PRODUCTION) {
            return new AccessDataProducao();
        }

        return null;
    }

    /**
     * @param $env
     * @param $accessData AccessData
     * @return Config
     */
    public static function getConfig($env, $accessData)
    {
        $config = new \PhpSigep\Config();
        $config->setAccessData($accessData);
        $config->setEnv($env);
        $config->setCacheOptions([
            'storageOptions' => [
                'enabled' => true,
                'ttl' => 10,
            ]
        ]);

        return $config;
    }

    public function rastrearObjeto(
        $tipo = RastrearObjeto::TIPO_LISTA_DE_OBJETOS,
        $resultado = RastrearObjeto::TIPO_RESULTADO_TODOS_OS_EVENTOS
    ) {
        if (!isset($this->etiqueta)) {
            return null;
        }

        $accessData = self::getAccessData(Config::ENV_PRODUCTION);
        $config = self::getConfig(Config::ENV_PRODUCTION, $accessData);
        \PhpSigep\Bootstrap::start($config);

        $rastrearObjeto = new \PhpSigep\Model\RastrearObjeto();
        $rastrearObjeto->setAccessData($accessData);
        $rastrearObjeto->setTipo($tipo);
        $rastrearObjeto->setTipoResultado($resultado);
        $rastrearObjeto->addEtiqueta(new Etiqueta(['etiquetaSemDV' => $this->etiqueta]));

        $phpSigep = new \PhpSigep\Services\SoapClient\Real();
        try {
            $result = $phpSigep->rastrearObjeto($rastrearObjeto);
            if (!$result->hasError()) {
                $rastro = $result->getResult();
                $rastro = array_shift($rastro);

                return $rastro;
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return null;
    }

    public function getStatusCorreios()
    {
        if ($this->statusAtual->tipoStatus->id >= 2) {
            if (!empty($this->plp_id)) {
                if ($rastro = $this->rastrearObjeto(\PhpSigep\Model\RastrearObjeto::TIPO_LISTA_DE_OBJETOS,
                    \PhpSigep\Model\RastrearObjeto::TIPO_RESULTADO_APENAS_O_ULTIMO_EVENTO)
                ) {
                    $evento = $rastro->getEventos();
                    $evento = array_shift($evento);
                    return $evento->getDescricao();
                } else {
                    return "Sem Eventos";
                }
            }

            if (empty($this->plp_id)) {
                return "Imprimir Etiqueta";
            }

            if (empty($this->etiqueta)) {
                return "Solicitar Etiqueta";
            }
        }
        return null;
    }

}