<?php
//1111
namespace backend\controllers;

use backend\functions\FunctionsOmie;
use backend\models\Administrador;
use backend\models\ContaCorrente;
use common\mail\AsyncMailer;
use common\models\PedidoMercadoLivrePagamento;
use common\models\PedidoMercadoLivreProdutoProdutoFilial;
use common\models\PedidoProdutoFilial;
use common\models\PedidoSearch;
use common\models\PedidoSkyhubSearch;
use common\models\PedidoStatusAberto;
use console\models\SkyhubClient;
use frontend\controllers\MailerController;
use yii\filters\AccessControl;
use common\models\Usuario;
use Yii;
use yii\base\ErrorException;
use yii\helpers\Json;
use common\models\Pedido;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\PedidoMercadoLivreSearch;
use backend\models\PedidoProdutoFilialCotacao;
use backend\models\PedidoProdutoFilialCotacaoSearch;
use common\models\PedidoMercadoLivre;
use common\models\PedidoMercadoLivreProduto;
use console\controllers\actions\omie\Omie;
use common\models\Filial;
use Livepixel\MercadoLivre\Meli;
use common\models\PedidoMercadoLivreProdutoProdutoFilialSearch;
use common\models\ProdutoFilial;
use common\models\Comprador;
use common\models\Empresa;
use common\models\EnderecoEmpresa;
use common\models\PedidoProdutoFilialSearch;
use yii\db\Query;
use common\models\StatusPedido;
use common\models\Transportadora;
use yii\web\Response;
use common\models\ValorProdutoFilial;
use common\models\Produto;
use Exception;
use backend\models\NotaFiscalPedidoProduto;
use common\models\MarcaProduto;

/**
 * PedidosController implements the CRUD actions for Pedido model.
 */
