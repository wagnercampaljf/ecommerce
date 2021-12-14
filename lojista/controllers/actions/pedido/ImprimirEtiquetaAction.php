<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 16/03/2016
 * Time: 14:35
 */

namespace lojista\controllers\actions\pedido;

use common\models\Pedido;
use PhpSigep\Config;
use yii\web\BadRequestHttpException;

class ImprimirEtiquetaAction extends Action
{
    public function run($pedido_id)
    {
        /* @var $model Pedido */
        $model = Pedido::findOne($pedido_id);
        $encomenda = $this->getEncomenda($model);
        $remetente = $this->getRemetente($model->filial->nome, $model->filial->enderecoFilial);

        if (!$accessData = $this->getAccessData(Config::ENV_PRODUCTION)) {
            throw new BadRequestHttpException();
        }
        $config = new \PhpSigep\Config();
        $config->setAccessData($accessData);
        $config->setEnv(Config::ENV_PRODUCTION);
        $config->setCacheOptions(
            array(
                'storageOptions' => array(
                    // Qualquer valor setado neste atributo será mesclado ao atributos das classes
                    // "\PhpSigep\Cache\Storage\Adapter\AdapterOptions" e "\PhpSigep\Cache\Storage\Adapter\FileSystemOptions".
                    // Por tanto as chaves devem ser o nome de um dos atributos dessas classes.
                    'enabled' => false,
                    'ttl' => 10,
                    // "time to live" de 10 segundos
                    'cacheDir' => sys_get_temp_dir(),
                    // Opcional. Quando não inforado é usado o valor retornado de "sys_get_temp_dir()"
                ),
            )
        );
        \PhpSigep\Bootstrap::start($config);

        $plp = new \PhpSigep\Model\PreListaDePostagem();
        $plp->setAccessData($accessData);
        $plp->setEncomendas(array($encomenda));
        $plp->setRemetente($remetente);

        $logoFile = __DIR__ . '/../../../web/assets/global/img/logoCorreios.png';

        $pdf = new \PhpSigep\Pdf\CartaoDePostagem($plp, $model->plp_id, $logoFile);

        $pdf->render();
    }
}