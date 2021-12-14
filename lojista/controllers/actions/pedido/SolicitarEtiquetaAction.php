<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 16/03/2016
 * Time: 16:34
 */

namespace lojista\controllers\actions\pedido;


use common\models\Pedido;
use PhpSigep\Config;
use PhpSigep\Model\ServicoDePostagem;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;

class SolicitarEtiquetaAction extends Action
{

    public function run($pedido_id)
    {
        set_time_limit(180);
        /* @var $model Pedido */
        $model = $this->findModel($pedido_id);

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

        $params = new \PhpSigep\Model\SolicitaEtiquetas();
        $params->setQtdEtiquetas(1);
        $params->setServicoDePostagem(ArrayHelper::getValue(self::servico_postagem, $model->transportadora->codigo));
        $params->setAccessData($accessData);

        $phpSigep = new \PhpSigep\Services\SoapClient\Real();

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $result = $phpSigep->solicitaEtiquetas($params);
            if (!$result->hasError()) {
                $result = ($result->getResult());
                $etiqueta = array_shift($result);

                $model->etiqueta = $etiqueta->getEtiquetaSemDV();
                if ($model->save()) {
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                }
            } else {
                Yii::$app->session->setFlash('error', $result->getErrorMsg());
            }

            return $this->controller->redirect(['view', 'id' => $pedido_id]);
        } catch (\Exception $e) {
            throw new $e;
        }
    }

}