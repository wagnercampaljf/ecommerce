<?php

namespace lojista\controllers;

use common\models\Filial;
use common\models\Usuario;
use Yii;
use common\models\Lojista;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LojistaController implements the CRUD actions for Lojista model.
 */
class LojistaController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Lojista models.
     * @return mixed
     */
    public function actionIndex()
    {
        $id = Yii::$app->user->identity->getId();

        $dataProvider = new ActiveDataProvider([
            'query' => Lojista::find()->where(['id' => $id]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Updates an existing Lojista model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {
        $id = Yii::$app->user->identity->getId();
        $model = $this->findModel($id);

        if (Yii::$app->request->post()) {
            $data = Yii::$app->request->post('Lojista');
            $model->contrato_correios = $data['contrato_correios'];
            $model->senha_correios = $data['senha_correios'];
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionIntegrarB2w()
    {
        /* @var $usuario Usuario */
        $usuario = Yii::$app->user->getIdentity();
        $filial = $usuario->filial;
        $filial->integrar_b2w = true;
        $filial->save();

        return $this->redirect(['pedidos/index']);
    }


    /**
     * Finds the Lojista model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Lojista the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lojista::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
