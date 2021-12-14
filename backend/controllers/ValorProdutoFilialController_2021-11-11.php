<?php
//222
namespace backend\controllers;

use backend\functions\FunctionsML;
use Yii;
use common\models\ValorProdutoFilial;
use common\models\ValorProdutoFilialSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\ProdutoFilial;
use common\models\Produto;
use common\models\Filial;
use common\models\MarcaProduto;
use yii\web\Response;
use Livepixel\MercadoLivre\Meli;
use yii\helpers\ArrayHelper;


/**
 * ValorProdutoFilialController implements the CRUD actions for ValorProdutoFilial model.
 */
class ValorProdutoFilialController extends Controller
{
    const APP_ID = '3029992417140266';
    const SECRET_KEY = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';

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

	echo "<pre>"; print_r(Yii::$app->request->post()); echo "</pre>"; //die;

	$e_valor_bloqueado = ArrayHelper::getValue(Yii::$app->request->post(), 'ValorProdutoFilial.e_valor_bloqueado');

        $model = new ValorProdutoFilial();
        $result = '';

        if ($model->load(Yii::$app->request->post())) {
            $produto_id = ProdutoFilial::findOne($model->produto_filial_id)->produto_id;
            $produto = Produto::findOne($produto_id);

            if (!$e_valor_bloqueado) {
                if ($model->save()) {

                    self::AtualizarValorProdutoFilial($model);

                    $produto_filial = ProdutoFilial::find()->andWhere(["=", "id", $model->produto_filial_id])->one();

                    if ($model->e_valor_bloqueado == 1 || $model->e_valor_bloqueado == true) {

                        $produto->e_valor_bloqueado = true;
                        $produto->save();

                        $produto_filials = ProdutoFilial::find()->where("produto_id = $produto_filial->produto_id and id <> $produto_filial->id")->orderBy("produto_filial_origem_id DESC")->all();

                        foreach ($produto_filials as $produto) {
                            if ($produto->id !== $model->produto_filial_id) {
                                $modelValorProdutoFilial = new ValorProdutoFilial();
                                $modelValorProdutoFilial->valor = $model->valor;
                                $modelValorProdutoFilial->dt_inicio = date("Y-m-d H:i:s");
                                $modelValorProdutoFilial->produto_filial_id = $produto->id;
                                $modelValorProdutoFilial->promocao = false;
                                $modelValorProdutoFilial->valor_compra = $model->valor_compra;
                                $modelValorProdutoFilial->save();
                            }
                        }
                    }

                    if ($produto_filial->filial->refresh_token_meli <> null && $produto_filial->filial->refresh_token_meli <> '') {
                        if ($produto_filial->meli_id <> null && $produto_filial->meli_id <> '') {
                            $result = FunctionsML::Permalink($produto_filial);
                        }
                    }

                    return $this->render('view', [
                        'model' => $model,
                        'mercadoLivre' => $result,
                    ]);
                }
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'mensagem' => 'Produto contem valor bloqueado'
                ]);
            }
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
        if (($model = ValorProdutoFilial::find() //findOne($id)
            //->select(['valor_produto_filial.id','produto_filial.filial_id as filial_id'])
            ->joinWith(['produtoFilial', 'produtoFilial.produto', 'produtoFilial.filial'])
            ->andWhere(['=', 'valor_produto_filial.id', $id])
            ->one()) !== null) {
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

    public function actionGetProdutoFilialUnitario($q, $id = null)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }
        if ($id > 0) {
            return ['results' => ['id' => $id, 'text' => ProdutoFilial::findOne($id)->filial->nome . " - " . ProdutoFilial::findOne($id)->produto->nome . "(" . ProdutoFilial::findOne($id)->produto->codigo_global . ")" . "(" . ProdutoFilial::findOne($id)->produto->codigo_fabricante . ")"]];
        }

        $busca_pa = str_replace("P", "", str_replace("A", "", str_replace("p", "", str_replace("a", "", $q))));

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = ProdutoFilial::find()
                ->select([
                    'produto_filial.id',
                    "coalesce(produto.nome, '') || ' - ' || coalesce(produto.codigo_global, '') || '(' || coalesce(filial.nome, '') || ')' || '(Est: ' || coalesce(produto_filial.quantidade::varchar , '') || ')' || '(' || coalesce(produto.codigo_fabricante, '') || ')' || '(' || coalesce((select valor_compra from valor_produto_filial where valor_produto_filial.produto_filial_id = produto_filial.id order by dt_inicio desc limit 1)::varchar, '') || ')' as text",
                    "coalesce(produto.nome, '')  as nome",
                    "coalesce(produto.codigo_global, '')  as codigo_global",
                    "coalesce(filial.nome, '') as filial",
                    "'Est: ' || coalesce(produto_filial.quantidade::varchar , '')  as quantidade",
                    "coalesce(produto.codigo_fabricante, '') as codigo_fabricante",
                    //"coalesce((select valor_compra from valor_produto_filial where valor_produto_filial.produto_filial_id = produto_filial.id order by dt_inicio desc limit 1)::varchar, '') as valor",
                    "round(coalesce((select valor_compra from valor_produto_filial where valor_produto_filial.produto_filial_id = produto_filial.id order by dt_inicio desc limit 1), 0)+(coalesce((select valor_compra from valor_produto_filial where valor_produto_filial.produto_filial_id = produto_filial.id order by dt_inicio desc limit 1),0)*(coalesce(produto.ipi, 0)/100)),2) as valor",
                    "coalesce((select valor from valor_produto_filial where valor_produto_filial.produto_filial_id = produto_filial.id order by dt_inicio desc limit 1)::varchar, '') as compra",
                ])
                ->joinWith(['produto', 'filial'])
                ->where("codigo_global not like 'CX.%' 
                            and filial.id <> 98
                            and (produto.codigo_global like '%$q%' or cast(produto_filial.produto_id as varchar) = '" . $busca_pa . "')")
                //or lower(lower(produto.id::VARCHAR)) like '%" . strtolower(preg_replace('/[^0-9]/', '', $q)) . "%')")
                ->limit(10)
                ->distinct()
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
            return ['results' => ['id' => $id, 'text' => ProdutoFilial::findOne($id)->filial->nome . " - " . ProdutoFilial::findOne($id)->produto->nome . "(" . ProdutoFilial::findOne($id)->produto->codigo_global . ")" . "(" . ProdutoFilial::findOne($id)->produto->codigo_fabricante . ")"]];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = ProdutoFilial::find()
                //Alterado dia 26/03/2021
                ->select([
                    'produto_filial.id',
                    "coalesce(produto.nome, '') || ' - ' || coalesce(produto.codigo_global, '') || '(' || coalesce(filial.nome, '') || ')' || '(Est: ' || coalesce(produto_filial.quantidade::varchar , '') || ')' || '(' || coalesce(produto.codigo_fabricante, '') || ')' || '(' || coalesce((select valor_compra from valor_produto_filial where valor_produto_filial.produto_filial_id = produto_filial.id order by dt_inicio desc limit 1)::varchar, '') || ')' as text",
                    "coalesce(produto.nome, '')  as nome",
                    "coalesce(produto.codigo_global, '')  as codigo_global",
                    "coalesce(filial.nome, '') as filial",
                    "'Est: ' || coalesce(produto_filial.quantidade::varchar , '')  as quantidade",
                    "coalesce(produto.codigo_fabricante, '') as codigo_fabricante",
                    //"coalesce((select valor_compra from valor_produto_filial where valor_produto_filial.produto_filial_id = produto_filial.id order by dt_inicio desc limit 1)::varchar, '') as valor",
                    "round(coalesce((select valor_compra from valor_produto_filial where valor_produto_filial.produto_filial_id = produto_filial.id order by dt_inicio desc limit 1), 0)+(coalesce((select valor_compra from valor_produto_filial where valor_produto_filial.produto_filial_id = produto_filial.id order by dt_inicio desc limit 1),0)*(coalesce(produto.ipi, 0)/100)),2) as valor",
                    "coalesce((select valor from valor_produto_filial where valor_produto_filial.produto_filial_id = produto_filial.id order by dt_inicio desc limit 1)::varchar, '') as compra",
                ])
                ->joinWith(['produto', 'filial'])
                ->where([
                    'like',
                    'lower(produto.nome)',
                    strtolower($q)
                ])
                ->orWhere([
                    'lower(produto_filial.id::VARCHAR)' =>  strtolower($q)
                ])
                ->orWhere([
                    'lower(produto.id::VARCHAR)' =>  strtolower(preg_replace('/[^0-9]/', '', $q))
                ])
                ->orWhere(['like', 'produto.codigo_global', $q])
                ->orWhere(['like', 'produto.codigo_fabricante', $q])
                ->andWhere(['<>', 'filial_id', 98])
		->andWhere(['<>', 'filial_id', 100])
                ->limit(10)
                ->distinct()
                ->createCommand()
                ->queryAll();
            $out['results'] = array_values($results);
        }

        return $out;
    }

    public function actionAtualizarml($id)
    {

        $model = $this->findModel($id);

        $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $model->produto_filial_id])->one();

        $mensagem = $produto_filial->produto->atualizarMercadoLivre();

        return $this->render('view', [
            'model' => $model,
            'mensagem' => $mensagem,
            'link_mercado_livre' => "",
        ]);


        //echo "<pre>"; print_r($model); echo "<pre>"; die;

        $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $model->produto_filial_id])->one();

        $produto_filial_mercado_livre = $produto_filial->meli_id;
        $refresh_token__meli_filial = $produto_filial->filial->refresh_token_meli;

        if ($produto_filial->produto_filial_origem_id != null) {
            $produto_filial = ProdutoFilial::findOne($produto_filial->produto_filial_origem_id);
        }

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken($refresh_token__meli_filial);
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {

            $meliAccessToken = $response->access_token;

            if (is_null($produto_filial->valorMaisRecente)) {
                Yii::error("Produto Filial: {$produto_filial->produto->nome}({$produto_filial->id}), não possui valor", 'error_yii');

                return $this->render('view', [
                    'model' => $model,
                    'erro' => "Produto sem valor cadastrado",
                ]);
            }

            //$page = $this->render(__DIR__ . 'lojista/views/mercado-livre/produto.php', ['produto' => $model]);
            $title = Yii::t('app', '{nome} ({cod})', ['cod' => $produto_filial->produto->codigo_global, 'nome' => $produto_filial->produto->nome]);

            //Update Item
            $body = [
                "title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                'attributes' => [
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
                        "id" => "BRAND",
                        "name" => "Marca",
                        "value_id" => null,
                        "value_name" => $produto_filial->produto->fabricante->nome,
                        "value_struct" => null,
                        "attribute_group_id" => "OTHERS",
                        "attribute_group_name" => "Outros"
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
                return $this->render('view', [
                    'model' => $model,
                    'erro' => '<div class="text-danger h4">Título não atualizado no Mercado Livre</div>',
                ]);
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
                    "mode" => $modo,
                    "local_pick_up" => true,
                    "free_shipping" => false,
                    "free_methods" => [],
                ],
            ];
            $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
            $permalink = "";
            if ($response['httpCode'] >= 300) {
                return $this->render('view', [
                    'model' => $model,
                    'erro' => '<div class="text-danger h4">Modo de envio não atualizado no Mercado Livre</div>',
                ]);
            }

            //Update Descrição
            //$body = ['text' => $page];
            //$response = $meli->put("items/{$model->meli_id}/description?access_token=" . $meliAccessToken, $body, []);

            $body = [
                "pictures" => $produto_filial->produto->getUrlImagesML(),
            ];
            $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
            //print_r($response);
            if ($response['httpCode'] >= 300) {
                return $this->render('view', [
                    'model' => $model,
                    'erro' => '<div class="text-danger h4">Imagens não atualizadas no Mercado Livre</div>',
                ]);
            }

            //Update Item
            $body = [
                "price" => round($produto_filial->getValorMercadoLivre(), 2),
                "available_quantity" => $produto_filial->quantidade,

            ];
            $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
            if ($response['httpCode'] >= 300) {
                return $this->render('view', [
                    'model' => $model,
                    'erro' => '<div class="text-danger h4">Preço e quantidade não atualizados no Mercado Livre</div>',
                ]);
            } else {

                $permalink = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML (Principal)</a></div>';

                $produtos_filiais_outros = ProdutoFilial::find()->andWhere(['=', 'produto_filial_origem_id', $produto_filial->id])->all();

                foreach ($produtos_filiais_outros as $produto_filial_outro) {
                    $preco_outro = round($produto_filial->getValorMercadoLivre(), 2);

                    $user_outro     = $meli->refreshAccessToken($produto_filial_outro->filial->refresh_token_meli);
                    $response_outro = ArrayHelper::getValue($user_outro, 'body');

                    if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 400) {

                        $meliAccessToken_outro = $response_outro->access_token;
                        if ($produto_filial_outro->meli_id != null) {
                            $body = [
                                "available_quantity" => $produto_filial->quantidade,
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
                                return $this->render('update', [
                                    'model' => $model,
                                    'erro' => '<div class="text-text-warning h4">Conta principal do Mercado Livre altarada com sucesso, conta secundária não alterada</div>',
                                ]);
                            } else {
                                $permalink = $permalink . '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response_outro, 'body.permalink') . '">Link para o ML (Secundária)</a></div>';
                            }
                        } else {
                            $title = Yii::t('app', '{nome} ({cod})', [
                                'cod' => $produto_filial->produto->codigo_global,
                                'nome' => $produto_filial->produto->nome
                            ]);
                            $condicao = ($produto_filial->produto->e_usado) ? "used" : "new";

                            $titulo_novo = mb_convert_encoding($title, 'UTF-8', 'UTF-8');

                            $categoria_meli_id              = "";
                            $nome = str_replace(" ", "%20", $title);

                            $nome_array         = explode(" ", $titulo_novo);
                            $nome               = $nome_array[0] . "%20" . ((array_key_exists(1, $nome_array)) ? $nome_array[1] : "");

                            $response_categoria_recomendada = $meli->get("sites/MLB/domain_discovery/search?q=" . $nome);

                            if ($response_categoria_recomendada['httpCode'] >= 300) {
                                echo " - ERRO Categoria Recomendada";
                                //$categoria_meli_id = $model->subcategoria->meli_id;
                                $categoria_meli_id = utf8_encode("MLB432476");
                            } else {

                                $response_categoria_dimensoes = $meli->get("categories/" . $categoria_meli_id . "/shipping");

                                if ($response_categoria_dimensoes['httpCode'] >= 300) {
                                } else {

                                    $response_categoria_frete = $meli->get("/users/435343067/shipping_options/free?dimensions=" . ArrayHelper::getValue($response_categoria_dimensoes, 'body.height') . "x" . ArrayHelper::getValue($response_categoria_dimensoes, 'body.width') . "x" . ArrayHelper::getValue($response_categoria_dimensoes, 'body.length') . "," . ArrayHelper::getValue($response_categoria_dimensoes, 'body.weight'));
                                    if ($response_categoria_frete['httpCode'] >= 300) {
                                    } else {
                                    }
                                }

                                $categoria_meli_id      = ArrayHelper::getValue($response_categoria_recomendada, 'body.0.category_id');
                                echo " - OK Categoria Recomendada " . $categoria_meli_id;
                            }

                            $body = [
                                "title" => utf8_encode((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                                "category_id" => utf8_encode($categoria_meli_id),
                                "listing_type_id" => "bronze",
                                "currency_id" => "BRL",
                                "price" => $preco_outro,
                                "available_quantity" => utf8_encode($produto_filial->quantidade),
                                "condition" => $condicao,
                                "pictures" => $produto_filial->produto->getUrlImagesML(),
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
                                $permalink = $permalink . '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response_outro, 'body.permalink') . '">Link para o ML (Secundária)</a></div>';
                            } else {
                                return $this->render('view', [
                                    'model' => $model,
                                    'erro' => '<div class="text-text-warning h4">Conta principal do Mercado Livre altarada com sucesso, conta secundária não criado</div>',
                                ]);
                            }
                        }
                    }
                }
            }


            return $this->render('view', [
                'model' => $model,
                'mensagem' => '<div class="text-primary h4">Produto atualizado no Mercado Livre com sucesso!</div>',
                'link_mercado_livre' => $permalink,
            ]);
        }
    }

    public function actionCriarml($id)
    {

        $model = $this->findModel($id);

        //echo "<pre>"; print_r($model); echo "<pre>"; die;

        $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $model->produto_filial_id])->one();
        //echo "<pre>"; print_r($produto_filial); echo "<pre>"; die;

        if ($produto_filial->meli_id <> null and $produto_filial->meli_id <> "") {
            return $this->render('view', [
                'model' => $model,
                'erro' => '<div class="text-warning h4">Produto já criado no Mercado Livre</div>',
            ]);
        }

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        //echo 2222;
        $user = $meli->refreshAccessToken($produto_filial->filial->refresh_token_meli);
        //echo "<pre>"; var_dump($produto_filial); echo "</pre>"; die;
        $response = ArrayHelper::getValue($user, 'body');
        //echo "<pre>"; var_dump($response); echo "</pre>"; die;
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;

            $subcategoriaMeli = $produto_filial->produto->subcategoria->meli_id;
            if (!isset($subcategoriaMeli)) {
                return $this->render('view', [
                    'model' => $model,
                    'erro' => "Produto sem subcategoria",
                ]);
            }
            //echo 111; die;
            //$page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',['produto' => $produtoFilial]);

            $title = Yii::t('app', '{nome})', ['nome' => $produto_filial->produto->nome]);

            $titulo_novo = mb_convert_encoding($title, 'UTF-8', 'UTF-8');

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
                default:
                    $modo = "me2";
                    break;
            }
            $condicao = ($produto_filial->produto->e_usado) ? "used" : "new";

            $page = $produto_filial->produto->nome . "\n\nAPLICAÇÃO:\n\n" . $produto_filial->produto->aplicacao . $produto_filial->produto->aplicacao_complementar . "\n\nDICAS: \n\nLado Esquerdo é o do Motorista.\n\n* Lado Direito é o do Passageiro.";
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

            $page = substr($page, 0, 5000);

            $marca = "OPT";
            $marca_produto = MarcaProduto::find()->andWhere(['=', 'id', $produto_filial->produto->marca_produto_id])->one();
            if ($marca_produto) {
                $marca = $marca_produto->nome;
            }

            $modelo = "Bebedouro de galao";


            $categoria_meli_id              = "";
            $nome = str_replace(" ", "%20", $title);

            $nome_array         = explode(" ", $titulo_novo);
            $nome               = $nome_array[0] . "%20" . ((array_key_exists(1, $nome_array)) ? $nome_array[1] : "") . "%20" . ((array_key_exists(2, $nome_array)) ? $nome_array[2] : "");

            $response_categoria_recomendada = $meli->get("sites/MLB/domain_discovery/search?q=" . $nome);

            if ($response_categoria_recomendada['httpCode'] >= 300) {
                echo " - ERRO Categoria Recomendada";
                //$categoria_meli_id = $model->subcategoria->meli_id;
                $categoria_meli_id = utf8_encode("MLB432476");
            } else {

                $response_categoria_dimensoes = $meli->get("categories/" . $categoria_meli_id . "/shipping");

                if ($response_categoria_dimensoes['httpCode'] >= 300) {
                } else {

                    $response_categoria_frete = $meli->get("/users/435343067/shipping_options/free?dimensions=" . ArrayHelper::getValue($response_categoria_dimensoes, 'body.height') . "x" . ArrayHelper::getValue($response_categoria_dimensoes, 'body.width') . "x" . ArrayHelper::getValue($response_categoria_dimensoes, 'body.length') . "," . ArrayHelper::getValue($response_categoria_dimensoes, 'body.weight'));
                    if ($response_categoria_frete['httpCode'] >= 300) {
                    } else {
                    }
                }

                $categoria_meli_id      = ArrayHelper::getValue($response_categoria_recomendada, 'body.0.category_id');
                echo " - OK Categoria Recomendada " . $categoria_meli_id;
            }


            $body = [
                //"title" => utf8_encode((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                "title" => mb_substr($titulo_novo, 0, 60),
                "category_id" => "MLB2219",//utf8_encode($categoria_meli_id),
                //"official_store_id" => 3627,
                "listing_type_id" => "bronze",
                "currency_id" => "BRL",
                "price" => utf8_encode(round($produto_filial->getValorMercadoLivre(), 2)),
                "available_quantity" => utf8_encode($produto_filial->quantidade),
                "seller_custom_field" => utf8_encode($produto_filial->id),
                "condition" => $condicao,
                //"description" => utf8_encode($page),
                //"description" => ["plain_text" => utf8_encode($page)],
                "description" => ["plain_text" => $page],
                "pictures" => $produto_filial->produto->getUrlImagesML(),
                "shipping" => [
                    "mode" => $modo,
                    "local_pick_up" => true,
                    "free_shipping" => false,
                    "free_methods" => [],
                ],
                //"warranty" => "6 meses",
                "sale_terms" => [
                    [
                        "id" => "WARRANTY_TYPE",
                        "value_id" => "2230280"
                    ],
                    [
                        "id" => "WARRANTY_TIME",
                        "value_name" => "3 meses"
                    ]
                ],
                'attributes' => [
                    [
                        'id'                    => 'PART_NUMBER',
                        'name'                  => 'Número de peça',
                        'value_id'              => null,
                        'value_name'            => $produto_filial->produto->codigo_global,
                        'value_struct'          => null,
                        'values'                => [[
                            'id'    => null,
                            'name'  => $produto_filial->produto->codigo_global,
                            'struct' => null,
                        ]],
                        'attribute_group_id'    => "OTHERS",
                        'attribute_group_name'  => "Outros"
                    ],
                    [
                        'id'                    => 'BRAND',
                        'name'                  => 'Marca',
                        'value_id'              => null,
                        'value_name'            => $marca,
                        'value_struct'          => null,
                        'attribute_group_id'    => "OTHERS",
                        'attribute_group_name'  => "Outros"
                    ],
                    /*[
                                'id'                    => 'MODEL',
                                'name'                  => 'Modelo',
                                'value_id'              => null,
                                'value_name'            => $modelo,
                                'value_struct'          => null,
                                'attribute_group_id'    => "OTHERS",
                                'attribute_group_name'  => "Outros"
                                ],*/
                ]
            ];

            //echo "<pre>"; print_r($body); echo "</pre>"; 

            if ($produto_filial->produto->subcategoria->meli_id == "MLB73052") {
                $body['attributes'][] = [
                    'id'                    => 'MODEL',
                    'name'                  => 'Model',
                    'value_id'              => null,
                    'value_name'            => "Bebedouro",
                    'value_struct'          => null,
                    'attribute_group_id'    => "OTHERS",
                    'attribute_group_name'  => "Outros"
                ];
            }

            //echo "<pre>"; print_r($body); echo "</pre>";

            //die;

            //echo "<pre>"; print_r($body); echo "</pre>"; die;
            $body_principal            = $body;
            if ($produto_filial->filial_id != 98 && $produto_filial->filial_id != 93 && $produto_filial->filial_id != 94 && $produto_filial->filial_id != 99 && $produto_filial->filial_id != 100) {
                $body_principal["official_store_id"] = 3627;
            }

            $response = $meli->post("items?access_token=" . $meliAccessToken, $body_principal);
            // echo "<pre>";      print_r($response['body']);           echo "</pre>"; die;
            if ($response['httpCode'] >= 300) {
                return $this->render('view', [
                    'model' => $model,
                    'erro' => '<div class="text-danger h4">' . 'Não criado no Mercado Livre: ' . $response['body']->message . '</div>',
                ]);
            } else {
                $produto_filial->meli_id = $response['body']->id;
                if ($produto_filial->save()) {

                    $produto_filial->produto->atualizarMLDescricao();

                    $mensagem_conta_duplicada   = "";
                    $link_conta_duplicada       = "";
                    $produtos_filiais_conta_duplicada = ProdutoFilial::find()->andWhere(['=', 'produto_filial_origem_id', $produto_filial->id])->all();

                    foreach ($produtos_filiais_conta_duplicada as $i => $produto_filial_conta_duplicada) {
                        $user_outro     = $meli->refreshAccessToken($produto_filial_conta_duplicada->filial->refresh_token_meli);
                        $response_outro = ArrayHelper::getValue($user_outro, 'body');

                        if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 400) {
                            $meliAccessToken_outro = $response_outro->access_token;

                            $response = $meli->post("items?access_token=" . $meliAccessToken_outro, $body);
                            //echo "<pre>"; print_r($response); echo "</pre>";
                            if ($response['httpCode'] >= 300) {
                                $mensagem_conta_duplicada = '<div class="text-danger h4">Produto não criado no ML</div>';
                            } else {
                                $produto_filial_conta_duplicada->meli_id = $response['body']->id;
                                if ($produto_filial_conta_duplicada->save()) {
                                    $produto_filial_conta_duplicada->produto->atualizarMLDescricao();
                                    $mensagem_conta_duplicada   = '<div class="text-primary h4">Produto criado no Mercado Livre (CONTA DUPLICADA) com sucesso!</div>';
                                    $link_conta_duplicada       = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML (CONTA DUPLICADA)</a></div>';
                                } else {
                                    $mensagem_conta_duplicada   = '<div class="text-danger h4">meli_id não salvo no estoque (CONTA DUPLICADA)</div>';
                                    $link_conta_duplicada       = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML (CONTA DUPLICADA)</a></div>';
                                }
                            }
                        }
                    }

                    return $this->render('view', [
                        'model' => $model,
                        'mensagem' => '<div class="text-primary h4">Produto criado no Mercado Livre com sucesso!</div>' . $mensagem_conta_duplicada,
                        'link_mercado_livre' => '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML</a></div>' . $link_conta_duplicada,
                    ]);
                } else {
                    return $this->render('view', [
                        'model' => $model,
                        'erro' => '<div class="text-danger h4">meli_id não salvo no estoque</div>',
                    ]);
                }
            }
        }
    }

    public static function AtualizarValorProdutoFilial($model)
    {

        $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $model->produto_filial_id])->one();

        if ($produto_filial->save()) {

            $filiais_mesmo_valor = array();
            switch ($produto_filial->filial_id) {
                case 93:
                    $filiais_mesmo_valor = [94, 95, 96, 8];
                    break;
                case 8:
                    $filiais_mesmo_valor = [93, 94, 95, 96];
                    break;
                case 94:
                    $filiais_mesmo_valor = [93, 8, 95, 96];
                    break;
                case 95:
                    $filiais_mesmo_valor = [93, 8, 94, 96];
                    break;
                case 96:
                    $filiais_mesmo_valor = [8, 93, 94, 95, 87, 88, 89, 90];
                    break;
                case 87:
                    $filiais_mesmo_valor = [96];
                    break;
                case 88:
                    $filiais_mesmo_valor = [96];
                    break;
                case 89:
                    $filiais_mesmo_valor = [96];
                    break;
                case 90:
                    $filiais_mesmo_valor = [96];
                    break;
            }

            foreach ($filiais_mesmo_valor as $k => $filial_mesmo_valor_id) {
                $produto_filial_mesmo_valor = ProdutoFilial::find()->andWhere(["=", "produto_id", $produto_filial->produto_id])
                    ->andWhere(["=", "filial_id", $filial_mesmo_valor_id])
                    ->one();

                if ($produto_filial_mesmo_valor) {
                    $valor_produto_filial_mesmo_valor                       = new ValorProdutoFilial();
                    $valor_produto_filial_mesmo_valor->produto_filial_id    = $produto_filial_mesmo_valor->id;
                    $valor_produto_filial_mesmo_valor->valor                = $model->valor;
                    $valor_produto_filial_mesmo_valor->valor_cnpj           = $model->valor_cnpj;
                    $valor_produto_filial_mesmo_valor->dt_inicio            = date("Y-m-d H:i:s");
                    $valor_produto_filial_mesmo_valor->promocao             = $model->promocao;
                    $valor_produto_filial_mesmo_valor->valor_compra         = $model->valor_compra;
                    $valor_produto_filial_mesmo_valor->save();
                }
            }

            $produto = Produto::find()->andWhere(['=', 'id', $produto_filial->produto_id])->one();
            $produto->e_valor_bloqueado = $model->e_valor_bloqueado;

            if ($model->dias_expedicao == "" || $model->dias_expedicao == null) {
                $produto->dias_expedicao = 0;
            } else {
                $produto->dias_expedicao = $model->dias_expedicao;
            }

            $produto->save();
        }
    }
}
