<?php

namespace lojista\controllers;

use common\models\FilialTransportadora;
use common\models\Transportadora;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * TransportadoraController implements the CRUD actions for FilialTransportadora model.
 */
class TransportadoraController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'get'],
                ],
            ],
        ];
    }

    /**
     * Lists all FilialTransportadora models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => FilialTransportadora::find()->andWhere(['filial_id' => Yii::$app->user->identity->filial_id]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new FilialTransportadora model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FilialTransportadora();
        $transportadoras = Transportadora::find()->all();
        if (Yii::$app->request->post('FilialTransportadora')) {
            $array = ArrayHelper::map(Yii::$app->request->post('FilialTransportadora'), 'transportadora_id',
                'dias_postagem');

            if (empty($array)) {
                Yii::$app->session->setFlash('error', 'É necessário selecionar pelo menos uma transportadora.');

                return $this->redirect(['index']);
            }

            foreach ($transportadoras as $transportadora) {
                if (array_key_exists($transportadora->id, $array)) {
                    $this->criaFilialTransportadora($transportadora->id, $array[$transportadora->id]);
                } else {
                    $this->deleteFilialTransportadora($transportadora->id);
                }
            }

            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    private function criaFilialTransportadora($transportadira_id, $dias_postagem)
    {
        $dados['filial_id'] = Yii::$app->user->identity->filial_id;
        $dados['transportadora_id'] = $transportadira_id;
        $nr_dias = $dias_postagem;
        if (!$model = FilialTransportadora::findOne($dados)) {
            $model = new FilialTransportadora();
        }
        $model->load($dados, '');
        $model->dias_postagem = $nr_dias;
        $model->save();
    }

    /**
     * Deletes an existing FilialTransportadora model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    private function deleteFilialTransportadora($transportadora_id)
    {
        $filialTransportadora = FilialTransportadora::findOne([
            'filial_id' => Yii::$app->user->identity->filial_id,
            'transportadora_id' => $transportadora_id
        ]);

        if (isset($filialTransportadora)) {
            $filialTransportadora->delete();
        }
    }

    /**
     * Finds the FilialTransportadora model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $filial_id
     * @param integer $transportadora_id
     * @return FilialTransportadora the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($filial_id, $transportadora_id)
    {
        if (($model = FilialTransportadora::findOne([
                'filial_id' => $filial_id,
                'transportadora_id' => $transportadora_id
            ])) !== null
        ) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
