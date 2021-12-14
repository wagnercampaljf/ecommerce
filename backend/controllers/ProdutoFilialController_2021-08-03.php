<?php

namespace backend\controllers;

use Yii;
use common\models\ProdutoFilial;
use common\models\ProdutoFilialSearch;
use common\models\ValorProdutoFilial;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Produto;
use common\models\Filial;
use yii\web\Response;
use Livepixel\MercadoLivre\Meli;
use yii\helpers\ArrayHelper;


/**
 * ProdutoFilialController implements the CRUD actions for ProdutoFilial model.
 */
class ProdutoFilialController extends Controller
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
     * Lists all ProdutoFilial models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProdutoFilialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProdutoFilial model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionRelatorios()
    {
        return $this->render('relatorios');
    }


    /**
     * Creates a new ProdutoFilial model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProdutoFilial();

        if ($model->load(Yii::$app->request->post())) {

            $registroDuplicado = ProdutoFilial::findOne(['produto_id' => $model->produto_id, 'filial_id' => $model->filial_id]);
            if ($registroDuplicado) {
                return $this->render('create', [
                    'model' => $model,
                    'error' => 'O produto já se encontra associado a esta filial !'
                ]);
            } else {
                $model->save();
                $produto_filial_conta_diplicada                             = new ProdutoFilial;
                $produto_filial_conta_diplicada->quantidade                 = 0;
                $produto_filial_conta_diplicada->filial_id                  = 98;
                $produto_filial_conta_diplicada->produto_id                 = $model->produto_id;
                $produto_filial_conta_diplicada->envio                      = $model->envio;
                $produto_filial_conta_diplicada->produto_filial_origem_id   = $model->id;
                $produto_filial_conta_diplicada->save();

                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ProdutoFilial model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //return $this->redirect(['view', 'id' => $model->id]);
            return $this->render('update', [
                'model' => $model,
                'mensagem' => '<div class="text-primary h4">Produto atualizado com sucesso!</div>',
            ]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ProdutoFilial model.
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
     * Finds the ProdutoFilial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProdutoFilial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProdutoFilial::findOne($id)) !== null) {
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
                ->select(['produto.id', "produto.nome||' ('||produto.codigo_global||')' as text"])
                ->where([
                    'like',
                    'lower(produto.nome)',
                    strtolower($q)
                ])
                ->orWhere([
                    'lower(produto.id::VARCHAR)' =>  strtolower($q)
                ])
                ->orWhere(['like', 'produto.codigo_global', $q])
                //->andWhere(['<>','id', 43])
                //->andWhere(['<>','id', 98])
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
                //->andWhere(['<>','id', 43])
                //->andFilterWhere(['NOT IN','filial.id',[94,95,96]])
                ->andWhere(['<>', 'id', 98])
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

        $mensagem = $model->produto->atualizarMercadoLivre();

        return $this->render('update', [
            'model' => $model,
            'mensagem' => $mensagem,
            'link_mercado_livre' => "",
        ]);


        $produto_filial_mercado_livre = $model->meli_id;
        $refresh_token__meli_filial = $model->filial->refresh_token_meli;

        /*if($model->produto_filial_origem_id != null){
            $model = ProdutoFilial::findOne($model->produto_filial_origem_id);
        }*/

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken($refresh_token__meli_filial);
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {

            $permalink         = "";
            $permalink_duplicada = "";
            $erro_retorno    = "";
            $mensagem_retorno    = "";

            $meliAccessToken = $response->access_token;

            if (is_null($model->valorMaisRecente)) {
                return $this->render('update', ['model' => $model, 'erro' => "Produto sem valor cadastrado",]);
            }

            //$page = $this->render(__DIR__ . 'lojista/views/mercado-livre/produto.php', ['produto' => $model]);
            $page = $model->produto->nome . "\n\nAPLICACAO:\n\n" . $model->produto->aplicacao . "\n\n" . $model->produto->aplicacao_complementar . "\n\nDICAS:\n\n* Lado Esquerdo    o do Motorista.\n* Lado Direito    o do Passageiro.";
            $page = str_replace("'", "", $page);
            $page = str_replace("<p>", " ", $page);
            $page = str_replace("</p>", " ", $page);
            $page = str_replace("<br>", "\n", $page);
            $page = str_replace("<BR>", "\n", $page);
            $page = str_replace("<br/>", "\n", $page);
            $page = str_replace("<BR/>", "\n", $page);
            $page = str_replace("<strong>", " ", $page);
            $page = str_replace("</strong>", " ", $page);
            $page = str_replace('<span class="redactor-invisible-space">', " ", $page);
            $page = str_replace('</span>', " ", $page);
            $page = str_replace('<span>', " ", $page);
            $page = str_replace('<ul>', " ", $page);
            $page = str_replace('</ul>', " ", $page);
            $page = str_replace('<li>', "\n", $page);
            $page = str_replace('</li>', " ", $page);
            $page = str_replace('<p style="margin-left: 20px;">', " ", $page);
            $page = str_replace('<h1>', " ", $page);
            $page = str_replace('</h1>', " ", $page);
            $page = str_replace('<h2>', " ", $page);
            $page = str_replace('</h2>', " ", $page);
            $page = str_replace('<h3>', " ", $page);
            $page = str_replace('</h3>', " ", $page);
            $page = str_replace('<span class="redactor-invisible-space" style="">', " ", $page);
            $page = str_replace('>>>', "(", $page);
            $page = str_replace('<<<', ")", $page);
            $page = str_replace('<u>', " ", $page);
            $page = str_replace('</u>', "\n", $page);
            $page = str_replace('<b>', " ", $page);
            $page = str_replace('</b>', " ", $page);
            $page = str_replace('<o:p>', " ", $page);
            $page = str_replace('</o:p>', " ", $page);
            $page = str_replace('<p style="margin-left: 40px;">', " ", $page);
            $page = str_replace('<del>', " ", $page);
            $page = str_replace('</del>', " ", $page);
            $page = str_replace('/', "-", $page);
            $page = str_replace('<em>', " ", $page);
            $page = str_replace('<-em>', " ", $page);

            $title = Yii::t('app', '{nome} ({cod})', ['cod' => $model->produto->codigo_global, 'nome' => $model->produto->nome]);

            //Update Item
            $body = [
                "title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                'attributes' => [
                    [
                        'id' => 'PART_NUMBER',
                        'name' => 'Número da peça',
                        'value_id' => NULL,
                        'value_name' => $model->produto->codigo_global,
                        'value_struct' => NULL,
                        'attribute_group_id' => 'DFLT',
                        'attribute_group_name' => 'Outros',
                    ],
                    [
                        "id" => "BRAND",
                        "name" => "Marca",
                        "value_id" => null,
                        "value_name" => $model->produto->fabricante->nome,
                        "value_struct" => null,
                        "attribute_group_id" => "OTHERS",
                        "attribute_group_name" => "Outros"
                    ],
                    [
                        "id" => "EAN",
                        "value_name" => $model->produto->codigo_barras
                    ],
                ]
            ];
            $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
            //echo "<pre>"; print_r($response); echo "<pre>"; die;
            if ($response['httpCode'] >= 300) {
                //return $this->render('update', ['model' => $model, 'erro' => '<div class="text-danger h4">Título não atualizado no Mercado Livre</div>', ]);
                $erro_retorno .= '<div class="text-danger h4">Titulo nao atualizado no Mercado Livre</div><br>';
            } else {
                $mensagem_retorno    .= '<div class="text-primary h4">Titulo atualizado no Mercado Livre</div><br>';
                $permalink         = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML (Principal)</a></div>';
            }

            //1 para me2 (Mercado Envios)
            //2 para not_especified (a combinar)
            //3 para customizado
            switch ($model->envio) {
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
                    "mode" => $modo,
                    "local_pick_up" => true,
                    "free_shipping" => false,
                    "free_methods" => [],
                ],
            ];
            $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
            //echo "<pre>"; print_r($response); echo "</pre>"; die;
            if ($response['httpCode'] >= 300) {
                //return $this->render('update', ['model' => $model, 'erro' => '<div class="text-danger h4">Modo de envio não atualizado no Mercado Livre</div>',]);
                $erro_retorno = '<div class="text-danger h4">Modo de envio nao atualizado no Mercado Livre</div><br>';
            } else {
                $mensagem_retorno       .= '<div class="text-primary h4">Modo de envio atualizado no Mercado Livre</div><br>';
                $permalink         = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML (Principal)</a></div>';
            }

            //Update Descrição
            //$body = ['text' => $page];
            //$response = $meli->put("items/{$model->meli_id}/description?access_token=" . $meliAccessToken, $body, []);

            $body = [
                "pictures" => $model->produto->getUrlImagesML(),
            ];
            $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
            if ($response['httpCode'] >= 300) {
                //return $this->render('update', ['model' => $model, 'erro' => '<div class="text-danger h4">Imagens não atualizadas no Mercado Livre</div>',]);
                $erro_retorno = '<div class="text-danger h4">Imagens n  o atualizadas no Mercado Livre</div><br>';
            } else {
                $mensagem_retorno       .= '<div class="text-primary h4">Imagens atualizadas no Mercado Livre</div><br>';
                $permalink         = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML (Principal)</a></div>';
            }

            //Update Preco
            $body = [
                "price" => round($model->getValorMercadoLivre(), 2),
                "available_quantity" => $model->quantidade,
            ];
            $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
            //echo "<pre>"; print_r($response); echo "</pre>";
            if ($response['httpCode'] >= 300) {
                //return $this->render('update', ['model' => $model, 'erro' => '<div class="text-danger h4">Preço e quantidade não atualizados no Mercado Livre</div>',]);
                $erro_retorno = '<div class="text-danger h4">Preco nao atualizadas no Mercado Livre</div><br>';
            } else {
                $mensagem_retorno       .= '<div class="text-primary h4">Preco atualizado no Mercado Livre</div><br>';
                $permalink          = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML (Principal)</a></div>';

                $produtos_filiais_outros = ProdutoFilial::find()->andWhere(['=', 'produto_filial_origem_id', $model->id])->all();

                foreach ($produtos_filiais_outros as $produto_filial_outro) {
                    $preco_outro = round(round($model->getValorMercadoLivre(), 2), 2);

                    $user_outro     = $meli->refreshAccessToken($produto_filial_outro->filial->refresh_token_meli);
                    $response_outro = ArrayHelper::getValue($user_outro, 'body');

                    if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 400) {

                        $meliAccessToken_outro = $response_outro->access_token;
                        if ($produto_filial_outro->meli_id != null) {
                            $body = [
                                "available_quantity" => $model->quantidade,
                                "price" => $preco_outro,
                                "shipping" => [
                                    "mode" => $modo,
                                    "local_pick_up" => true,
                                    "free_shipping" => false,
                                    "free_methods" => [],
                                ],
                            ];
                            $response_outro = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, []);
                            //echo "<pre>"; print_r($response); echo "<pre>"; die;
                            if ($response_outro['httpCode'] >= 300) {
                                //return $this->render('update', [ 'model' => $model, 'erro' => '<div class="text-text-warning h4">Conta principal do Mercado Livre altarada com sucesso, conta secundária não alterada</div>',]);
                                $erro_retorno = '<div class="text-text-warning h4">Conta secundaria nao alterada</div>';
                            } else {
                                $mensagem_retorno       .= '<div class="text-primary h4">Conta secundaria alterada</div><br>';
                                $permalink_duplicada = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response_outro, 'body.permalink') . '">Link para o ML (Secundária)</a></div>';
                            }
                        } else {
                            $title = Yii::t('app', '{nome} ({cod})', ['cod' => $model->produto->codigo_global, 'nome' => $model->produto->nome]);
                            $condicao = ($model->produto->e_usado) ? "used" : "new";
                            $body = [
                                "title" => utf8_encode((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                                "category_id" => utf8_encode(ArrayHelper::getValue($response, 'body.category_id')),
                                "listing_type_id" => "bronze",
                                "currency_id" => "BRL",
                                "price" => $preco_outro,
                                "available_quantity" => utf8_encode($model->quantidade),
                                "condition" => $condicao,
                                "pictures" => $model->produto->getUrlImagesML(),
                                "shipping" => [
                                    "mode" => "me2",
                                    "local_pick_up" => true,
                                    "free_shipping" => false,
                                    "free_methods" => [],
                                ],
                                "sale_terms" => [
                                    [
                                        "id" => "WARRANTY_TYPE",
                                        "value_id" => "2230280"
                                    ],
                                    [
                                        "id" => "WARRANTY_TIME",
                                        "value_name" => "3 meses"
                                    ]
                                ]
                            ];
                            $response_outro = $meli->post("items?access_token=" . $meliAccessToken_outro, $body);
                            if ($response_outro['httpCode'] < 300) {
                                $produto_filial_outro->meli_id = $response_outro['body']->id;
                                $produto_filial_outro->save();
                                $mensagem_retorno       .= '<div class="text-primary h4">Conta secundaria, produto criado</div><br>';
                                $permalink_duplicada = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response_outro, 'body.permalink') . '">Link para o ML (Secundária)</a></div>';
                            } else {
                                //return $this->render('update', ['model' => $model,'erro' => '<div class="text-text-warning h4">Conta principal do Mercado Livre altarada com sucesso, conta secundária não criado</div>',]);
                                $erro_retorno .= '<div class="text-text-warning h4">Conta secundaria, produto nao criado</div>';
                            }
                        }
                    }
                }
            }

            //echo "<pre>"; print_r($erro_retorno); echo "</pre>";
            //echo "<pre>"; print_r($mensagem_retorno); echo "</pre>";
            //echo "<pre>"; print_r($permalink . "<br>" . $permalink_duplicada); echo "</pre>";

            return $this->render('update', [
                'model'         => $model,
                'erro'             => $erro_retorno,
                'mensagem'         => $mensagem_retorno,
                'link_mercado_livre'     => $permalink . "<br>" . $permalink_duplicada,
            ]);
        }
    }

    public function actionResetarml($id)
    {

        $model = $this->findModel($id);

        $produto_filial_mercado_livre = $model->meli_id;
        $refresh_token__meli_filial = $model->filial->refresh_token_meli;

        if ($model->produto_filial_origem_id != null) {
            $model = ProdutoFilial::findOne($model->produto_filial_origem_id);
        }

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken($refresh_token__meli_filial);
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {

            $meliAccessToken = $response->access_token;

            if (is_null($model->valorMaisRecente)) {
                Yii::error("Produto Filial: {$model->produto->nome}({$model->id}), não possui valor", 'error_yii');

                return $this->render('update', [
                    'model' => $model,
                    'erro' => "Produto sem valor cadastrado",
                ]);
            }

            $body = [
                "available_quantity" => 0,
            ];
            $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
            if ($response['httpCode'] >= 300) {
                return $this->render('update', [
                    'model' => $model,
                    'erro' => '<div class="text-danger h4">Quantidade não atualizado no Mercado Livre</div>',
                ]);
            } else {

                $model->meli_id = null;
                if ($model->save()) {
                    $subcategoriaMeli = $model->produto->subcategoria->meli_id;
                    if (!isset($subcategoriaMeli)) {
                        return $this->render('update', [
                            'model' => $model,
                            'erro' => "Produto sem subcategoria",
                        ]);
                    }

                    //$page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',['produto' => $produtoFilial]);

                    $title = Yii::t('app', '{nome} ({cod})', [
                        'cod' => $model->produto->codigo_global,
                        'nome' => $model->produto->nome
                    ]);

                    switch ($model->envio) {
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
                    $condicao = ($model->produto->e_usado) ? "used" : "new";
                    $body = [
                        "title" => (strlen($title) <= 60) ? $title : substr($title, 0, 60),
                        "category_id" => utf8_encode($subcategoriaMeli),
                        "listing_type_id" => "bronze",
                        "currency_id" => "BRL",
                        "price" => utf8_encode(round($model->getValorMercadoLivre(), 2)),
                        "available_quantity" => utf8_encode($model->quantidade),
                        "seller_custom_field" => utf8_encode($model->id),
                        "condition" => $condicao,
                        //"description" => utf8_encode($page),
                        //"description" => ["plain_text" => utf8_encode($page)],
                        //"description" => ["plain_text" => $page],
                        "pictures" => $model->produto->getUrlImagesML(),
                        "shipping" => [
                            "mode" => $modo,
                            "local_pick_up" => true,
                            "free_shipping" => false,
                            "free_methods" => [],
                        ],
                        "warranty" => "6 meses",
                    ];

                    $response = $meli->post("items?access_token=" . $meliAccessToken, $body);
                    if ($response['httpCode'] >= 300) {
                        return $this->render('update', [
                            'model' => $model,
                            'erro' => '<div class="text-danger h4">Produto não criado no ML</div>',
                        ]);
                    } else {
                        $model->meli_id = $response['body']->id;
                        if (!$model->save()) {
                            return $this->render('update', [
                                'model' => $model,
                                'erro' => '<div class="text-danger h4">meli_id não salvo no estoque</div>',
                            ]);
                        } else {
                            return $this->render('update', [
                                'model' => $model,
                                'mensagem' => '<div class="text-primary h4">Produto criado no Mercado Livre com sucesso!</div>',
                                'link_mercado_livre' => '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML</a></div>',
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function actionGetEValorBloqueado($id)
    {
        $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $id])->one();
        $e_valor_bloqueado = 'null';
        if ($produto_filial) {
            if ($produto_filial->produto->e_valor_bloqueado) {
                $e_valor_bloqueado = 'true';
            } else {
                $e_valor_bloqueado = 'false';
            }
        }
        $retorno = '{"e_valor_bloqueado":' . $e_valor_bloqueado . '}';
        return $retorno;
    }


    public function actionGetValorCompra($id)
    {
        $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=', 'produto_filial_id', $id])->orderBy(['dt_inicio' => SORT_DESC])->one();

        $valor_retorno = 0;

        /*if($valor_produto_filial) {

            $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $id])->one();

            if ($produto_filial->filial_id ==43) {

                $valor_retorno = $valor_produto_filial->valor_compra*0.97;

            }else{$valor_retorno = $valor_produto_filial->valor_compra;}


        }*/

        if ($valor_produto_filial) {

            $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $id])->one();

            if ($produto_filial->filial_id == 43) {

                $valor_retorno = $valor_produto_filial->valor_compra * 0.97;
            } elseif (
                $produto_filial->produto_id == 381855
                || $produto_filial->produto_id == 381856
                || $produto_filial->produto_id == 381857
                || $produto_filial->produto_id == 381858
                || $produto_filial->produto_id == 381859
                || $produto_filial->produto_id == 381860
                || $produto_filial->produto_id == 381861
                || $produto_filial->produto_id == 381862
                || $produto_filial->produto_id == 381863
                || $produto_filial->produto_id == 381864
                || $produto_filial->produto_id == 381865
                || $produto_filial->produto_id == 381866
                || $produto_filial->produto_id == 381867
                || $produto_filial->produto_id == 381868
                || $produto_filial->produto_id == 381869
                || $produto_filial->produto_id == 381870
                || $produto_filial->produto_id == 381871
                || $produto_filial->produto_id == 381872
                || $produto_filial->produto_id == 381873
                || $produto_filial->produto_id == 381874
                || $produto_filial->produto_id == 381876
                || $produto_filial->produto_id == 381877
                || $produto_filial->produto_id == 381878
                || $produto_filial->produto_id == 381879
                || $produto_filial->produto_id == 381880
                || $produto_filial->produto_id == 381881
                || $produto_filial->produto_id == 381882
                || $produto_filial->produto_id == 381883
                || $produto_filial->produto_id == 381884
                || $produto_filial->produto_id == 381885
                || $produto_filial->produto_id == 381886
                || $produto_filial->produto_id == 381887
                || $produto_filial->produto_id == 381888
                || $produto_filial->produto_id == 381889
                || $produto_filial->produto_id == 381890
                || $produto_filial->produto_id == 381891
                || $produto_filial->produto_id == 381892
                || $produto_filial->produto_id == 381893
                || $produto_filial->produto_id == 381894
                || $produto_filial->produto_id == 381895
                || $produto_filial->produto_id == 381896
                || $produto_filial->produto_id == 381897
                || $produto_filial->produto_id == 381898
                || $produto_filial->produto_id == 381899
                || $produto_filial->produto_id == 381937
                || $produto_filial->produto_id == 381942
                || $produto_filial->produto_id == 381943
                || $produto_filial->produto_id == 382035
                || $produto_filial->produto_id == 382036
                || $produto_filial->produto_id == 382037
                || $produto_filial->produto_id == 382038
                || $produto_filial->produto_id == 382039
                || $produto_filial->produto_id == 382040
                || $produto_filial->produto_id == 382041
                || $produto_filial->produto_id == 382042
                || $produto_filial->produto_id == 382043
                || $produto_filial->produto_id == 382044
                || $produto_filial->produto_id == 382045
                || $produto_filial->produto_id == 382046
                || $produto_filial->produto_id == 382047
                || $produto_filial->produto_id == 382048
                || $produto_filial->produto_id == 382049
                || $produto_filial->produto_id == 382050
                || $produto_filial->produto_id == 382051
                || $produto_filial->produto_id == 382052
                || $produto_filial->produto_id == 382053
                || $produto_filial->produto_id == 382054
                || $produto_filial->produto_id == 382055
                || $produto_filial->produto_id == 382056
                || $produto_filial->produto_id == 382057
            ) {

                $valor_retorno = $valor_produto_filial->valor_compra * 0.80;
            } else {
                $valor_retorno = $valor_produto_filial->valor_compra;
            }
        }

        $valor_retorno = number_format($valor_retorno, 2, '.', '');


        $retorno = '{"valor_compra":' . $valor_retorno . '}';

        return $retorno;
    }



    /*public function actionGetValorCompra($id)
    {
        $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id',$id])->orderBy(['dt_inicio' => SORT_DESC])->one();
        
        $valor_retorno = 0;
        if($valor_produto_filial){
            $valor_retorno = $valor_produto_filial->valor_compra;
        }
        $retorno = '{"valor_compra":'.$valor_retorno.'}';
        return $retorno;
    }*/

    public function actionGetEmailFilial($id)
    {
        $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $id])->one();
        if ($produto_filial) {
            $filial = Filial::find()->andWhere(['=', 'id', $produto_filial->filial_id])->one();
            if ($filial) {
                return '{"email":"' . $filial->email_pedido . '"}';
            }
        }

        return '{"email":""}';
    }
}
