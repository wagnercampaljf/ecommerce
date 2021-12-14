<?php

namespace backend\controllers;

use common\models\AnoModelo;
use common\models\Modelo;
use common\models\Subcategoria;
use Yii;
use common\models\Produto;
use backend\models\ProdutoSearch;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\base\Action;
use Livepixel\MercadoLivre\Meli;
use yii\helpers\ArrayHelper;
use common\models\ProdutoFilial;

/**
 * ProdutoController implements the CRUD actions for Produto model.
 */
class ProdutoController extends Controller
{
    
    const APP_ID = '3029992417140266';
    const SECRET_KEY = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'create'            => 'backend\actions\produto\CreateAction',
            'update'            => 'backend\actions\produto\UpdateAction',
	    'updateml'          => 'backend\actions\produto\UpdatemlAction',
            'criaalteraomie'    => 'backend\actions\produto\CriarAlterarOmieAction',
            'pedidoML'          => 'backend\actions\produto\PedidoMLAction',
            'duplicarproduto'   => 'backend\actions\produto\DuplicarProdutoAction',
            'delete'            => 'backend\actions\produto\DeleteAction',
        ];
    }

    /**
     * Lists all Produto models.
     * @return mixed
     */
    public function actionIndex($erro = '')
    {
        $searchModel = new ProdutoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'   => $searchModel,
            'dataProvider'  => $dataProvider,
            'erro'         => $erro,
        ]);
    }

    /**
     * Displays a single Produto model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

//    /**
//     * Deletes an existing Produto model.
//     * If deletion is successful, the browser will be redirected to the 'index' page.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * @author Otavio 04/11/2016
     * @param $q
     * @return array
     */
    public function actionGetAnoModelo($q)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $results = Modelo::find()
            ->select(['am.id', "CONCAT(modelo.nome,' ',am.nome) as text"])
            ->innerJoin("ano_modelo am", "am.modelo_id = modelo.id")
            ->andWhere([
                'like',
                'lower(modelo.nome)',
                strtolower($q)
            ])
            ->orderBy('am.nome DESC')
            ->createCommand()->queryAll();

        $out['results'] = array_values($results);
        return $out;
    }

    /**
     * Finds the Produto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Produto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($id)
    {
        if (($model = Produto::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionAtualizarml($id)
    {
        
        $model = $this->findModel($id);
        
        $produtos_filiais = ProdutoFilial::find()->andWhere(['=','produto_id',$id])->all();
        
        $mensagem = '<div class="text-primary h4">Produto atualizado no Mercado Livre com sucesso!</div>';
        $permalink = "";
        
        foreach ($produtos_filiais as $k => $produto_filial){
            
            if ($produto_filial->meli_id == null or $produto_filial->meli_id == ""){
                continue;
            }
            
            $produto_filial_mercado_livre = $produto_filial->meli_id;
            $refresh_token__meli_filial = $produto_filial->filial->refresh_token_meli;
            
            $texto_link = "Link para o ML (Principal)";
            if($produto_filial->produto_filial_origem_id != null){
                $texto_link = "Link para o ML (Secundária)";
                $produto_filial = ProdutoFilial::findOne($produto_filial->produto_filial_origem_id);
            }
            
            $meli = new Meli(static::APP_ID, static::SECRET_KEY);
            $user = $meli->refreshAccessToken($refresh_token__meli_filial);
            $response = ArrayHelper::getValue($user, 'body');
            
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                
                $meliAccessToken = $response->access_token;
                
                if (is_null($produto_filial->valorMaisRecente)) {
                    return $this->render('update', [
                        'model' => $model,
                        'erro' => "Produto sem valor cadastrado",
                    ]);
                }
                
                //$page = $this->render(__DIR__ . 'lojista/views/mercado-livre/produto.php', ['produto' => $model]);
                $title = Yii::t('app', '{nome} ({cod})', ['cod' => $produto_filial->produto->codigo_global, 'nome' => $produto_filial->produto->nome ]);
                
                //Update Item
                $body = [
                    "title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                    'attributes' =>[
                        [
                            'id' => 'PART_NUMBER',
                            'name' => 'Número da peça',
                            'value_id' => NULL,
                            'value_name' => $produto_filial->produto->codigo_global,
                            'value_struct' => NULL,
                            'attribute_group_id' => 'DFLT',
                            'attribute_group_name' => 'Outros',
                        ],
                        [
                            "id"=> "BRAND",
                            "name"=> "Marca",
                            "value_id"=> null,
                            "value_name"=> $produto_filial->produto->fabricante->nome,
                            "value_struct"=> null,
                            "attribute_group_id"=> "OTHERS",
                            "attribute_group_name"=> "Outros"
                        ],
                        [
                            "id" => "EAN",
                            "value_name" => $produto_filial->produto->codigo_barras
                        ],
                    ]
                    
                ];
                $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
                //echo "<pre>"; print_r($response); echo "<pre>"; die;
                if ($response['httpCode'] >= 300) {
                    $mensagem .= '<div class="text-danger h4">Título não atualizado no Mercado Livre</div>';
                    continue;
                }
                
                //1 para me2 (Mercado Envios)
                //2 para not_especified (a combinar)
                //3 para customizado
                
                switch ($produto_filial->envio) {
                    case 1:
                        $modo = "me2";
                        break;
                    case 2:
                        $modo = "not_specified";
                        break;
                    case 3:
                        $modo = "custom";
                        break;
                }
                $body = [
                    "shipping" => [
                        "mode"=> $modo,
                        "local_pick_up" => true,
                        "free_shipping" => false,
                        "free_methods" => [],
                    ],
                ];
                $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body,[]);
                if ($response['httpCode'] >= 300) {
                    $mensagem .= '<div class="text-danger h4">Modo de envio não atualizado no Mercado Livre</div>';
                    continue;
                }
                
                //Update Descrição
                //$body = ['text' => $page];
                //$response = $meli->put("items/{$model->meli_id}/description?access_token=" . $meliAccessToken, $body, []);
                
                $body = [
                    "pictures" => $produto_filial->produto->getUrlImagesML(),
                ];
                $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body,[]);
                if ($response['httpCode'] >= 300) {
                    $mensagem .= '<div class="text-danger h4">Imagens não atualizadas no Mercado Livre</div>';
                    continue;                    
                }
                
                //Update Item
                $body = [
                    "price" => round($produto_filial->getValorMercadoLivre(), 2),
                    "available_quantity" => $produto_filial->quantidade,
                    
                ];
                $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $mensagem .= '<div class="text-danger h4">Preço e quantidade não atualizados no Mercado Livre</div>';
                    continue; 
                }
                else{
                    $permalink = $permalink . '<div class="h4"><a class="text-success" href="'.ArrayHelper::getValue($response, 'body.permalink').'">'.$texto_link.'</a></div>';
                }
            }
        }
        
        return $this->render('update', [
            'model' => $model,
            'mensagem' => $mensagem,
            'link_mercado_livre' => $permalink,
        ]);
    }
}
