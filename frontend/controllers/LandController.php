<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 27/04/2016
 * Time: 17:50
 */

namespace frontend\controllers;

use common\models\Portal;
use Yii;

class LandController extends \yii\web\Controller
{
    public function actionPortal()
    {
        $model = new Portal();
        if ($model->load(Yii::$app->request->post()) && $model->contact('sac@pecaagora.com')) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }

        return $this->render('portal', [
            'model' => $model,
        ]);
    }

}
