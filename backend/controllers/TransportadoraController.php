<?php

namespace backend\controllers;

use Yii;
use common\models\Transportadora;
use common\models\TransportadoraSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use console\controllers\actions\omie\Omie;
use yii\helpers\ArrayHelper;

/**
 * TransportadoraController implements the CRUD actions for Transportadora model.
 */
class TransportadoraController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Transportadora models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TransportadoraSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Transportadora model.
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
     * Creates a new Transportadora model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Transportadora();

        if ($model->load(Yii::$app->request->post())) {

            $acessoOmie = [
                '468080198586'  => '7b3fb2b3bae35eca3b051b825b6d9f43',
                '469728530271'  => '6b63421c9bb3a124e012a6bb75ef4ace',
                '1017311982687' => '78ba33370fac6178da52d42240591291',
                '1758907907757' => '0a69c9b49e5a188e5f43d5505f2752bc'
            ];

            $i = 0;
            $filial = [96, 94, 95, 93];

            foreach ($acessoOmie as $key => $value) {

                $model = new Transportadora();
                $model->load(Yii::$app->request->post());
                $model->filial_id = $filial[$i];
                $model->save(false);
                $i++;

                $APP_KEY_OMIE              = $key;
                $APP_SECRET_OMIE           = $value;

                $omie = new Omie(1, 1);

                $body = [
                    "call" => "ListarClientes",
                    "app_key" => $APP_KEY_OMIE,
                    "app_secret" => $APP_SECRET_OMIE,
                    "param" => [
                        "pagina" => 1,
                        "registros_por_pagina" => 1,
                        "apenas_importado_api" => "N",
                        "clientesFiltro" => [
                            "cnpj_cpf" => $model->cnpj,
                        ]
                    ]
                ];
                $responseOmie = $omie->consulta("/api/v1/geral/clientes/?JSON=", $body);

                if (ArrayHelper::getValue($responseOmie, 'httpCode') == 200) {
                    $model->codigo_omie = $responseOmie['body']['clientes_cadastro'][0]['codigo_cliente_omie'];
                    $model->save(false);
                    continue;
                } else {

                    $body = [
                        "call" => "IncluirCliente",
                        "app_key" => $APP_KEY_OMIE,
                        "app_secret" => $APP_SECRET_OMIE,
                        "param" => [
                            "codigo_cliente_integracao" => $model->id,
                            "razao_social" => $model->razao_social,
                            "cnpj_cpf" => $model->cnpj,
                            "nome_fantasia" => $model->nome,
                            "email" => $model->email,
                        ]
                    ];

                    $responseOmie = $omie->consulta("/api/v1/geral/clientes/?JSON=", $body);

                    if (ArrayHelper::getValue($responseOmie, 'httpCode') !== 200) {
                        echo '<pre>';
                        print_r($responseOmie);
                        echo '</pre>';
                        die;
                    }

                    $model->codigo_omie = $responseOmie['body']['codigo_cliente_omie'];
                    $model->save(false);
                }
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Transportadora model.
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
     * Deletes an existing Transportadora model.
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
     * Finds the Transportadora model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transportadora the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transportadora::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
