<?php

namespace backend\controllers;

use Yii;
use common\models\ValorProdutoFilial;
use common\models\ValorProdutoFilialSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\ProdutoFilial;
use common\models\Produto;
use common\models\Filial;
use yii\web\Response;

/**
 * ValorProdutoFilialController implements the CRUD actions for ValorProdutoFilial model.
 */
class ValorProdutoFilialController extends Controller
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
     * Lists all ValorProdutoFilial models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ValorProdutoFilialSearch();
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ValorProdutoFilial model.
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
     * Creates a new ValorProdutoFilial model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ValorProdutoFilial();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
	    $valor_produto_filial = ProdutoFilial::find()->andWhere(['=','id',$model->produto_filial_id])->one();
            $valor_produto_filial->quantidade = 99999;
            $valor_produto_filial->save();

	    return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ValorProdutoFilial model.
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
            return $this->render('update', ['model' => $model,]);
        }
    }

    /**
     * Deletes an existing ValorProdutoFilial model.
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
     * Finds the ValorProdutoFilial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ValorProdutoFilial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ValorProdutoFilial::  find()//findOne($id)
                                            //->select(['valor_produto_filial.id','produto_filial.filial_id as filial_id'])
                                            ->joinWith(['produtoFilial', 'produtoFilial.produto', 'produtoFilial.filial'])
                                            ->andWhere(['=','valor_produto_filial.id',$id])
                                            ->one()
            ) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionGetProduto($q, $id = null)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }
        if ($id > 0) {
            return ['results' => ['id' => $id, 'text' => Produto::findOne($id)->nome]];
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = Produto::find()
            ->select(['produto.id', 'produto.nome as text'])
            ->where([
                'like',
                'lower(produto.nome)',
                strtolower($q)
            ])
            ->orWhere([
                'lower(produto.id::VARCHAR)' =>  strtolower($q)
            ])
            ->limit(10)
            ->createCommand()
            ->queryAll();
            $out['results'] = array_values($results);
        }
        
        return $out;
    }
    
    public function actionGetFilial($q, $id = null)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }
        if ($id > 0) {
            return ['results' => ['id' => $id, 'text' => Filial::findOne($id)->nome]];
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = Filial::find()
            ->select(['filial.id', 'filial.nome as text'])
            ->where([
                'like',
                'lower(filial.nome)',
                strtolower($q)
            ])
            ->orWhere([
                'lower(filial.id::VARCHAR)' =>  strtolower($q)
            ])
            ->limit(10)
            ->createCommand()
            ->queryAll();
            $out['results'] = array_values($results);
        }
        
        return $out;
    }
    
    public function actionGetProdutoFilial($q, $id = null)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }
        if ($id > 0) {
            return ['results' => ['id' => $id, 'text' => ProdutoFilial::findOne($id)->filial->nome ." - ". ProdutoFilial::findOne($id)->produto->nome ."(". ProdutoFilial::findOne($id)->produto->codigo_global . ")"]];
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = ProdutoFilial::find()
            ->select(['produto_filial.id', "(coalesce((filial.nome),'') ||' - '|| coalesce((produto.nome),'')|| '('|| coalesce((produto.codigo_global),'')||')') as text"])
            ->joinWith(['produto', 'filial'])
            ->where([
                'like',
                'lower(produto.nome)',
                strtolower($q)
            ])
            ->orWhere([
                'lower(produto_filial.id::VARCHAR)' =>  strtolower($q)
            ])
	    ->orWhere(['like', 'produto.codigo_global', $q])
	    ->andWhere(['<>', 'filial_id', 43])
            ->limit(10)
            ->createCommand()
            ->queryAll();
            $out['results'] = array_values($results);
            
        }
        
        return $out;
    }
}
