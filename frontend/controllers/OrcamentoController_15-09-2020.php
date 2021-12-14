<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 09/12/2015
 * Time: 17:27
 */

namespace frontend\controllers;

use common\models\Orcamento;
use Yii;

class OrcamentoController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model = new Orcamento();
        if ($model->load(Yii::$app->request->post()) && $model->contact('sac@pecaagora.com')) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}

?>