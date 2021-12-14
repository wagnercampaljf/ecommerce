<?php

namespace frontend\controllers;

use common\models\Respostas;
use Yii;

class RespostasController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionResponder()
    {
        $model = new Respostas();

        if ($model->load(Yii::$app->request->post())) {
            $model->data_resposta = date("Y-m-d");
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', 'Sua resposta foi recebida com Sucesso! Obrigado!');
                return $this->goBack();
            } else {
                Yii::$app->getSession()->setFlash('error', array_pop($model->getErrors()));

                return $this->goBack([
                    'model' => $model,
                ]);
            }
        } else {
//            Yii::$app->getSession()->setFlash('error', "Você não devia estar aqui!");
            return $this->goBack([
                'model' => $model,
            ]);
        }
    }


}