class PedidosController extends Controller
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
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Pedido models.
     * @return mixed
     */
    public function actionIndex($filtro = null, $filtro_status = null)
    {
        $searchModel = new PedidoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider'              => $dataProvider,
            'filterModel'               => $searchModel,
            'filtro'                    => $filtro,
            'filtro_status'             => $filtro_status
        ]);
    }

    public function actionPedidoInterno($filtro = null, $filtro_status = null)
    {
        $searchModel = new PedidoSearch();
        $dataProvider = $searchModel->searchInterno(Yii::$app->request->get());

        return $this->render('pedidos-internos/index', [
            'dataProvider'              => $dataProvider,
            'filterModel'               => $searchModel,
            'filtro'                    => $filtro,
            'filtro_status'             => $filtro_status
        ]);
    }

    public function actionMudarStatus($id, $status)
    {
        //$user = Usuario::findOne(\Yii::$app->user->id);
        //$pedido = Pedido::find()->where(['id' => $id])->andWhere(['filial_id' => $user->filial_id])->one();

        $pedido = Pedido::find()->where(['id' => $id])->one();
        if (is_null($pedido)) {
            throw new \yii\web\ForbiddenHttpException('Sem permissão');
        }

        $pedido->mudarStatus($status);

        return $this->redirect(Url::to(['/pedidos/view', 'id' => $pedido->id]));
    }

    public function actionUpdateCotacao($id)
    {
        $model = PedidoProdutoFilialCotacao::findOne($id);

        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());

            if ($model->e_atualizar_site) {

                $produto_id = ProdutoFilial::findOne($model->produto_filial_id)->produto_id;
                $produto = Produto::findOne($produto_id);

                if ($produto->e_valor_bloqueado !== true) {

                    $modelValorProdutoFilial = new ValorProdutoFilial();
                    $markup = Yii::$app->db->createCommand("select margem from markup_detalhe 
                            inner join markup_mestre on markup_detalhe.markup_mestre_id = markup_mestre.id
                            where ($model->valor ::float between valor_minimo and valor_maximo) and markup_mestre.e_markup_padrao = true
                            order by markup_mestre.id desc 
                            limit 1")->queryScalar();

                    $modelValorProdutoFilial->valor = $markup < 5 ? ((float)$model->valor * $markup) : $markup;
                    $modelValorProdutoFilial->dt_inicio = date("Y-m-d H:i:s");
                    $modelValorProdutoFilial->produto_filial_id = $model->produto_filial_id;
                    $modelValorProdutoFilial->promocao = false;
                    $modelValorProdutoFilial->valor_compra = $model->valor;
                    $modelValorProdutoFilial->save();
                    ValorProdutoFilialController::AtualizarValorProdutoFilial($modelValorProdutoFilial);
                }
            }
            $model->save();
            $pedido_id = PedidoProdutoFilial::findOne(['id' => $model->pedido_produto_filial_id])->pedido_id;
            return $this->redirect(['pedidos/view', 'id' => $pedido_id]);
        } else {
            return $this->render('pedidos-internos/pedido-produto-filial-cotacao/create', [
                'model' => $model,
            ]);
        }
    }

    public function actionDeleteCotacao($id)
    {

        $model = PedidoProdutoFilialCotacao::findOne($id);
        $pedido_produto_filial_id = $model->pedido_produto_filial_id;
        $model->delete();

        return $this->redirect(['pedido-interno-produto', 'pedido_produto_filial_id' => $pedido_produto_filial_id]);
    }

    public function actionCreate($id = null)
    {
        $modelPedido = new Pedido();
        $modelComprador = new Comprador();
        $modelEmpresa = new Empresa();
        $modelEndEmpresa = new EnderecoEmpresa();
        $modelPedProdFilial = new PedidoProdutoFilial();
        $modelProdFilial = new ProdutoFilial();
        $dataProvider = null;

        $modelPedido->email_enderecos = "entregasp.pecaagora@gmail.com, compras.pecaagora@gmail.com";
        $modelPedido->email_assunto = "{de} Pedido Interno {num_pedido} ({codigo_fabricante} * {quantidade} - {vendedor})";

        $modelPedido->email_texto = "DESTACAR O ST RECOLHIDO ANTERIORMENTE EM INFORMAÇÕES ADICIONAIS E TAMBÉM NO XML DA NOTA, CASO CONTRÁRIO A MESMA SERÁ RECUSADA.

                    Cód.: {codigo}
                    Descrição: {descricao}
                    Quantidade: {quantidade}
                    Valor: R$ {valor}
                    Observação: {observacao}
                    NCM: {ncm}
                    PA{pa}
                    
                    Envio: Carmópolis de Minas, 963, Vila Maria.
                    
                    Atenciosamente,
                    Peça Agora
                    Site: https://www.pecaagora.com/
                    E-mail: compras.pecaagora@gmail.com Setor de Compras:(32)3015-0023 Whatsapp:(32)988354007
                    Skype: pecaagora";

        if ($id) {

            $modelPedido = Pedido::findOne($id);
            $modelComprador = Comprador::findOne($modelPedido->comprador_id);
            $modelEmpresa = Empresa::findOne($modelComprador->empresa_id);
            $modelEndEmpresa = EnderecoEmpresa::findOne(['empresa_id' => $modelEmpresa->id]);

            if (Yii::$app->request->post()) {
                $post = Yii::$app->request->post();

                if (!isset($post['Empresa']['documento']) || $post['Empresa']['documento']  == '') {

                    return $this->render('pedidos-internos/create', [
                        'modelPedido' => $modelPedido,
                        'modelComprador' => $modelComprador,
                        'modelEmpresa' => $modelEmpresa,
                        'modelEndEmpresa' => $modelEndEmpresa,
                        'modelPedProdFilial' => $modelPedProdFilial,
                        'modelProdFilial' => $modelProdFilial,
                        'dataProvider' => $dataProvider,
                        'mensagem' => 'CPF/CNPJ não preenchido'
                    ]);
                }

                $modelPedido->load(Yii::$app->request->post());
                $modelPedido->valor_total = floatval($modelPedido->valor_total) + ($modelPedido->valor_total = $post['PedidoProdutoFilial']['valor'] * $post['PedidoProdutoFilial']['quantidade']);
                $modelPedido->save(false);

                $modelComprador->load(Yii::$app->request->post());
                $modelComprador->save(false);

                $modelEmpresa->load(Yii::$app->request->post());
                $modelEmpresa->documento = str_replace('/', '', str_replace('-', '', str_replace('.', '', $modelEmpresa->documento)));
                $modelEmpresa->save(false);

                $modelEndEmpresa->load(Yii::$app->request->post());
                $modelEndEmpresa->save(false);

                $modelPedProdFilial->load(Yii::$app->request->post());
                $modelPedProdFilial->pedido_id = $modelPedido->id;
                $modelPedProdFilial->save(false);
                $modelPedProdFilial = new PedidoProdutoFilial();
            }

            $sql = new Query;
            $sql->Select([
                'pedido_produto_filial.pedido_id',
                'pedido_produto_filial.produto_filial_id',
                'produto.id',
                'produto.nome',
                'produto.codigo_global as codglobal',
                'pedido_produto_filial.valor',
                'pedido_produto_filial.quantidade',
                'pedido_produto_filial.valor_cotacao'
            ])
                ->from("produto")
                ->innerJoin("produto_filial", "produto_filial.produto_id = produto.id")
                ->innerJoin("pedido_produto_filial", "pedido_produto_filial.produto_filial_id = produto_filial.id")
                ->innerJoin("pedido", "pedido_produto_filial.pedido_id = pedido.id")
                ->where("pedido.id = $id")
                ->orderBy("produto.id ASC")
                ->all();

            $dataProvider = new ActiveDataProvider([
                'query' => $sql
            ]);
        } else {
            if (Yii::$app->request->post()) {
                $post = Yii::$app->request->post();

                if (!isset($post['Empresa']['documento']) || $post['Empresa']['documento']  == '') {

                    return $this->render('pedidos-internos/create', [
                        'modelPedido' => $modelPedido,
                        'modelComprador' => $modelComprador,
                        'modelEmpresa' => $modelEmpresa,
                        'modelEndEmpresa' => $modelEndEmpresa,
                        'modelPedProdFilial' => $modelPedProdFilial,
                        'modelProdFilial' => $modelProdFilial,
                        'dataProvider' => $dataProvider,
                        'mensagem' => 'CPF/CNPJ não preenchido'
                    ]);
                }

                $modelEmpresa = Empresa::findOne(['documento' => $post['Empresa']['documento']]);
                if ($modelEmpresa) {
                    $modelEmpresa->load(Yii::$app->request->post());
                    $modelEmpresa->documento = str_replace('/', '', str_replace('-', '', str_replace('.', '', $modelEmpresa->documento)));
                    $modelEmpresa->save(false);
                    $modelEndEmpresa = EnderecoEmpresa::findOne(['empresa_id' => $modelEmpresa->id]);
                    if ($modelEndEmpresa) {
                        $modelEndEmpresa->load(Yii::$app->request->post());
                        $modelEndEmpresa->save(false);
                    } else {
                        $modelEndEmpresa = new EnderecoEmpresa();
                        $modelEndEmpresa->load(Yii::$app->request->post());
                        $modelEndEmpresa->empresa_id = $modelEmpresa->id;
                        $modelEndEmpresa->save(false);
                    }

                    $modelComprador = new Comprador();
                    $modelComprador->cpf = $modelEmpresa->documento;
                    $modelComprador->nome = $modelEmpresa->nome;
                    $modelComprador->empresa_id = $modelEmpresa->id;
                    $modelComprador->dt_criacao = date('Y-m-d');
                    $modelComprador->ativo = true;
                    $modelComprador->email = $post['Empresa']['email'];
                    $modelComprador->nivel_acesso_id = 1;
                    $modelComprador->save(false);

                    $modelPedido->load(Yii::$app->request->post());
                    $modelPedido->valor_total = $post['PedidoProdutoFilial']['valor'] * $post['PedidoProdutoFilial']['quantidade'];
                    $modelPedido->comprador_id = $modelComprador->id;
                    $modelPedido->forma_pagamento_id = 2;
                    $modelPedido->administrador_id = Yii::$app->user->id;

                    $modelPedido->save(false);

                    $modelStatus = new StatusPedido();
                    $modelStatus->data_referencia = date('Y-m-d');
                    $modelStatus->pedido_id = $modelPedido->id;
                    $modelStatus->tipo_status_id = 1;
                    $modelStatus->save(false);

                    $modelPedProdFilial->load(Yii::$app->request->post());
                    $modelPedProdFilial->pedido_id = $modelPedido->id;
                    $modelPedProdFilial->save(false);
                    $modelPedProdFilial = new PedidoProdutoFilial();
                    $modelProdFilial = new ProdutoFilial();
                } else {
                    $modelEmpresa = new Empresa();
                    $modelEmpresa->load(Yii::$app->request->post());
                    $modelEmpresa->grupo_id = 81;
                    $modelEmpresa->documento = str_replace('/', '', str_replace('-', '', str_replace('.', '', $modelEmpresa->documento)));
                    $modelEmpresa->save(false);

                    $modelEndEmpresa->load(Yii::$app->request->post());
                    $modelEndEmpresa->empresa_id = $modelEmpresa->id;
                    $modelEndEmpresa->save(false);

                    $modelComprador = new Comprador();
                    $modelComprador->cpf = $modelEmpresa->documento;
                    $modelComprador->nome = $modelEmpresa->nome;
                    $modelComprador->empresa_id = $modelEmpresa->id;
                    $modelComprador->dt_criacao = date('Y-m-d');
                    $modelComprador->ativo = true;
                    $modelComprador->email = $post['Empresa']['email'];
                    $modelComprador->nivel_acesso_id = 1;
                    $modelComprador->save(false);

                    $modelPedido->load(Yii::$app->request->post());
                    $modelPedido->valor_total = $post['PedidoProdutoFilial']['valor'] * $post['PedidoProdutoFilial']['quantidade'];
                    $modelPedido->comprador_id = $modelComprador->id;
                    $modelPedido->forma_pagamento_id = 2;
                    $modelPedido->administrador_id = Yii::$app->user->id;
                    $modelPedido->save(false);

                    $modelStatus = new StatusPedido();
                    $modelStatus->data_referencia = date('Y-m-d');
                    $modelStatus->pedido_id = $modelPedido->id;
                    $modelStatus->tipo_status_id = 1;
                    $modelStatus->save(false);

                    $modelPedProdFilial->load(Yii::$app->request->post());
                    $modelPedProdFilial->pedido_id = $modelPedido->id;
                    $modelPedProdFilial->save(false);
                    $modelPedProdFilial = new PedidoProdutoFilial();
                    $modelProdFilial = new ProdutoFilial();
                }
                $sql = new Query;
                $sql->Select([
                    'pedido_produto_filial.pedido_id',
                    'pedido_produto_filial.produto_filial_id',
                    'produto.id',
                    'produto.nome',
                    'produto.codigo_global as codglobal',
                    'pedido_produto_filial.valor',
                    'pedido_produto_filial.quantidade',
                    'pedido_produto_filial.valor_cotacao'
                ])
                    ->from("produto")
                    ->innerJoin("produto_filial", "produto_filial.produto_id = produto.id")
                    ->innerJoin("pedido_produto_filial", "pedido_produto_filial.produto_filial_id = produto_filial.id")
                    ->innerJoin("pedido", "pedido_produto_filial.pedido_id = pedido.id")
                    ->where("pedido.id = $modelPedido->id")
                    ->orderBy("produto.id ASC")
                    ->all();

                $dataProvider = new ActiveDataProvider([
                    'query' => $sql
                ]);
            }
        }

        return $this->render('pedidos-internos/create', [
            'modelPedido' => $modelPedido,
            'modelComprador' => $modelComprador,
            'modelEmpresa' => $modelEmpresa,
            'modelEndEmpresa' => $modelEndEmpresa,
            'modelPedProdFilial' => $modelPedProdFilial,
            'modelProdFilial' => $modelProdFilial,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdateModel($id)
    {
        $model = Pedido::findOne($id);
        $model->load(Yii::$app->request->post());
        $model->save(false);

        return $this->redirect(Url::to(['/pedidos/view', 'id' => $model->id]));
    }



    /**
     * Displays a single Pedido model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $mensagem = null)
    {
        $model = $this->findModel($id);

        $dataProvider = new ActiveDataProvider([
            'query' => PedidoProdutoFilial::find()->with([
                'produtoFilial',
                'produtoFilial.produto'
            ])->where(['pedido_id' => $id]),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => false,
        ]);

        return $this->render('view', [
            'model' => $model,
            'pedidoStatus' => $model->status,
            'dataProvider' => $dataProvider,
            'mensagem' => $mensagem
        ]);
    }

    public function actionPedidoInternoProduto($pedido_produto_filial_id)
    {
        $pedido_interno_produto = PedidoProdutoFilial::findOne(['id' => $pedido_produto_filial_id]);


        $searchModelCotacao = new PedidoProdutoFilialCotacaoSearch();
        $dataProviderProdutoFilial = $searchModelCotacao->search(['PedidoProdutoFilialCotacaoSearch' => ['pedido_produto_filial_id' => $pedido_interno_produto['id']]]);

        return $this->render('_pedido-produto', [
            'model' => $pedido_interno_produto,
            'searchModelProdutoFilial' => $searchModelCotacao,
            'dataProviderProdutoFilial' => $dataProviderProdutoFilial,
        ]);
    }

    public function actionEmailRevisao($id)
    {
        $pedidoProduto = PedidoProdutoFilial::findAll(['pedido_id' => $id]);

        $email_texto = "Produtos a serem revisados para informações mais apuradas. (Imagens, Dimensões e dados pertinentes a venda do produto)\n\n";

        $enviar = false;

        foreach ($pedidoProduto as $produto) {
            if ($produto->e_revisao) {
                $enviar = true;
                $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $produto->produto_filial_id])->one();
                $email_texto .= "PA: " . $produto_filial->produto->id . "\nCódigo: " . $produto_filial->produto->codigo_fabricante . "\nDescrição: " . $produto_filial->produto->nome . "\nObservação: " . $produto->observacao . "\n\n";
            }
        }
        if ($enviar) {
            var_dump(\Yii::$app->mailer->compose()
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                // ->setTo('dev3.pecaagora@gmail.com')
                ->setTo('devolucoessp.pecaagora@gmail.com')
                ->setSubject("Produtos Pedido $id para revisão")
                ->setTextBody($email_texto)
                ->send());
        }
    }

    public function actionPedidoProdutoFilialCotacaoCreate($id)
    {
        if (Yii::$app->request->post()) {
            $modelCotacao = new PedidoProdutoFilialCotacao();
            $modelCotacao->pedido_produto_filial_id = $id;
            $modelCotacao->load(Yii::$app->request->post());

            $produto_id = ProdutoFilial::findOne($modelCotacao->produto_filial_id)->produto_id;
            $produto = Produto::findOne($produto_id);

            if ($produto->e_valor_bloqueado !== true) {

                if ($modelCotacao->e_atualizar_site) {
                    $modelValorProdutoFilial = new ValorProdutoFilial();
                    $markup = Yii::$app->db->createCommand("select margem from markup_detalhe 
                            inner join markup_mestre on markup_detalhe.markup_mestre_id = markup_mestre.id
                            where ($modelCotacao->valor ::float between valor_minimo and valor_maximo) and markup_mestre.e_markup_padrao = true
                            order by markup_mestre.id desc 
                            limit 1")->queryScalar();

                    $modelValorProdutoFilial->valor = $markup < 5 ? ((float)$modelCotacao->valor * $markup) : $markup;
                    $modelValorProdutoFilial->dt_inicio = date("Y-m-d H:i:s");
                    $modelValorProdutoFilial->produto_filial_id = $modelCotacao->produto_filial_id;
                    $modelValorProdutoFilial->promocao = false;
                    $modelValorProdutoFilial->valor_compra = $modelCotacao->valor;
                    $modelValorProdutoFilial->save();
                    ValorProdutoFilialController::AtualizarValorProdutoFilial($modelValorProdutoFilial);
                }
            }
            $modelCotacao->save();
            $pedido_id = PedidoProdutoFilial::findOne(['id' => $modelCotacao->pedido_produto_filial_id])->pedido_id;
            return $this->redirect(['pedidos/view', 'id' => $pedido_id]);
        } else {
            $modelProdutoFilial = PedidoProdutoFilial::findOne(['id' => $id]);
            $modelCotacao = new PedidoProdutoFilialCotacao();
            $modelCotacao->pedido_produto_filial_id = $id;
            $modelCotacao->produto_filial_id = $modelProdutoFilial['produto_filial_id'];
            $modelCotacao->valor = $modelProdutoFilial['valor_cotacao'];
            $modelCotacao->quantidade = $modelProdutoFilial['quantidade'];
            $modelCotacao->email = ProdutoFilial::findOne($modelProdutoFilial->produto_filial_id)->filial->email_pedido;

            return $this->render('pedidos-internos/pedido-produto-filial-cotacao/create', [
                'model' => $modelCotacao,
            ]);
        }
    }

    public function actionSkyhubView($code)
    {
        $skyhub = new SkyhubClient();
        $pedido = $skyhub->orders()->find($code);

        //echo "<pre>"; print_r($pedido); echo "</pre>";

        return $this->render('view/_b2w', [
            'model' => $pedido,
        ]);
    }

    /**
     * Updates an existing Pedido model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $pedido_interno_produto = PedidoProdutoFilial::findOne(['id' => $id]);
        $post = Yii::$app->request->post();
        $pedido_interno_produto->observacao = $post['PedidoProdutoFilial']['observacao'];
        $pedido_interno_produto->e_revisao = $post['PedidoProdutoFilial']['e_revisao'];
        $pedido_interno_produto->save();

        $searchModelCotacao = new PedidoProdutoFilialCotacaoSearch();
        $dataProviderProdutoFilial = $searchModelCotacao->search(['PedidoProdutoFilialCotacaoSearch' => ['pedido_produto_filial_id' => $pedido_interno_produto->id]]);

        return $this->render('_pedido-produto', [
            'model' => $pedido_interno_produto,
            'searchModelProdutoFilial' => $searchModelCotacao,
            'dataProviderProdutoFilial' => $dataProviderProdutoFilial,
        ]);
    }

    public function actionPedidoUpdate($id, $id_produto = null)
    {
        $modelPedido = Pedido::findOne($id);
        $modelComprador = Comprador::findOne($modelPedido->comprador_id);
        $modelEmpresa = Empresa::findOne($modelComprador->empresa_id);
        $modelEndEmpresa = EnderecoEmpresa::findOne(['empresa_id' => $modelEmpresa->id]);
        $modelPedProdFilial = new PedidoProdutoFilial();
        $modelProdFilial = new ProdutoFilial();

        if ($id_produto) {
            $modelPedProdFilial = PedidoProdutoFilial::findOne($id_produto);
        }

        if (Yii::$app->request->post()) {
            $post = Yii::$app->request->post();

            if (!isset($post['Empresa']['documento']) || $post['Empresa']['documento']  == '') {

                $sql = new Query;
                $sql->Select([
                    'produto.nome',
                    'produto.codigo_global as codglobal',
                    'pedido_produto_filial.valor',
                    'pedido_produto_filial.id',
                    'pedido_produto_filial.quantidade',
                    'pedido_produto_filial.valor_cotacao',
                    'pedido_produto_filial.pedido_id'
                ])
                    ->from("produto")
                    ->innerJoin("produto_filial", "produto_filial.produto_id = produto.id")
                    ->innerJoin("pedido_produto_filial", "pedido_produto_filial.produto_filial_id = produto_filial.id")
                    ->innerJoin("pedido", "pedido_produto_filial.pedido_id = pedido.id")
                    ->where("pedido.id = $id")
                    ->orderBy("produto.id ASC")
                    ->all();

                $dataProvider = new ActiveDataProvider([
                    'query' => $sql
                ]);

                return $this->render('pedidos-internos/update', [
                    'modelPedido' => $modelPedido,
                    'modelComprador' => $modelComprador,
                    'modelEmpresa' => $modelEmpresa,
                    'modelEndEmpresa' => $modelEndEmpresa,
                    'modelPedProdFilial' => $modelPedProdFilial,
                    'modelProdFilial' => $modelProdFilial,
                    'dataProvider' => $dataProvider,
                    'id_produto' => $id_produto,
                    'mensagem' => 'CPF/CNPJ não preenchido'
                ]);
            }

            $modelPedido->load(Yii::$app->request->post());
            $modelComprador->load(Yii::$app->request->post());
            $modelEmpresa->load(Yii::$app->request->post());
            $modelEndEmpresa->load(Yii::$app->request->post());

            if (isset($post['PedidoProdutoFilial']['produto_filial_id'])) {
                $modelPedProdFilial = PedidoProdutoFilial::findOne(['produto_filial_id' => $post['PedidoProdutoFilial']['produto_filial_id'], 'pedido_id' => $post['Pedido']['id']]);
                if ($modelPedProdFilial) {
                    $modelPedido->valor_total = $modelPedido->valor_total - ($modelPedProdFilial->valor * $modelPedProdFilial->quantidade);
                    $modelPedido->save(false);
                    $modelPedProdFilial->load(Yii::$app->request->post());
                    $modelPedProdFilial->save();
                    $modelPedido->valor_total = $modelPedido->valor_total + ($modelPedProdFilial->valor * $modelPedProdFilial->quantidade);
                    $modelPedido->save(false);
                    $modelPedProdFilial = new PedidoProdutoFilial();
                } else {
                    $modelPedProdFilial = new PedidoProdutoFilial();
                    $modelPedProdFilial->load(Yii::$app->request->post());
                    $modelPedProdFilial->pedido_id = $modelPedido->id;
                    $modelPedProdFilial->save(false);
                    $modelPedido->valor_total = $modelPedido->valor_total + ($modelPedProdFilial->valor * $modelPedProdFilial->quantidade);
                    $modelPedido->save(false);
                }
            }

            $modelPedido->save(false);
            $modelComprador->save(false);
            $modelEmpresa->save(false);
            $modelEndEmpresa->save(false);
        }

        $sql = new Query;
        $sql->Select([
            'produto.nome',
            'produto.codigo_global as codglobal',
            'pedido_produto_filial.valor',
            'pedido_produto_filial.id',
            'pedido_produto_filial.quantidade',
            'pedido_produto_filial.valor_cotacao',
            'pedido_produto_filial.pedido_id'
        ])
            ->from("produto")
            ->innerJoin("produto_filial", "produto_filial.produto_id = produto.id")
            ->innerJoin("pedido_produto_filial", "pedido_produto_filial.produto_filial_id = produto_filial.id")
            ->innerJoin("pedido", "pedido_produto_filial.pedido_id = pedido.id")
            ->where("pedido.id = $id")
            ->orderBy("produto.id ASC")
            ->all();

        $dataProvider = new ActiveDataProvider([
            'query' => $sql
        ]);

        return $this->render('pedidos-internos/update', [
            'modelPedido' => $modelPedido,
            'modelComprador' => $modelComprador,
            'modelEmpresa' => $modelEmpresa,
            'modelEndEmpresa' => $modelEndEmpresa,
            'modelPedProdFilial' => $modelPedProdFilial,
            'modelProdFilial' => $modelProdFilial,
            'dataProvider' => $dataProvider,
            'id_produto' => $id_produto
        ]);
    }

    public function actionPedidoDelete($id)
    {
        $modelProduto = PedidoProdutoFilial::findOne($id);
        $modelPedido = Pedido::findOne($modelProduto->pedido_id);
        if (!$modelPedido->e_pedido_autorizado) {
            $modelPedidoCotacao = PedidoProdutoFilialCotacao::findAll(['pedido_produto_filial_id' => $id]);
            if ($modelPedidoCotacao) {
                foreach ($modelPedidoCotacao as $cotacao) {
                    $cotacao->delete();
                }
            }
            $modelPedido->valor_total = $modelPedido->valor_total - ($modelProduto->valor * $modelProduto->quantidade);
            $modelPedido->save();
            $modelProduto->delete();

            return $this->redirect(Url::to(['/pedidos/view', 'id' => $modelProduto->pedido_id]));
        } else {
            throw new Exception("Produto não pode ser excluído pois o pedido já está autorizado.");
        }
    }

    /**
     * Deletes an existing Pedido model.
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
     * Altera status do pedido
     *
     * @param $id
     * @param $status
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @author Vitor Horta 26/03/2015
     * @since 0.1
     */
    /**
     * Finds the Pedido model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pedido the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pedido::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGerarXMLOmie($id)
    {

        $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(['=', 'id', $id])->one();
        if ($pedido_mercado_livre) {

            $meli = new Omie(1, 1);
            $APP_KEY_OMIE       = '468080198586';
            $APP_SECRET_OMIE    = '7b3fb2b3bae35eca3b051b825b6d9f43';

            $body = [
                "call" => "ConsultarPedido",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "codigo_pedido_integracao" => $pedido_mercado_livre->pedido_meli_id,
                ]
            ];
            $response_pedido = $meli->consulta_pedido("api/v1/geral/pedido/?JSON=", $body);
            //echo "<pre>"; print_r($response_pedido); echo "</pre>"; die;

            $body = [
                "call" => "ConsultarNF",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "nIdPedido" => ArrayHelper::getValue($response_pedido, 'body.pedido_venda_produto.cabecalho.codigo_pedido'),
                ]
            ];
            $response_nota_fiscal = $meli->consulta("/api/v1/produtos/nfconsultar/?JSON=", $body);
            //echo "<pre>"; print_r($response_nota_fiscal); echo "</pre>";die;

            $body = [
                "call" => "GetUrlNotaFiscal",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "nCodNF" => ArrayHelper::getValue($response_nota_fiscal, 'body.compl.nIdNF'),
                ]
            ];
            $response_url_nota_fiscal = $meli->consulta("/api/v1/produtos/notafiscalutil/?JSON=", $body);
            //echo "<pre>"; print_r($response_url_nota_fiscal); echo "</pre>"; die;

            //return $this->redirect(ArrayHelper::getValue($response_url_nota_fiscal, 'body.cUrlNF'));

            $url_xml_nota_fiscal = ArrayHelper::getValue($response_url_nota_fiscal, 'body.cUrlNF');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url_xml_nota_fiscal);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $xml_nota_fiscal = curl_exec($ch);
            curl_close($ch);
            //print_r($xml_nota_fiscal);

            $arquivo_nome = "/var/tmp/" . ArrayHelper::getValue($response_nota_fiscal, 'body.compl.nIdNF') . ".xml";
            $arquivo = fopen($arquivo_nome, "a");
            fwrite($arquivo, $xml_nota_fiscal);
            fclose($arquivo);

            header('Content-disposition: Attachment; filename="' . ArrayHelper::getValue($response_nota_fiscal, 'body.compl.nIdNF') . '.xml"');
            header('Content-type: "text/xml"; charset="utf8"');
            readfile($arquivo_nome);
        }

        return $this->redirect(['index']);
    }

    public function actionEmailCompras($id, $num_omie = null)
    {
        $model = Pedido::findOne($id);
        $PedidoProdutoFilial = PedidoProdutoFilial::findAll(['pedido_id' => $model->id]);
        $comprador = Comprador::findOne($model->comprador_id);
        $empresa = Empresa::findOne($comprador->empresa_id);
        $endEmpresa = EnderecoEmpresa::findOne(['empresa_id' => $empresa->id]);
        $transportadora = Transportadora::findOne($model->transportadora_id);
        $estado = $endEmpresa->cidade->estado->sigla;
        $omie = '';

        if ($model->filial_id) {
            switch ($model->filial_id) {
                case 96:
                    $omie = 'SP2';
                    break;
                case 94:
                    $omie = 'MG1';
                    break;
                case 93:
                    $omie = 'MG4';
                    break;
                default:
                    $omie = 'SP3';
                    break;
            }
        } else {
            $omie = 'MG4';
        }


        $tipo_pedido = 'Site';
        $vendedor = 'Site';
        $filialVendedor = '';
        $modalidade = '';
        $de = '';
        $pf_pj = 'PJ';
        $comprado_por = '';
        $vendido_por = '';

        if ($model->compra_filial_id == 96 || $model->compra_filial_id == 95) {
            $comprado_por = 'SP';
        } else {
            $comprado_por = 'MG';
        }

        if ($model->filial_id == 96 || $model->filial_id == 95) {
            $vendido_por = 'SP';
        } else {
            $vendido_por = 'MG';
        }

        if ($model->tipo_frete == 0) {
            $modalidade = 'Frete por conta do remetente';
        } else if ($model->tipo_frete == 1) {
            $modalidade = 'Frete por conta do destinatário';
        } else {
            $modalidade = 'Sem ocorrência de transporte';
        }

        if ($estado == 'SP' && ($model->filial_id == 96 || $model->filial_id == 95)) {
            $de = 'DE';
        } else if ($estado == 'MG' && $model->filial_id == 94) {
            $de = 'DE';
        } else {
            $de = 'FE';
        }

        if (strlen($empresa->documento) < 14) {
            $pf_pj = 'PF';
        }

        if ($model->administrador_id) {
            $tipo_pedido = 'Interno';
            $admin = Administrador::findOne($model->administrador_id);
            $vendedor = $admin->nome;
            $filialVendedor = $admin->filial_id;
        }

        $total_cotacao = 0;

        $produtos = '';

        foreach ($PedidoProdutoFilial as $pedidoProduto) {
            $pedidoProdutoFilialCotacao = PedidoProdutoFilialCotacao::findAll(['pedido_produto_filial_id' => $pedidoProduto->id]);
            foreach ($pedidoProdutoFilialCotacao as $pedidoCotacao) {
                $total_cotacao += $pedidoCotacao->valor;
                $produto_filial = ProdutoFilial::findOne($pedidoCotacao->produto_filial_id);
                $produtos .= "\n
                Cód.: " . $produto_filial->produto->codigo_fabricante . "
                Descrição: " . $produto_filial->produto->nome . " (" . $produto_filial->produto->codigo_global . ")
                Quantidade: " . $pedidoCotacao->quantidade . "
                Valor: " . $pedidoCotacao->valor . " * " . $pedidoCotacao->quantidade . "
                Observação: " . $pedidoCotacao->observacao . "
                NCM: " . $produto_filial->produto->codigo_montadora . "
                PA" . $produto_filial->produto->id . "
                Filial: " . $produto_filial->filial->nome;
            }
        }

        $margem = $model->valor_total / $total_cotacao;

        $dados_pedido = "Integrado ao Omie com o Número $num_omie \nTransportadora: " . $transportadora->nome . "\n" . "Valor Frete: R$" . number_format($model->valor_frete, 2, ',', ' ') . "\n" . "Modalidade: " . $modalidade;

        $emails = 'entregasp.pecaagora@gmail.com,compras.pecaagora@gmail.com,logistica.pecaagora@gmail.com';

        if ($vendedor !== 'Site') {
            if ($filialVendedor == 96) {
                //$emails .= ',notafiscalsp.pecaagora@gmail.com,notafiscal.pecaagora@gmail.com';
		$emails .= ',notafiscalsp.pecaagora@gmail.com';
            } else {
                $emails .= ',notafiscal.pecaagora@gmail.com';
            }
        } else {
            $emails .= ',notafiscal.pecaagora@gmail.com';
        }


        if ($model->e_email_estoque) {
            $emails .= ',estoque.pecaagora@gmail.com';
            $dados_pedido .= "\nPRODUTO COMPRADO POR $comprado_por - VENDIDO POR $vendido_por - FAVOR ACERTAR OS ESTOQUES";
        }

        $dados_pedido .= $produtos;
        if ($model->e_pedido_autorizado == true) {
            $emails_destinatarios = explode(",", $emails);

	    //echo "<pre>"; print_r($emails_destinatarios); echo "</pre>"; die;

            var_dump(\Yii::$app->mailer->compose()
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                ->setTo($emails_destinatarios)
                ->setSubject("[Omie $omie][$de] Pedido $tipo_pedido ($model->id - $empresa->nome -  $pf_pj - $estado) (" . number_format($margem, 2, '.', ' ') . ") Vendedor: $vendedor")
                ->setTextBody($dados_pedido)
                ->send());
        }
    }

    public function actionEmailPorFornecedor($id)
    {
        $modelProduto = PedidoProdutoFilial::findOne($id);

        if (!$modelProduto->e_email_enviado) {

            $modelPedido = Pedido::findOne($modelProduto->pedido_id);
            $administrador = Administrador::findOne($modelPedido->administrador_id)->nome;

            $pedidoProdutoCotacao = PedidoProdutoFilialCotacao::findAll(['pedido_produto_filial_id' => $modelProduto->id]);

            foreach ($pedidoProdutoCotacao as $pedido_cotacao) {
                $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $pedido_cotacao->produto_filial_id])->one();

                $email_texto = $modelPedido->email_texto;

                $emails = $modelPedido->email_enderecos . (($pedido_cotacao->email != null && $pedido_cotacao->email != "") ? "," . $pedido_cotacao->email : "");
                $emails = str_replace(";", ",", str_replace(" ", "", $emails));
                $emails_destinatarios = explode(",", $emails);

                if ($pedido_cotacao->quantidade > 0) {

                    $texto_unidades = (($pedido_cotacao->quantidade > 1) ? " Unidades" : " Unidade");

                    $codigo_fabricante = $produto_filial->produto->codigo_fabricante;
                    switch ($produto_filial->filial_id) {
                        case 43:
                            $codigo_fabricante = str_replace('.M', '', $codigo_fabricante);
                            break;
                        case 60:
                            $codigo_fabricante = str_replace('L', '', $codigo_fabricante);
                            $codigo_fabricante = substr($codigo_fabricante, 0, 2) . "-" . substr($codigo_fabricante, 2);
                            break;
                        case 72:
                            $codigo_fabricante = str_replace('.B', '', $codigo_fabricante);
                            break;
                        case 97:
                            $codigo_fabricante = str_replace('D', '', $codigo_fabricante);
                            break;
                    }

                    //Preencher estoques do fornecedor e interno
                    if ($produto_filial->filial_id == 96) {
                        $email_texto = str_replace('Atenciosamente,', "INT: " . $produto_filial->quantidade . "\n\nAtenciosamente,", $email_texto);
                    } else {
                        $texto_quantidade_estoque = "EXT: " . $produto_filial->quantidade;

                        $produto_filial_pecaagorafisica = ProdutoFilial::find()->andWhere(["=", "filial_id", 96])
                            ->andWhere(["=", "produto_id", $produto_filial->produto_id])
                            ->one();
                        if ($produto_filial_pecaagorafisica) {
                            $texto_quantidade_estoque .= "\nINT: " . $produto_filial_pecaagorafisica->quantidade;
                        }

                        $email_texto = str_replace('Atenciosamente,', $texto_quantidade_estoque . "\n\nAtenciosamente,", $email_texto);
                    }

                    $email_texto = str_replace("{codigo}", $codigo_fabricante, $email_texto);
                    $email_texto = str_replace("{descricao}", $produto_filial->produto->nome . " (" . $produto_filial->produto->codigo_global . ")", $email_texto);
                    $email_texto = str_replace("{quantidade}", " * " . $pedido_cotacao->quantidade . " " . $texto_unidades, $email_texto);
                    $email_texto = str_replace("{valor}", $pedido_cotacao->valor . " * " . $pedido_cotacao->quantidade . " " . $texto_unidades, $email_texto);

                    if ($pedido_cotacao->observacao != null && $pedido_cotacao->observacao != "") {
                        $email_texto = str_replace("{observacao}", $pedido_cotacao->observacao, $email_texto);
                    } else {
                        $email_texto = str_replace("\nObservação: {observacao}", "", $email_texto);
                    }

                    $codigo_montadora = $produto_filial->produto->codigo_montadora;

                    $email_texto = str_replace("{ncm}", $codigo_montadora, $email_texto);
                    $email_texto = str_replace("{pa}", $produto_filial->produto->id, $email_texto);


                    $assunto = $modelPedido->email_assunto;
                    $assunto = str_replace("{quantidade}", $pedido_cotacao->quantidade . " " . $texto_unidades, $assunto);
                    $assunto = str_replace("{codigo_fabricante}", $codigo_fabricante, $assunto);
                    $assunto = str_replace("{num_pedido}", $modelPedido->id, $assunto);
                    $assunto = str_replace("{vendedor}", $administrador, $assunto);

                    $de = 'DE';

                    $comprador = Comprador::findOne($modelPedido->comprador_id);
                    $empresa = Empresa::findOne($comprador->empresa_id);
                    $endEmpresa = EnderecoEmpresa::findOne(['empresa_id' => $empresa->id]);

                    if ($endEmpresa->cidade->nome == 'São Paulo') {
                        $assunto = str_replace("{de}", $de, $assunto);
                    } else {
                        $de = 'FE';
                        $assunto = str_replace("{de}", $de, $assunto);
                    }

                    var_dump(\Yii::$app->mailer->compose()
                        ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                        ->setTo($emails_destinatarios)
                        ->setSubject($assunto)
                        ->setTextBody($email_texto)
                        ->send());
                    $modelProduto->e_email_enviado = true;
                    $modelProduto->save();
                }
            }

            return $this->redirect(Url::to(['/pedidos/view', 'id' => $modelProduto->pedido_id]));
        } else {
            throw new Exception("produto já enviado para o fornecedor");
        }
    }


    public function actionEmailLogistica($id, $num_pedido)
    {
        $model = Pedido::findOne($id);
        $PedidoProdutoFilial = PedidoProdutoFilial::findAll(['pedido_id' => $model->id]);
        $transportadora = Transportadora::findOne($model->transportadora_id);
        $comprador = Comprador::findOne($model->comprador_id);
        $empresa = Empresa::findOne($comprador->empresa_id);
        $endEmpresa = EnderecoEmpresa::findOne(['empresa_id' => $empresa->id]);
        $transportadora = Transportadora::findOne($model->transportadora_id);

        $tipo_pedido = 'Site';
        $vendedor = 'Site';
        $produtos = '';

        foreach ($PedidoProdutoFilial as $pedidoProduto) {
            $produto_filial = ProdutoFilial::findOne($pedidoProduto->produto_filial_id);
            $produtos .= "produto - " . $produto_filial->produto->nome . " Quantidade - " . $pedidoProduto->quantidade . " \n" . "Cód. Global - " . $produto_filial->produto->codigo_global . " \n";
        }

        if ($model->administrador_id) {
            $tipo_pedido = 'Interno';
            $admin = Administrador::findOne($model->administrador_id);
            $vendedor = $admin->nome;
        }

        $texto = "Pedido - $model->id
        Pedido Omie - $num_pedido - 
        Vendedor - $vendedor
        Transportadora - $transportadora->nome
        Nome - $empresa->razao
        Rua - $endEmpresa->logradouro - $endEmpresa->complemento - $endEmpresa->numero
        Bairro - $endEmpresa->bairro
        Cep - $endEmpresa->cep
        $produtos";

        $emails = 'logistica.pecaagora@gmail.com,compras.pecaagora@gmail.com';

        if ($model->e_pedido_autorizado) {
            $emails_destinatarios = explode(",", $emails);
            var_dump(\Yii::$app->mailer->compose()
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                ->setTo($emails_destinatarios)
                ->setSubject("Pedido $tipo_pedido ($model->id) (LOGISTICA)")
                ->setTextBody($texto)
                ->send());
        }
    }

    public function actionEmailNotaFiscal($id, $num_pedido)
    {
        $model = Pedido::findOne($id);
        $produto = PedidoProdutoFilial::findOne(['pedido_id' => $model->id]);
        $produto_filial = ProdutoFilial::findOne($produto->produto_filial_id);
        $comprador = Comprador::findOne($model->comprador_id);
        $empresa = Empresa::findOne($comprador->empresa_id);

        $tipo_pedido = 'Site';
        $omie = 'SP';
        $tipo_banco = 'Moip';

        if ($model->administrador_id) {
            $tipo_pedido = 'Interno';
            $tipo_banco = $model->observacao;
            $omie = $model->filial->nome;
        }

        $texto = "Pedido - $model->id
        Pedido Omie - $num_pedido - $omie
        Dados Bancários - $tipo_banco
        Nome - $empresa->razao
        Produto - " . $produto_filial->produto->nome . " Cód. Globla - " . $produto_filial->produto->codigo_global;

        $emails = 'notafiscal.pecaagora@gmail.com,compras.pecaagora@gmail.com';

        if ($model->e_pedido_autorizado) {
            $emails_destinatarios = explode(",", $emails);
            var_dump(\Yii::$app->mailer->compose()
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                ->setTo($emails_destinatarios)
                ->setSubject("Pedido $tipo_pedido ($model->id) (Nota Fiscal)")
                ->setTextBody($texto)
                ->send());
        }
    }

    public function actionAutorizarPedido($id)
    {
        $model = Pedido::findOne($id);
        $administrador = Administrador::findOne($model->administrador_id);
        $pedido_produto_filial = PedidoProdutoFilial::find()->andWhere(['=', 'pedido_id', $model->id])->all();

        if (is_null($model->e_pedido_autorizado) || $model->e_pedido_autorizado == false) {

            $model->e_pedido_autorizado = true;
            $model->save(false);

            foreach ($pedido_produto_filial as $k => $pedido_produto) {

                $pedido_produto_filial_cotacao = PedidoProdutoFilialCotacao::find()->Where(['pedido_produto_filial_id' => $pedido_produto->id])->all();

                foreach ($pedido_produto_filial_cotacao as $pedido_cotacao) {

                    $modelNFProdutoValidacao = new NotaFiscalPedidoProduto();
                    $modelNFProdutoValidacao->pedido_produto_filial_cotacao_id = $pedido_cotacao->id;
                    $modelNFProdutoValidacao->save();
                    $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $pedido_cotacao->produto_filial_id])->one();

                    $email_texto = $model->email_texto;

                    $emails = $model->email_enderecos . (($pedido_cotacao->email != null && $pedido_cotacao->email != "") ? "," . $pedido_cotacao->email : "");
                    $emails = str_replace(";", ",", str_replace(" ", "", $emails));
                    $emails_destinatarios = explode(",", $emails);

                    if ($pedido_cotacao->quantidade > 0) {

                        $texto_unidades = (($pedido_cotacao->quantidade > 1) ? " Unidades" : " Unidade");

                        $codigo_fabricante = $produto_filial->produto->codigo_fabricante;
                        switch ($produto_filial->filial_id) {
                            case 43:
                                $codigo_fabricante = str_replace('.M', '', $codigo_fabricante);
                                break;
                            case 60:
                                $codigo_fabricante = str_replace('L', '', $codigo_fabricante);
                                $codigo_fabricante = substr($codigo_fabricante, 0, 2) . "-" . substr($codigo_fabricante, 2);
                                break;
                            case 72:
                                $codigo_fabricante = str_replace('.B', '', $codigo_fabricante);
                                break;
                            case 97:
                                $codigo_fabricante = str_replace('D', '', $codigo_fabricante);
                                break;
                        }

                        //Preencher estoques do fornecedor e interno
                        if ($produto_filial->filial_id == 96) {
                            $email_texto = str_replace('Atenciosamente,', "INT: " . $produto_filial->quantidade . "\n\nAtenciosamente,", $email_texto);
                        } else {
                            $texto_quantidade_estoque = "EXT: " . $produto_filial->quantidade;

                            $produto_filial_pecaagorafisica = ProdutoFilial::find()->andWhere(["=", "filial_id", 96])
                                ->andWhere(["=", "produto_id", $produto_filial->produto_id])
                                ->one();
                            if ($produto_filial_pecaagorafisica) {
                                $texto_quantidade_estoque .= "\nINT: " . $produto_filial_pecaagorafisica->quantidade;
                            }

                            $email_texto = str_replace('Atenciosamente,', $texto_quantidade_estoque . "\n\nAtenciosamente,", $email_texto);
                        }

                        $email_texto = str_replace("{codigo}", $codigo_fabricante, $email_texto);
                        $email_texto = str_replace("{descricao}", $produto_filial->produto->nome . " (" . $produto_filial->produto->codigo_global . ")", $email_texto);
                        $email_texto = str_replace("{quantidade}", " * " . $pedido_cotacao->quantidade . " " . $texto_unidades, $email_texto);
                        $email_texto = str_replace("{valor}", $pedido_cotacao->valor . " * " . $pedido_cotacao->quantidade . " " . $texto_unidades, $email_texto);

                        if ($pedido_cotacao->observacao != null && $pedido_cotacao->observacao != "") {
                            $email_texto = str_replace("{observacao}", $pedido_cotacao->observacao, $email_texto);
                        } else {
                            $email_texto = str_replace("\nObservação: {observacao}", "", $email_texto);
                        }

                        $codigo_montadora = $produto_filial->produto->codigo_montadora;

                        $email_texto = str_replace("{ncm}", $codigo_montadora, $email_texto);
                        $email_texto = str_replace("{pa}", $produto_filial->produto->id, $email_texto);

                        $assunto = $model->email_assunto; //"Pedido ".$codigo_fabricante." * ". $pedido_mercado_livre_produto_produto_filial->quantidade." ".$texto_unidades;
                        $assunto = str_replace("{quantidade}", $pedido_cotacao->quantidade . " " . $texto_unidades, $assunto);
                        $assunto = str_replace("{codigo_fabricante}", $codigo_fabricante, $assunto);
                        $assunto = str_replace("{num_pedido}", $model->id, $assunto);
                        if ($administrador) {
                            $assunto = str_replace("{vendedor}", $administrador->nome, $assunto);
                        } else {
                            $assunto = str_replace("{vendedor}", "Site", $assunto);
                        }

                        $de = 'DE';

                        $comprador = Comprador::findOne($model->comprador_id);
                        $empresa = Empresa::findOne($comprador->empresa_id);
                        $endEmpresa = EnderecoEmpresa::findOne(['empresa_id' => $empresa->id]);

                        if ($endEmpresa->cidade->nome == 'São Paulo') {
                            $assunto = str_replace("{de}", $de, $assunto);
                        } else {
                            $de = 'FE';
                            $assunto = str_replace("{de}", $de, $assunto);
                        }

                        if ($model->e_pedido_autorizado == true) {
                            var_dump(\Yii::$app->mailer->compose()
                                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                                ->setTo($emails_destinatarios)
                                ->setSubject($assunto)
                                ->setTextBody($email_texto)
                                ->send());
                        }
                    }
                }
            }

            $this->actionCriarPedidoOmie($model->id);
        } else {
            return $this->actionView($id, 'Pedido já Autorizado');
        }
    }

    public function actionGetTransportadora($q, $filial, $id = null)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }
        if ($id > 0) {
            return ['results' => ['id' => $id, 'text' => Transportadora::findOne($id)->nome]];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = Transportadora::find()
                ->select([
                    'transportadora.id', 'transportadora.nome as text'
                ])
                ->where([
                    'like',
                    'lower(transportadora.nome)',
                    strtolower($q)
                ])
                ->andWhere(['=', 'filial_id', $filial])
                ->orderBy(["transportadora.nome" => SORT_ASC])
                ->limit(10)
                ->createCommand()
                ->queryAll();
            $out['results'] = array_values($results);
        }

        return $out;
    }

    public function actionGetContaCorrente($filial, $id = null)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }
        if ($id > 0) {
            return ['results' => ['id' => $id, 'text' => ContaCorrente::findOne($id)->descricao]];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($filial)) {
            $results = ContaCorrente::find()
                ->select([
                    'conta_corrente.id', 'conta_corrente.descricao as text'
                ])
                ->andWhere(['=', 'filial_id', $filial])
                ->orderBy(["conta_corrente.descricao" => SORT_ASC])
                ->createCommand()
                ->queryAll();

            $out['results'] = array_values($results);
        }

        return $out;
    }

    public function actionGetAdministrador($q, $id = null)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }
        if ($id > 0) {
            return ['results' => ['id' => $id, 'text' => Administrador::findOne($id)->nome]];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = Administrador::find()
                ->select([
                    'administrador.id', 'administrador.nome as text'
                ])
                ->where([
                    'like',
                    'lower(administrador.nome)',
                    strtolower($q)
                ])
                ->orderBy(["administrador.nome" => SORT_ASC])
                ->createCommand()
                ->queryAll();

            $out['results'] = array_values($results);
        }

        return $out;
    }

    public function actionDesautorizarPedido($id)
    {
        $model = Pedido::findOne($id);

        $model->e_pedido_autorizado = false;
        $model->save();

        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionCriarPedidoOmie($id)
    {
        $result = FunctionsOmie::CriarPedido($id);

        if (isset($result['numero_pedido']) && !empty($result['numero_pedido'])) {
            $this->actionEmailCompras($id, $result['numero_pedido']);
        }

        return $this->actionView($id, $result['mensagem']);
    }


    public function actionGetEndereco($cep)
    {
        $cep = str_replace('-', '', $cep);
        $cep = substr($cep, 0, 8);
        try {
            $end = file_get_contents('https://viacep.com.br/ws/' . $cep . '/json/');
        } catch (ErrorException $e) {
            return Json::encode(['error' => true]);
        }
        $endereco = Json::decode($end);
        if (!empty($endereco)) {
            return Json::encode($endereco);
        }

        return $endereco;
    }

    // public function actionGetCliente($cnpj)
    // {
    //     $cnpj = str_replace('-', '', $cnpj);
    //     $cnpj = str_replace('/', '', $cnpj);

    //     $empresa = Empresa::findOne(['documento' => $cnpj]);
    //     if ($empresa->documento == null || $empresa->documento == '') {
    //         return '{"nome":"","email":"","telefone":"", "logradouro":"", "cidade_id":"","bairro":"","cep":"","numero":""}';
    //     }
    //     $endEmpresa = EnderecoEmpresa::findOne(['empresa_id' => $empresa->id]);
    //     if ($empresa) {
    //         return '{"nome":"' . $empresa->nome . '","email":"' . $empresa->email . '","telefone":"' . $empresa->telefone . '", "logradouro":"' . $endEmpresa->logradouro . '", "cidade_id":"' . $endEmpresa->cidade_id . '","bairro":"' . $endEmpresa->bairro . '","cep":"' . $endEmpresa->cep . '","numero":"' . $endEmpresa->numero . '"}';
    //     }
    // }

    public function actionGetCliente($dados)
    {
        if (is_numeric($dados)) {
            $empresa = Empresa::findOne(['documento' => $dados]);
            if ($empresa->documento == null || $empresa->documento == '') {
                return '{"nome":"","email":"","telefone":"", "logradouro":"", "cidade_id":"","bairro":"","cep":"","numero":""}';
            }
            $endEmpresa = EnderecoEmpresa::findOne(['empresa_id' => $empresa->id]);
            if ($empresa) {
                return '{"nome":"' . $empresa->nome . '","email":"' . $empresa->email . '","telefone":"' . $empresa->telefone . '", "logradouro":"' . $endEmpresa->logradouro . '", "cidade_id":"' . $endEmpresa->cidade_id . '","bairro":"' . $endEmpresa->bairro . '","cep":"' . $endEmpresa->cep . '","numero":"' . $endEmpresa->numero . '"}';
            }
        } else {
            $empresa = Empresa::findOne(['nome' => $dados]);
            if ($empresa->documento == null || $empresa->documento == '') {
                return '{"cnpj":"","email":"","telefone":"", "logradouro":"", "cidade_id":"","bairro":"","cep":"","numero":""}';
            }
            $endEmpresa = EnderecoEmpresa::findOne(['empresa_id' => $empresa->id]);
            if ($empresa) {
                return '{"cnpj":"' . $empresa->documento . '","email":"' . $empresa->email . '","telefone":"' . $empresa->telefone . '", "logradouro":"' . $endEmpresa->logradouro . '", "cidade_id":"' . $endEmpresa->cidade_id . '","bairro":"' . $endEmpresa->bairro . '","cep":"' . $endEmpresa->cep . '","numero":"' . $endEmpresa->numero . '"}';
            }
        }
    }

    public function actionGetDadosCliente($q = null)
    {
        $query = new Query;

        $query->select('nome')
            ->from('empresa')
            ->where("nome ILIKE '%" . $q . "%'")
            ->orderBy('nome');
        $command = $query->createCommand();
        $data = $command->queryAll();
        $out = [];
        foreach ($data as $d) {
            $out[] = ['value' => $d['nome']];
        }
        echo Json::encode($out);
    }

    public function actionBaixarPedidoOmie($num_pedido)
    {
        $acessoOmie = [
            '468080198586'  => '7b3fb2b3bae35eca3b051b825b6d9f43',
            '469728530271'  => '6b63421c9bb3a124e012a6bb75ef4ace',
            '1017311982687' => '78ba33370fac6178da52d42240591291',
            '1758907907757' => '0a69c9b49e5a188e5f43d5505f2752bc'
        ];

        $omie = new Omie(1, 1);
        $filial = '';

        foreach ($acessoOmie as $key => $value) {

            $APP_KEY_OMIE            = $key;
            $APP_SECRET_OMIE         = $value;

            $body = [
                "call" => "ConsultarPedido",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "numero_pedido" => "$num_pedido",
                ]
            ];

            $respPedido = $omie->consulta("/api/v1/produtos/pedido/?JSON=", $body);
            $respPedido = (object) $respPedido;

            if ($respPedido->body) {
                if (isset($respPedido->body["pedido_venda_produto"])) {

                    if ($key == '468080198586') {
                        $filial = 96;
                    } else if ($key == '469728530271') {
                        $filial = 94;
                    } else if ($key == '1758907907757') {
                        $filial = 93;
                    } else {
                        $filial = 95;
                    }

                    $codigo_cliente = $respPedido->body["pedido_venda_produto"]['cabecalho']["codigo_cliente"];
                    $comprador_id = '';
                    $empresa = '';
                    $comprador = '';
                    $endEmpresa = '';
                    $grupo = '';

                    $body = [
                        "call" => "ConsultarCliente",
                        "app_key" => $APP_KEY_OMIE,
                        "app_secret" => $APP_SECRET_OMIE,
                        "param" => [
                            "codigo_cliente_omie" => $codigo_cliente,
                        ]
                    ];

                    $respCliente = $omie->consulta("api/v1/geral/clientes/?JSON=", $body);
                    $respCliente = (object) $respCliente;

                    if ($respCliente->body) {
                        $cpf_cnpj = str_replace('-', '', str_replace('.', '', str_replace('/', '', $respCliente->body['cnpj_cpf'])));

                        $empresa = Empresa::findOne(['documento' => $cpf_cnpj]);

                        if ($empresa) {
                            $endEmpresa = EnderecoEmpresa::findOne(['empresa_id' => $empresa->id]);
                            if ($endEmpresa) {
                                $endEmpresa->logradouro = $respCliente->body['endereco'];
                                $endEmpresa->cep = $respCliente->body['cep'];
                                $endEmpresa->bairro = $respCliente->body['bairro'];
                                $endEmpresa->numero = $respCliente->body['endereco_numero'];
                                $endEmpresa->cidade_id = $respCliente->body['cidade_ibge'];
                                $endEmpresa->save(false);
                            } else {
                                $endEmpresa = new EnderecoEmpresa();
                                $endEmpresa->empresa_id = $empresa->id;
                                $endEmpresa->logradouro = $respCliente->body['endereco'];
                                $endEmpresa->cep = $respCliente->body['cep'];
                                $endEmpresa->bairro = $respCliente->body['bairro'];
                                $endEmpresa->numero = $respCliente->body['endereco_numero'];
                                $endEmpresa->cidade_id = $respCliente->body['cidade_ibge'];
                                $endEmpresa->save(false);
                            }
                            $comprador = Comprador::findOne(['cpf' => $empresa->documento]);

                            if ($comprador) {
                                $comprador_id = $comprador->id;
                                $endEmpresa = EnderecoEmpresa::findOne(['empresa_id' => $empresa->id]);
                            } else {
                                $comprador = new Comprador();
                                $comprador->empresa_id = $empresa->id;
                                $comprador->cpf = $empresa->documento;
                                $comprador->nome = $respCliente->body['nome_fantasia'];
                                $comprador->dt_criacao = date("Y-m-d H:i:s");
                                $comprador->ativo = true;
                                $comprador->nivel_acesso_id = 1;
                                $comprador->email = 'duplicado.' . $respCliente->body['email'];
                                $comprador->save(false);
                                $comprador_id = $comprador->id;
                            }
                        } else {
                            $empresa = new Empresa();
                            $empresa->nome = $respCliente->body['nome_fantasia'];
                            $empresa->razao = $respCliente->body['razao_social'];
                            $empresa->documento = $cpf_cnpj;
                            $empresa->juridica = false;
                            $empresa->grupo_id = 81;
                            $empresa->email = $respCliente->body['email'];
                            $empresa->save(false);

                            $endEmpresa = new EnderecoEmpresa();
                            $endEmpresa->empresa_id = $empresa->id;
                            $endEmpresa->logradouro = $respCliente->body['endereco'];
                            $endEmpresa->cep = $respCliente->body['cep'];
                            $endEmpresa->bairro = $respCliente->body['bairro'];
                            $endEmpresa->numero = $respCliente->body['endereco_numero'];
                            $endEmpresa->cidade_id = $respCliente->body['cidade_ibge'];
                            $endEmpresa->save(false);

                            $comprador = new Comprador();
                            $comprador->empresa_id = $empresa->id;
                            $comprador->cpf = $empresa->documento;
                            $comprador->nome = $respCliente->body['nome_fantasia'];
                            $comprador->dt_criacao = date("Y-m-d H:i:s");
                            $comprador->ativo = true;
                            $comprador->nivel_acesso_id = 1;
                            $comprador->save(false);
                        }
                    }

                    $pedido = new Pedido();
                    $pedido->valor_total = $respPedido->body["pedido_venda_produto"]["total_pedido"]['valor_mercadorias'];
                    $pedido->data_prevista = $respPedido->body["pedido_venda_produto"]["cabecalho"]['data_previsao'];
                    $pedido->comprador_id = $comprador_id;
                    $pedido->filial_id = $filial;

                    if (isset($respPedido->body["pedido_venda_produto"]["frete"]['codigo_transportadora'])) {
                        $pedido->transportadora_id = Transportadora::findOne(['codigo_omie' => $respPedido->body["pedido_venda_produto"]["frete"]['codigo_transportadora']])->id;
                    } else {
                        switch ($filial) {
                            case 93:
                                $pedido->transportadora_id = 1408;
                                break;
                            case 94:
                                $pedido->transportadora_id = 1406;
                                break;
                            case 95:
                                $pedido->transportadora_id = 1407;
                                break;
                            case 96:
                                $pedido->transportadora_id = 1405;
                                break;
                        }
                    }

                    if (isset($respPedido->body["pedido_venda_produto"]["frete"]['valor_frete'])) {
                        $pedido->valor_frete = $respPedido->body["pedido_venda_produto"]["frete"]['valor_frete'];
                    } else {
                        $pedido->valor_frete = 0.0;
                    }

                    $pedido->forma_pagamento_id = 2;
                    $pedido->administrador_id = Administrador::find()->where("codigo_omie_sp = '" . $respPedido->body['pedido_venda_produto']['informacoes_adicionais']['codVend'] . "' or 
                    codigo_omie_filial = '" . $respPedido->body['pedido_venda_produto']['informacoes_adicionais']['codVend'] . "' or 
                    codigo_omie_mg = '" . $respPedido->body['pedido_venda_produto']['informacoes_adicionais']['codVend'] . "' or 
                    codigo_omie_mg_vendas = '" . $respPedido->body['pedido_venda_produto']['informacoes_adicionais']['codVend'] . "'")->one()->id;
                    $pedido->tipo_frete = $respPedido->body["pedido_venda_produto"]["frete"]['modalidade'];
                    $pedido->conta_corrente_id = ContaCorrente::findOne(['codigo_conta_corrente_omie' => $respPedido->body['pedido_venda_produto']['informacoes_adicionais']['codigo_conta_corrente']])->id;
                    $pedido->e_pedido_autorizado = false;
                    $pedido->email_enderecos = "entregasp.pecaagora@gmail.com, compras.pecaagora@gmail.com";
                    $pedido->email_assunto = "{de} Pedido Interno {num_pedido} ({codigo_fabricante} * {quantidade} - {vendedor})";

                    $pedido->email_texto = "DESTACAR O ST RECOLHIDO ANTERIORMENTE EM INFORMAÇÕES ADICIONAIS E TAMBÉM NO XML DA NOTA, CASO CONTRÁRIO A MESMA SERÁ RECUSADA.

                    Cód.: {codigo}
                    Descrição: {descricao}
                    Quantidade: {quantidade}
                    Valor: R$ {valor}
                    Observação: {observacao}
                    NCM: {ncm}
                    PA{pa}
                    
                    Envio: Carmópolis de Minas, 963, Vila Maria.
                    
                    Atenciosamente,
                    Peça Agora
                    Site: https://www.pecaagora.com/
                    E-mail: compras.pecaagora@gmail.com Setor de Compras:(32)3015-0023 Whatsapp:(32)988354007
                    Skype: pecaagora";
                    $pedido->codigo_pedido_omie = $respPedido->body["pedido_venda_produto"]['cabecalho']['codigo_pedido'];
                    //echo "<pre>"; print_r($pedido); echo "</pre>"; die;
                    $pedido->save(false);

                    $modelStatus = new StatusPedido();
                    $modelStatus->data_referencia = date('Y-m-d');
                    $modelStatus->pedido_id = $pedido->id;
                    $modelStatus->tipo_status_id = 1;
                    $modelStatus->save(false);


                    foreach ($respPedido->body["pedido_venda_produto"]["det"] as $dadosProd) {

                        $pedidoProdutoFilial = new PedidoProdutoFilial();

                        $pedidoProdutoFilial->produto_filial_id = ProdutoFilial::findOne(['produto_id' => substr($dadosProd['produto']['codigo'], 2)])->id;
                        $pedidoProdutoFilial->pedido_id = $pedido->id;
                        $pedidoProdutoFilial->valor = $dadosProd['produto']['valor_unitario'];
                        $pedidoProdutoFilial->valor_cotacao = $dadosProd['produto']['valor_unitario'];
                        $pedidoProdutoFilial->quantidade = $dadosProd['produto']['quantidade'];
                        $pedidoProdutoFilial->save(false);
                    }
                }
            }
        }

        return $this->redirect('pedido-interno');
    }

    public function actionGestaoPedidos()
    {
        $query1 = (new \yii\db\Query())
            ->Select([
                'pedido.id',
                "concat('','Pedido/Interno') as tipo",
                "cast(administrador.nome as varchar) as vendedor",
                'pedido.dt_referencia as data',
                'pedido.valor_total',
                "cast(pedido.id as text) as num_pedido",
                '(CASE WHEN pedido.administrador_id is null THEN 96 ELSE pedido.filial_id END) as filial',
                'estado.nome as estado',
                'comprador.nome',
                'empresa.documento',
                'transportadora.nome as transportadora',
                'pedido.valor_frete',
                'pedido.tipo_frete',
                'nota_fiscal.numero_nf',
                'nota_fiscal.id as id_nf',
                'pedido.e_verificado'
            ])
            ->from("pedido")
            ->innerJoin("comprador", "comprador.id = pedido.comprador_id")
            ->innerJoin("empresa", "empresa.id = comprador.empresa_id")
            ->innerJoin("endereco_empresa", "endereco_empresa.empresa_id = empresa.id")
            ->innerJoin("cidade", "cidade.id = endereco_empresa.cidade_id")
            ->innerJoin("estado", "estado.id = cidade.estado_id")
            ->innerJoin("transportadora", "transportadora.id = pedido.transportadora_id")
            ->leftJoin("nota_fiscal", "nota_fiscal.id_pedido = pedido.codigo_pedido_omie")
            ->leftJoin("administrador", "administrador.id = pedido.administrador_id")
            ->orderBy(['pedido.id' => SORT_DESC]);

        $query2 = (new \yii\db\Query())
            ->Select([
                'pedido_mercado_livre.id',
                "concat('','Pedido ML') as tipo",
                "cast(concat('','ML') as varchar) as vendedor",
                'pedido_mercado_livre.date_created as data',

                '(select
                total_amount + shipping_option_cost - (
                select
                    sum(sale_fee * quantity)
                from
                    pedido_mercado_livre_produto
                where
                    pedido_mercado_livre_produto.pedido_mercado_livre_id = pedido_mercado_livre.id) - shipping_option_list_cost
            from
                pedido_mercado_livre
                where
		pedido_mercado_livre_produto.pedido_mercado_livre_id = pedido_mercado_livre.id) as valor_total',
                "cast(pedido_mercado_livre.pedido_meli_id as text) as num_pedido",
                "(CASE WHEN pedido_mercado_livre.user_id = '193724256' THEN 96 
                WHEN pedido_mercado_livre.user_id = '14353430671' THEN 95 ELSE 94 END) as filial",
                'pedido_mercado_livre.receiver_state_name as estado',
                "concat(pedido_mercado_livre.buyer_first_name, ' ', pedido_mercado_livre.buyer_last_name) as nome",
                "pedido_mercado_livre.buyer_doc_number as documento",
                'pedido_mercado_livre.shipping_tracking_method as transportadora',
                'pedido_mercado_livre.shipping_option_cost as valor_frete',
                "cast(concat('',0) as integer) as tipo_frete",
                'nota_fiscal.numero_nf',
                'nota_fiscal.id as id_nf',
                'pedido_mercado_livre.e_verificado'
            ])
            ->from("pedido_mercado_livre")
            ->leftJoin("nota_fiscal", "nota_fiscal.id_pedido = pedido_mercado_livre.codigo_pedido_omie")
            ->innerJoin("pedido_mercado_livre_produto", "pedido_mercado_livre_produto.pedido_mercado_livre_id = pedido_mercado_livre.id")
            ->orderBy(['pedido_mercado_livre.id' => SORT_DESC]);

        $unionQuery = (new \yii\db\Query())
            ->from(['t1' => $query1->union($query2, true)])
            ->orderBy(['t1.data' => SORT_DESC]);

        // echo $unionQuery->createCommand()->sql; die;

        $provider = new ActiveDataProvider([
            'query' => $unionQuery,
            'pagination' => [
                'pageSize' => 20,
                'totalCount' => 100,
            ],
        ]);

        return $this->render('gestao-pedidos', [
            'dataProvider'    => $provider,
        ]);
    }

    public static function actionGestaoItensPedidos($id)
    {
        $query1 = (new \yii\db\Query())
            ->Select([
                "concat('PA', produto.id) as pa",
                'produto.nome as nome_produto',
                'produto.codigo_global as cod_global',
                'coalesce(pedido_produto_filial_cotacao.quantidade, pedido_produto_filial.quantidade) as quantidade',
                'coalesce(pedido_produto_filial_cotacao.valor, pedido_produto_filial.valor_cotacao) as valor_cotacao',
                'pedido_produto_filial.valor as valor_venda',
                'filial.nome as filial_nome',
                'produto_filial.estoque_minimo',
                '(select quantidade from produto_filial where filial_id = 96 and produto.id = produto_id) as estoque_sp',
                '(select quantidade from produto_filial where filial_id = 94 and produto.id = produto_id) as estoque_mg',
                'nota_fiscal.id as id_nf'
            ])
            ->from("pedido")
            ->innerJoin("pedido_produto_filial", "pedido_produto_filial.pedido_id = pedido.id")
            ->leftJoin("pedido_produto_filial_cotacao", "pedido_produto_filial_cotacao.pedido_produto_filial_id = pedido_produto_filial.id")
            ->leftJoin("nota_fiscal", "nota_fiscal.id_pedido = pedido.codigo_pedido_omie")
            ->innerJoin("produto_filial", "produto_filial.id = 
            (case
                when (pedido_produto_filial_cotacao.produto_filial_id is not null) then pedido_produto_filial_cotacao.produto_filial_id
                else (pedido_produto_filial.produto_filial_id)
            end)")
            ->innerJoin("produto", "produto.id = produto_filial.produto_id")
            ->innerJoin("filial", "filial.id = produto_filial.filial_id")
            ->orderBy(['pedido.id' => SORT_DESC])
            ->where("pedido.id = $id");

        $query2 = (new \yii\db\Query())
            ->Select([
                "concat('PA', produto.id) as pa",
                'produto.nome as nome_produto',
                'produto.codigo_global as cod_global',
                'coalesce(pedido_mercado_livre_produto_produto_filial.quantidade, pedido_mercado_livre_produto.quantity) as quantidade',
                "coalesce(pedido_mercado_livre_produto_produto_filial.valor, 1) as valor_cotacao",
                'pedido_mercado_livre_produto.unit_price as valor_venda',
                'filial.nome as filial_nome',
                'produto_filial.estoque_minimo',
                '(select quantidade from produto_filial where filial_id = 96 and produto.id = produto_id) as estoque_sp',
                '(select quantidade from produto_filial where filial_id = 94 and produto.id = produto_id) as estoque_mg',
                'nota_fiscal.id as id_nf'
            ])
            ->from("pedido_mercado_livre")
            ->innerJoin("pedido_mercado_livre_produto", "pedido_mercado_livre_produto.pedido_mercado_livre_id = pedido_mercado_livre.id")
            ->leftJoin("pedido_mercado_livre_produto_produto_filial", "pedido_mercado_livre_produto_produto_filial.pedido_mercado_livre_produto_id = pedido_mercado_livre_produto.id")
            ->leftJoin("nota_fiscal", "nota_fiscal.id_pedido = pedido_mercado_livre.codigo_pedido_omie")
            ->innerJoin("produto_filial", "produto_filial.id = 
            (case
                when (pedido_mercado_livre_produto_produto_filial.produto_filial_id is not null) then pedido_mercado_livre_produto_produto_filial.produto_filial_id
                else (pedido_mercado_livre_produto.produto_filial_id)
            end)")
            ->innerJoin("produto", "produto.id = produto_filial.produto_id")
            ->innerJoin("filial", "filial.id = produto_filial.filial_id")
            ->orderBy(['pedido_mercado_livre.id' => SORT_DESC])
            ->where("pedido_mercado_livre.pedido_meli_id = '$id'");

        $unionQuery = (new \yii\db\Query())
            ->from(['t1' => $query1->union($query2, true)]);

        $provider = new ActiveDataProvider([
            'query' => $unionQuery,
            'pagination' => [
                'pageSize' => 20,
                'totalCount' => 100,
            ],
        ]);

        return $provider;
    }

    public function actionVerificarPedido()
    {
        if (isset($_POST['keylist'])) {
            foreach ($_POST['keylist'] as $key => $value) {
                $modelPedido = Pedido::findOne($value);
                if ($modelPedido) {
                    if ($modelPedido->e_verificado) {
                        $modelPedido->e_verificado = false;
                    } else {
                        $modelPedido->e_verificado = true;
                    }
                    $modelPedido->save();
                } else {
                    $modelPedido = PedidoMercadoLivre::findOne($value);
                    if ($modelPedido->e_verificado) {
                        $modelPedido->e_verificado = false;
                    } else {
                        $modelPedido->e_verificado = true;
                    }
                    $modelPedido->save();
                }
            }
        }
        $this->redirect(['gestao-pedidos']);
    }
}
