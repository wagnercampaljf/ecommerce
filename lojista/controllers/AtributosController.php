<?php

namespace lojista\controllers;

use common\models\Caracteristica;
use common\models\CaracteristicaFilial;
use common\models\Filial;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * AtributosController implements the CRUD actions for CaracteristicaFilial model.
 */
class AtributosController extends Controller
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
     * Lists all CaracteristicaFilial models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => CaracteristicaFilial::find()->andWhere(['filial_id' => Yii::$app->user->identity->filial_id]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new CaracteristicaFilial model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CaracteristicaFilial();

        if (Yii::$app->request->post('CaracteristicaFilial')) {
            foreach (Yii::$app->request->post('CaracteristicaFilial') as $dados) {
                $dados['filial_id'] = Yii::$app->user->identity->filial_id;
                $obs = ArrayHelper::remove($dados, 'observacao');
                if (!$model = CaracteristicaFilial::findOne($dados)) {
                    $model = new CaracteristicaFilial();
                }
                $model->load($dados, '');
                $model->observacao = $obs;
                $model->save();
            }

            $filial = Yii::$app->user->identity->filial;
            $caracteristicas = array_diff(
                ArrayHelper::getColumn($filial->caracteristicas, 'id'),
                ArrayHelper::getColumn(Yii::$app->request->post('CaracteristicaFilial'), 'caracteristica_id')
            );
            foreach ($caracteristicas as $id) {
                $filial->unlink('caracteristicas', Caracteristica::findOne($id), true);
            }

            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CaracteristicaFilial model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id, Yii::$app->user->identity->filial_id)->delete();

        if (Yii::$app->request->isAjax) {
            return true;
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the CaracteristicaFilial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $caracteristica_id
     * @param integer $filial_id
     * @return CaracteristicaFilial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($caracteristica_id, $filial_id)
    {
        if (($model = CaracteristicaFilial::findOne([
                'caracteristica_id' => $caracteristica_id,
                'filial_id' => $filial_id
            ])) !== null
        ) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
