<?php

namespace backend\controllers;

use common\models\Produto;
use yii\web\Response;
use Yii;
use common\models\Imagens;
use common\models\ImagensSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use Livepixel\MercadoLivre\Meli;
use yii\helpers\ArrayHelper;
use common\models\ProdutoFilial;

/**
 * ImagensController implements the CRUD actions for Imagens model.
 */
class ImagensController extends Controller
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
        ];
    }

    /**
     * Lists all Imagens models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ImagensSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Imagens model.
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
     * Creates a new Imagens model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Imagens();
        if ($model->load(Yii::$app->request->post())) {
            $this->uploadImages($model);
//            echo "<pre>";
//            $model->validate();
//            var_dump($model->errors);
//            die;
            if ($model->save()) {
                // $this->ImagemReferencia($model);
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                die("oi2");
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Imagens model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $this->uploadImages($model);
            if ($model->save()) {
                // $this->ImagemReferencia($model);
                return $this->redirect(['index']);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Imagens model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    public function actionGetImg($id)
    {
        $model = $this->findModel($id);

        header('Content-Type: image/jpeg');
        return base64_decode(stream_get_contents($model->imagem));
    }

    /**
     * Finds the Imagens model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Imagens the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Imagens::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param $model Imagens
     */
    private function uploadImages(&$model)
    {
        if ($file = UploadedFile::getInstance($model, 'imagem')) {
            $model->imagem = base64_encode(file_get_contents($file->tempName));
        } else {
            // Erro na imagem
           $model->imagem = $model->oldAttributes['imagem'];

        }
        if ($file = UploadedFile::getInstance($model, 'imagem_sem_logo')) {
            $model->imagem_sem_logo = base64_encode(file_get_contents($file->tempName));
        } else {
            $model->imagem_sem_logo = $model->oldAttributes['imagem_sem_logo'];
        }

    }

    /**
     * @author Otavio Augusto 26/09/17
     * @param $q
     * @return array
     */

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
    
    public function actionAtualizarml($id)
    {
        $model = $this->findModel($id);
        
        $produtos_filiais = ProdutoFilial::find()->andWhere(['=','produto_id',$model->produto_id])->all();
        
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

    // public function ImagemReferencia($model, $method = null)
    // {
    //     if ($method == 'delete') {
    //         unlink('/var/www/imagens_produto/produto_' . $model->produto_id . '/' . $model->produto_id . '_' . $model->ordem . '_sem_logo.webp');
    //         unlink('/var/www/imagens_produto/produto_' . $model->produto_id . '/' . $model->produto_id . '_' . $model->ordem . '.webp');
    //     } else {

    //         if (!file_exists('/var/www/imagens_produto/produto_' . $model['produto_id'])) {
    //             mkdir('/var/www/imagens_produto/produto_' . $model['produto_id'], 0777, true);
    //         }

    //         if (!empty($model)) {

    //             $caminho = "https://www.pecaagora.com/site/get-link?produto_id=" . $model->produto_id . '&' . 'ordem=' . $model->ordem;
    //             copy($caminho, '/var/www/imagens_produto/produto_' . $model->produto_id . '/' . $model->produto_id . '_' . $model->ordem . ".webp");

    //             if ($model->imagem_sem_logo !== null) {
    //                 $caminho = "https://www.pecaagora.com/site/get-link-sem-logo?produto_id=" . $model->produto_id . '&' . 'ordem=' . $model->ordem;
    //                 copy($caminho, '/var/www/imagens_produto/produto_' . $model->produto_id . '/' . $model->produto_id . '_' . $model->ordem . "_sem_logo.webp");
    //             }

    //             if ($model->imagem_zoom !== null) {
    //                 $caminho = "https://www.pecaagora.com/site/get-link-zoom?produto_id=" . $model->produto_id . '&' . 'ordem=' . $model->ordem;
    //                 copy($caminho, '/var/www/imagens_produto/produto_' . $model->produto_id . '/' . $model->produto_id . '_' . $model->ordem . "_zoom.webp");
    //             }
    //         }
    //     }
    // }
}
