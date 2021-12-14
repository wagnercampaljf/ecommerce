<?php

namespace frontend\controllers;

use Yii;
use common\models\FormularioGarantia;
use frontend\models\FormularioCarantiaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FormularioGarantiaController implements the CRUD actions for FormularioGarantia model.
 */
class FormularioGarantiaController extends Controller
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
     * Lists all FormularioGarantia models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FormularioCarantiaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FormularioGarantia model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new FormularioGarantia model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FormularioGarantia();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {


            $texto_cabecalho = "<h2>Bom dia!</h2><br><h2>Segue dados do Formulário de garantia:</h2><br>";

            $texto_html = "<table border>
                            <tr><td><b>ID</b></td><td>$model->id</td></tr>
                            <tr><td><b>*Nome:</b></td><td>$model->nome</td></tr>
                            <tr><td><b>*Email:</b></td><td><a>$model->email</a></td></tr>
                            <tr><td><b>*Data Compra:</b></td><td>$model->data_compra</td></tr>
                            <tr><td><b>*Razão Social:</b></td><td>$model->razao_social</td></tr>
                            <tr><td><b>*NR.NF.Compra:</b></td><td>$model->nr_nf_compra</td></tr>
                            <tr><td><b>*Código peça 6 dígitos:</b></td><td>$model->codigo_peca_seis_digitos</td></tr>
                            <tr><td><b>*Modelo do Veículo:</b></td><td>$model->modelo_do_veiculo</td></tr>
                            <tr><td><b>*Ano:</b></td><td>$model->ano</td></tr>
                            <tr><td><b>*Chassi:</b></td><td>$model->chassi</td></tr>
                            <tr><td><b>*Número de série do motor:</b></td><td>$model->numero_de_serie_do_motor</td></tr>
                            <tr><td><b>*Data Aplicação:</b></td><td>$model->data_aplicacao</td></tr>
                            <tr><td><b>*KM Montagem:</b></td><td>$model->km_montagem</td></tr>
                            <tr><td><b>*KM Defeito:</b></td><td>$model->km_defeito</td></tr>
                            <tr><td><b>*Contato:</b></td><td>$model->contato</td></tr>
                            <tr><td><b>*Telefone:</b></td><td>$model->telefone</td></tr>
                            <tr><td><b>*Descrição do defeito apresentado:</b></td><td>$model->descricao_do_defeito_apresentado</td></tr>
                        </table>";

            $texto_rodape = "<br><br><h3>Atenciosamente.</h3> <br>";

            \Yii::$app->mailer  ->compose()
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                //->setTo(["wagnercampaljf@yahoo.com.br","dev.pecaagora@gmail.com","compras.pecaagora@gmail.com","dev2.pecaagora@gmail.com"])
                ->setTo(["assistencia.pecaagora@gmail.com",])
                ->setSubject(\Yii::$app->name . ' - Garantia '.$model->nome)
                //->setTextBody($texto)
                ->setHtmlBody($texto_cabecalho . $texto_html . $texto_rodape)
                ->send();
            return $this->redirect(['/site/index', 'id' => $model->id]);

         } else {
             return $this->render('create', [
                 'model' => $model,
             ]);
         }
     }

     /**
      * Updates an existing FormularioGarantia model.
      * If update is successful, the browser will be redirected to the 'view' page.
      * @param integer $id
      * @return mixed
      */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing FormularioGarantia model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the FormularioGarantia model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FormularioGarantia the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FormularioGarantia::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
