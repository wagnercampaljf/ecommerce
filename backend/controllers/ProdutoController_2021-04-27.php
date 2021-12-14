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
use yii\data\ActiveDataProvider;
use backend\models\ConsultaExpedicaoBusca;
use Mpdf\Utils\Arrays;

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

    public function actionPesquisar($termo = '')
    {
        $query = null;

        if (!empty($termo)) {

            $query = (new Query())
                ->select([
                    'p.id',
                    'p.nome',
                    'p.codigo_global',
                    'p.codigo_fabricante',
                    'mp.nome as marca',
                    'coalesce((select quantidade from produto_filial where produto_id = p.id and filial_id = 38 limit 1), 0) as quantidade_vannucci',
                    'coalesce((select quantidade from produto_filial where produto_id = p.id and filial_id = 43 limit 1), 0) as quantidade_morelate',
                    'coalesce((select quantidade from produto_filial where produto_id = p.id and filial_id = 72 limit 1), 0) as quantidade_br',
                    'coalesce((select quantidade from produto_filial where produto_id = p.id and filial_id = 60 limit 1), 0) as quantidade_lng',
                    'coalesce((select quantidade from produto_filial where produto_id = p.id and filial_id = 94 limit 1), 0) as quantidade_mg',
                    'coalesce((select quantidade from produto_filial where produto_id = p.id and filial_id = 95 limit 1), 0) as quantidade_spfilial',
                    'coalesce((select quantidade from produto_filial where produto_id = p.id and filial_id = 96 limit 1), 0) as quantidade_sp'

                ])
                ->from('produto p')
                ->join('left join', 'marca_produto mp', 'mp.id = p.marca_produto_id')
                ->orWhere(['like', 'p.nome', strtoupper($termo)])
                ->orWhere(['like', 'cast(p.id as varchar)', str_replace("PA", "", $termo)])
                ->orWhere(['like', 'p.codigo_fabricante', $termo])
                ->orWhere(['like', 'p.codigo_global', $termo])
                ->groupBy(['p.id', 'mp.nome'])
                ->orderBy(["id" => SORT_ASC]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
        } else {

            $dataProvider = null;
        }

        return $this->render('pesquisar', ['dataProvider' => $dataProvider]);
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

    public function actionProdutoml($produto)
    {
        return $this->render('produtoml', ['produto' => $produto]);
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

        $mensagem = $model->atualizarMercadoLivre();

        return $this->render('update', [
            'model' => $model,
            'mensagem' => $mensagem,
            'link_mercado_livre' => "",
        ]);

        $produtos_filiais = ProdutoFilial::find()->andWhere(['=', 'produto_id', $id])
            //->andWhere(['<>','filial_id', 43])
            ->andWhere(['<>', 'filial_id', 98])
            ->all();

        $mensagem = '<div class="text-primary h4">Produto atualizado no Mercado Livre com sucesso!</div>';
        $permalink = "";
        $permalink_duplicada = "";
        $erro_retorno       = "";
        $mensagem_retorno   = "";
        $texto_link = "Link para o ML (Principal)";

        foreach ($produtos_filiais as $k => $produto_filial) {

            if ($produto_filial->meli_id == null or $produto_filial->meli_id == "") {
                echo "Produto fora do ML";
                continue;
            }

            $produto_filial_mercado_livre = $produto_filial->meli_id;
            $refresh_token__meli_filial = $produto_filial->filial->refresh_token_meli;

            /*if($produto_filial->produto_filial_origem_id != null){
                $texto_link = "Link para o ML (Secundaria)";
                $produto_filial = ProdutoFilial::findOne($produto_filial->produto_filial_origem_id);
            }*/

            $meli = new Meli(static::APP_ID, static::SECRET_KEY);
            $user = $meli->refreshAccessToken($refresh_token__meli_filial);
            $response = ArrayHelper::getValue($user, 'body');
            echo "<pre>";
            print_r($response);
            echo "</pre>";
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                echo "333";
                $meliAccessToken = $response->access_token;

                if (is_null($produto_filial->valorMaisRecente)) {
                    return $this->render('update', ['model' => $model, 'erro' => "Produto sem valor cadastrado",]);
                }

                //echo __DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php'; die;
                //$page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',['produto' => $produto_filial]);
                //$page = $this->render('produtoml',['produto' => $produto_filial]);
                $page = $produto_filial->produto->nome . "\n\nAPLICACAO:\n\n" . $produto_filial->produto->aplicacao . "\n\n" . $produto_filial->produto->aplicacao_complementar . "\n\nDICAS:\n\n* Lado Esquerdo    o do Motorista.\n* Lado Direito    o do Passageiro.";
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

                $title = Yii::t('app', '{nome} ({cod})', ['cod' => $produto_filial->produto->codigo_global, 'nome' => $produto_filial->produto->nome]);

                $condicao = "new";
                if ($produto_filial->produto->e_usado) {
                    $condicao = "used";
                }

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
                    ],
                    "condition" => $condicao,
                ];
                $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
                //echo "<pre>"; print_r($response); echo "<pre>"; die;
                if ($response['httpCode'] >= 300) {
                    $erro_retorno .= '<div class="text-danger h4">Titulo nao atualizado no Mercado Livre</div><br>';
                    //$mensagem .= '<div class="text-danger h4">Título não atualizado no Mercado Livre</div>';
                    //continue;
                } else {
                    $mensagem_retorno       .= '<div class="text-primary h4">Titulo atualizado no Mercado Livre</div><br>';
                    $permalink              = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML (Principal)</a></div>';
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
                if ($response['httpCode'] >= 300) {
                    $erro_retorno = '<div class="text-danger h4">Modo de envio nao atualizado no Mercado Livre</div><br>';
                    //$mensagem .= '<div class="text-danger h4">Modo de envio não atualizado no Mercado Livre</div>';
                    //continue;
                } else {
                    $mensagem_retorno       .= '<div class="text-primary h4">Modo de envio atualizado no Mercado Livre</div><br>';
                    $permalink              = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML (Principal)</a></div>';
                }

                //Update Descricaoo
                $body = ['plain_text' => $page];
                $response = $meli->put("items/{$produto_filial_mercado_livre}/description?access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $erro_retorno = '<div class="text-danger h4">Descricao nao atualizada no Mercado Livre</div><br>';
                    //$mensagem .= '<div class="text-danger h4">Imagens n  o atualizadas no Mercado Livre</div>';
                    //continue;
                } else {
                    $mensagem_retorno       .= '<div class="text-primary h4">Descricao atualizada no Mercado Livre</div><br>';
                    //$permalink              = '<div class="h4"><a class="text-success" href="'.ArrayHelper::getValue($response, 'body.permalink').'">Link para o ML (Principal)</a></div>';
                }

                //Update Imagem
                $body = [
                    "pictures" => $produto_filial->produto->getUrlImagesML(),
                ];
                $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $erro_retorno = '<div class="text-danger h4">Imagens n  o atualizadas no Mercado Livre</div><br>';
                    //$mensagem .= '<div class="text-danger h4">Imagens não atualizadas no Mercado Livre</div>';
                    //continue;
                } else {
                    $mensagem_retorno       .= '<div class="text-primary h4">Imagens atualizadas no Mercado Livre</div><br>';
                    $permalink              = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML (Principal)</a></div>';
                }

                //Update Inativo
                $e_ativo = 'paused';
                if ($produto_filial->produto->e_ativo) {
                    $e_ativo = 'active';
                }
                $body = ["status" => $e_ativo,];
                $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $erro_retorno = '<div class="text-danger h4">EAtivo nao alterado(' . ($produto_filial->produto->e_ativo ? 'Ativo' : 'Inativo') . ')</div><br>';
                } else {
                    $mensagem_retorno       .= '<div class="text-primary h4">EAtivo alterado(' . ($produto_filial->produto->e_ativo ? 'Ativo' : 'Inativo') . ')</div><br>';
                    $permalink              = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML (Principal)</a></div>';
                }
                $response = $meli->put("items/{$produto_filial->meli_id_sem_juros}?access_token=" . $meliAccessToken, $body, []);
                if ($response['httpCode'] >= 300) {
                    $erro_retorno = '<div class="text-danger h4">EAtivo nao alterado(' . ($produto_filial->produto->e_ativo ? 'Ativo' : 'Inativo') . '), Sem juros</div><br>';
                } else {
                    $mensagem_retorno       .= '<div class="text-primary h4">EAtivo alterado(' . ($produto_filial->produto->e_ativo ? 'Ativo' : 'Inativo') . '), Sem Juros</div><br>';
                    $permalink              = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML (Principal)</a></div>';
                }

                //Update Quantidade
                $body = [
                    //"price" => round($produto_filial->getValorMercadoLivre(), 2),
                    "available_quantity" => $produto_filial->quantidade,
                ];
                $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
                //echo "<pre>"; print_r($body); echo "</pre>";
                //echo "<pre>"; print_r($response); echo "</pre>";
                if ($response['httpCode'] >= 300) {
                    $erro_retorno = '<div class="text-danger h4">Quantidade nao atualizadas no Mercado Livre</div><br>';
                    //$mensagem .= '<div class="text-danger h4">Pre  o e quantidade n  o atualizados no Mercado Livre</div>';
                    //continue;
                } else {
                    //$permalink = $permalink . '<div class="h4"><a class="text-success" href="'.ArrayHelper::getValue($response, 'body.permalink').'">'.$texto_link.'</a></div>';
                    $mensagem_retorno       .= '<div class="text-primary h4">Quantidade atualizado no Mercado Livre</div><br>';
                    $permalink               = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML (Principal)</a></div>';
                }
                //echo "Preço";die;
                //Update Preco
                $body = [
                    "price" => round($produto_filial->getValorMercadoLivre(), 2),
                    //"available_quantity" => $produto_filial->quantidade,
                ];
                $response = $meli->put("items/{$produto_filial_mercado_livre}?access_token=" . $meliAccessToken, $body, []);
                //echo "<pre>"; print_r($body); echo "</pre>";
                //echo "<pre>"; print_r($response); echo "</pre>";
                if ($response['httpCode'] >= 300) {
                    $erro_retorno = '<div class="text-danger h4">Preco nao atualizadas no Mercado Livre</div><br>';
                    //$mensagem .= '<div class="text-danger h4">Preço e quantidade não atualizados no Mercado Livre</div>';
                    //continue;
                } else {
                    //$permalink = $permalink . '<div class="h4"><a class="text-success" href="'.ArrayHelper::getValue($response, 'body.permalink').'">'.$texto_link.'</a></div>';
                    $mensagem_retorno       .= '<div class="text-primary h4">Preco atualizado no Mercado Livre</div><br>';
                    $permalink               = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML (Principal)</a></div>';

                    $produtos_filiais_outros = ProdutoFilial::find()->andWhere(['=', 'produto_filial_origem_id', $produto_filial->id])->all();

                    foreach ($produtos_filiais_outros as $produto_filial_outro) {
                        $preco_outro = round(round($produto_filial->getValorMercadoLivre(), 2), 2);

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
                                    "condition" => $condicao,
                                ];
                                $response_outro = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, []);
                                //echo "<pre>"; print_r($response); echo "<pre>"; die;
                                if ($response_outro['httpCode'] >= 300) {
                                    //return $this->render('update', [ 'model' => $model, 'erro' => '<div class="text-text-warning h4">Conta principal do Mercado Livre altarada com sucesso, conta secund  ria n  o a$
                                    $erro_retorno = '<div class="text-text-warning h4">Conta secundaria nao alterada</div>';
                                } else {
                                    $mensagem_retorno       .= '<div class="text-primary h4">Conta secundaria alterada</div><br>';
                                    $permalink_duplicada = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response_outro, 'body.permalink') . '">Link para o ML (Secund  ria)</a></div>';
                                }

                                //Update Inativo
                                if ($produto_filial->produto->e_ativo) {
                                    $body = ["status" => 'active',];
                                    $response = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken, $body, []);
                                    //echo "<pre>"; print_r($response); echo "</pre>";
                                    if ($response['httpCode'] >= 300) {
                                        $erro_retorno = '<div class="text-danger h4">Produto nao ativado</div><br>';
                                    } else {
                                        $mensagem_retorno       .= '<div class="text-primary h4">Produto ativado no Mercado Livre</div><br>';
                                        $permalink              = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML (Principal)</a></div>';
                                    }
                                } else {
                                    $body = ["status" => 'paused',];
                                    $response = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken, $body, []);
                                    if ($response['httpCode'] >= 300) {
                                        $erro_retorno = '<div class="text-danger h4">Produto nao desativado</div><br>';
                                    } else {
                                        $mensagem_retorno       .= '<div class="text-primary h4">Produto desativado no Mercado Livre</div><br>';
                                        $permalink              = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response, 'body.permalink') . '">Link para o ML (Principal)</a></div>';
                                    }
                                }
                            } else {
                                $title = Yii::t('app', '{nome} ({cod})', ['cod' => $produto_filial->produto->codigo_global, 'nome' => $model->produto->nome]);
                                $condicao = ($produto_filial->produto->e_usado) ? "used" : "new";
                                $body = [
                                    "title" => utf8_encode((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                                    "category_id" => utf8_encode(ArrayHelper::getValue($response, 'body.category_id')),
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
                                    $mensagem_retorno       .= '<div class="text-primary h4">Conta secundaria, produto criado</div><br>';
                                    $permalink_duplicada = '<div class="h4"><a class="text-success" href="' . ArrayHelper::getValue($response_outro, 'body.permalink') . '">Link para o ML (Secund  ria)</a></div>';
                                } else {
                                    //return $this->render('update', ['model' => $model,'erro' => '<div class="text-text-warning h4">Conta principal do Mercado Livre altarada com sucesso, conta secund  ria n  o cri$
                                    $erro_retorno .= '<div class="text-text-warning h4">Conta secundaria, produto nao criado</div>';
                                }
                            }
                        }
                    }
                }

                //echo "<pre>"; print_r($erro_retorno); echo "</pre>";
                //echo "<pre>"; print_r($mensagem_retorno); echo "</pre>";
                //echo "<pre>"; print_r($permalink . "<br>" . $permalink_duplicada); echo "</pre>";
            }
        }

        return $this->render('update', [
            'model' => $model,
            'erro'                  => $erro_retorno,
            'mensagem'              => $mensagem_retorno,
            'link_mercado_livre'    => $permalink . "<br>" . $permalink_duplicada,
        ]);
    }

    public function actionMovimentacaoEstoque($termo = '')
    {
        $query = null;

        if (!empty($termo)) {

            $codigo_busca = str_replace("A", "", $termo);
            $codigo_busca = str_replace("a", "", $termo);
            $codigo_busca = str_replace("P", "", $termo);
            $codigo_busca = str_replace("p", "", $termo);

            $query = (new Query())
                ->select([
                    'p.id',
                    'p.nome',
                    'p.codigo_global',
                    'p.codigo_fabricante',
                    'mp.nome as marca'
                ])
                ->from('produto p')
                //->join('join', 'produto_filial pf', 'pf.produto_id = p.id')
                //->join('join', 'valor_produto_filial vpf', 'vpf.produto_filial_id = pf.id')
                ->join('left join', 'marca_produto mp', 'mp.id = p.marca_produto_id')
                ->orWhere(['like', 'cast(p.id as varchar)', str_replace("PA", "", $termo)])
                ->orWhere(['like', 'p.codigo_fabricante', $termo])
                ->orWhere(['like', 'p.codigo_global', $termo])
                ->groupBy(['p.id', 'mp.nome'])
                ->orderBy(["id" => SORT_ASC]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
        } else {

            $dataProvider = null;
        }

        return $this->render('movimentacao-estoque', ['dataProvider' => $dataProvider]);
    }
}
