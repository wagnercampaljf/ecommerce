<?php

namespace backend\controllers;

use Yii;
use backend\models\NotaFiscal;
use backend\models\NotaFiscalPedidoProduto;
use backend\models\NotaFiscalSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use backend\models\NotaFiscalProduto;
use console\controllers\actions\omie\Omie;
use yii\helpers\Json;
use backend\models\NotaFiscalProdutoSearch;
use backend\models\PedidoCompra;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

/**
 * NotaFiscalController implements the CRUD actions for NotaFiscal model.
 */
class NotaFiscalController extends Controller
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
     * Lists all NotaFiscal models.
     * @return mixed
     */

    public function actionIndex()
    {
        $searchModel = new NotaFiscalSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        if (Yii::$app->request->post('hasEditable')) {

            $nota_fiscal_produto_id = Yii::$app->request->post('editableKey');
            $model = NotaFiscalProduto::findOne($nota_fiscal_produto_id);

            $out = Json::encode(['output' => '', 'message' => '']);

            $posted = current($_POST['NotaFiscalProduto']);
            $post = ['NotaFiscalProduto' => $posted];

            if ($model->load($post)) {
                $model->save();
                $output = '';
                if (isset($posted['valor_real_produto'])) {
                    $output = Yii::$app->formatter->asDecimal($model->valor_real_produto, 2);
                }

                $out = Json::encode(['output' => $output, 'message' => '']);
            }
            echo $out;
            return;
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'tela' => ''
        ]);
    }

    public function actionNotasValidadas()
    {
        $searchModel = new NotaFiscalSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(), 'true');

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'tela' => 'tela_validada'
        ]);
    }

    public function actionIndexNotaPedido($filtro = null)
    {

        if ($filtro) {
            $query1 = (new \yii\db\Query())
                ->Select([
                    'nota_fiscal_pedido_produto.id',
                    'nota_fiscal.numero_nf',
                    'nota_fiscal.e_validada',
                    'pedido_produto_filial.pedido_id',
                    'pedido.dt_referencia as data_pedido',
                    "concat('','Pedido/Interno') as tipo",
                    "comprador.nome as nome",
                    "concat('PA', produto.id) as pa",
                    'produto.codigo_global as cod_global',
                    'produto.codigo_fabricante',
                    'produto.nome as nome_produto',
                    'produto.codigo_global',
                    'filial.nome as nome_filial',
                    'pedido_produto_filial_cotacao.valor',
                    'pedido_produto_filial_cotacao.quantidade',
                    'nota_fiscal_pedido_produto.e_validado'
                ])
                ->from("pedido_produto_filial_cotacao")
                ->innerJoin("nota_fiscal_pedido_produto", "nota_fiscal_pedido_produto.pedido_produto_filial_cotacao_id = pedido_produto_filial_cotacao.id")
                ->innerJoin("pedido_produto_filial", "pedido_produto_filial.id = pedido_produto_filial_cotacao.pedido_produto_filial_id")
                ->innerJoin("pedido", "pedido.id = pedido_produto_filial.pedido_id")
                ->innerJoin("comprador", "comprador.id = pedido.comprador_id")
                ->innerJoin("produto_filial", "produto_filial.id = pedido_produto_filial_cotacao.produto_filial_id")
                ->innerJoin("produto", "produto.id = produto_filial.produto_id")
                ->innerJoin("filial", "filial.id = produto_filial.filial_id")
                ->innerJoin("nota_fiscal_produto", 'nota_fiscal_produto.id = nota_fiscal_pedido_produto.nota_fiscal_produto_id')
                ->innerJoin("nota_fiscal", 'nota_fiscal.id = nota_fiscal_produto.nota_fiscal_id');

            $query2 = (new \yii\db\Query())
                ->Select(
                    [
                        'nota_fiscal_pedido_produto.id',
                        'nota_fiscal.numero_nf',
                        'nota_fiscal.e_validada',
                        'pedido_compra_produto_filial.pedido_compra_id as pedido_id',
                        'pedido_compra.data as data_pedido',
                        "concat('','Pedido Estoque') as tipo",
                        "pedido_compra.descricao as nome",
                        "concat('PA', produto.id) as pa",
                        'produto.codigo_global as cod_global',
                        'produto.codigo_fabricante',
                        'produto.nome as nome_produto',
                        'produto.codigo_global',
                        'filial.nome as nome_filial',
                        'pedido_compra_produto_filial.valor_compra as valor',
                        'pedido_compra_produto_filial.quantidade as quantidade',
                        'nota_fiscal_pedido_produto.e_validado'
                    ]
                )
                ->from("pedido_compra_produto_filial")
                ->innerJoin('pedido_compra', 'pedido_compra.id = pedido_compra_produto_filial.pedido_compra_id')
                ->innerJoin("nota_fiscal_pedido_produto", "nota_fiscal_pedido_produto.pedido_compras_produto_filial_id = pedido_compra_produto_filial.id")
                ->innerJoin("produto_filial", "produto_filial.id = pedido_compra_produto_filial.produto_filial_id")
                ->innerJoin("produto", "produto.id = produto_filial.produto_id")
                ->innerJoin("filial", "filial.id = produto_filial.filial_id")
                ->innerJoin("nota_fiscal_produto", 'nota_fiscal_produto.id = nota_fiscal_pedido_produto.nota_fiscal_produto_id')
                ->innerJoin("nota_fiscal", 'nota_fiscal.id = nota_fiscal_produto.nota_fiscal_id');

            $query3 = (new \yii\db\Query())
                ->Select(
                    [
                        'nota_fiscal_pedido_produto.id',
                        'nota_fiscal.numero_nf',
                        'nota_fiscal.e_validada',
                        'pedido_mercado_livre_produto.pedido_mercado_livre_id as pedido_id',
                        'pedido_mercado_livre.date_created as data_pedido',
                        "concat('','Pedido ML') as tipo",
                        "concat(pedido_mercado_livre.buyer_first_name || ' ' || pedido_mercado_livre.buyer_last_name) as nome",
                        "concat('PA', produto.id) as pa",
                        'produto.codigo_global as cod_global',
                        'produto.codigo_fabricante',
                        'produto.nome as nome_produto',
                        'produto.codigo_global',
                        'filial.nome as nome_filial',
                        'pedido_mercado_livre_produto_produto_filial.valor as valor',
                        'pedido_mercado_livre_produto_produto_filial.quantidade as quantidade',
                        'nota_fiscal_pedido_produto.e_validado'
                    ]
                )
                ->from("pedido_mercado_livre_produto_produto_filial")
                ->innerJoin("pedido_mercado_livre_produto", "pedido_mercado_livre_produto_produto_filial.pedido_mercado_livre_produto_id = pedido_mercado_livre_produto.id")
                ->innerJoin("pedido_mercado_livre", "pedido_mercado_livre.id = pedido_mercado_livre_produto.pedido_mercado_livre_id")
                ->innerJoin("nota_fiscal_pedido_produto", "nota_fiscal_pedido_produto.pedido_mercado_livre_produto_produto_filial_id = pedido_mercado_livre_produto_produto_filial.id")
                ->innerJoin("produto_filial", "produto_filial.id = pedido_mercado_livre_produto_produto_filial.produto_filial_id")
                ->innerJoin("produto", "produto.id = produto_filial.produto_id")
                ->innerJoin("filial", "filial.id = produto_filial.filial_id")
                ->innerJoin("nota_fiscal_produto", 'nota_fiscal_produto.id = nota_fiscal_pedido_produto.nota_fiscal_produto_id')
                ->innerJoin("nota_fiscal", 'nota_fiscal.id = nota_fiscal_produto.nota_fiscal_id');

            $unionQuery = (new \yii\db\Query())
                ->from(['t1' => $query1->union($query2->union($query3, true), true)])
                ->where("t1.e_validado = true and t1.e_validada = true and t1.numero_nf = $filtro");

            $provider = new ActiveDataProvider([
                'query' => $unionQuery,
                'pagination' => [
                    'pageSize' => 20,
                    'totalCount' => 100,
                ],
            ]);

            return $this->render('_notas_pedido', [
                'dataProvider' => $provider,
            ]);
        }

        return $this->render('_notas_pedido');
    }

    public function actionSearch($filtro, $validada)
    {
        $query = null;
        $tela = '';

        if ($validada == 0) {
            $query = NotaFiscal::find()->where('finalidade_emissao <> 4 and (cod_cliente <> 2641483458 and cod_cliente <> 1018587858 or cod_cliente is null) and tipo_nf = 0 and e_validada = false');
        } else {
            $tela = 'tela_validada';
            $query = NotaFiscal::find()->where('finalidade_emissao <> 4 and (cod_cliente <> 2641483458 and cod_cliente <> 1018587858 or cod_cliente is null) and tipo_nf = 0 and e_validada = true');
        }

        $filtro = str_replace(' ', '', $filtro);

        $query->andWhere("numero_nf = $filtro or chave_nf = '$filtro'");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['data_nf' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'tela' => $tela,
        ]);
    }

    public function actionSearchExpedicao($filtro)
    {
        $query = NotaFiscal::find()->where('nota_fiscal.e_validada = true')
            ->andWhere("numero_nf = $filtro");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['data_nf' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('_notas_pedido', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NotaFiscal model.
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
     * Creates a new NotaFiscal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new NotaFiscal();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing NotaFiscal model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing NotaFiscal model.
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
     * Finds the NotaFiscal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NotaFiscal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NotaFiscal::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionValidarNota()
    {
        if (isset($_POST['keylist'])) {
            $count = count($_POST['keylist']);
            $idNota = 0;
            foreach ($_POST['keylist'] as $key => $value) {
                $idNota = $value;
                $model = NotaFiscal::findOne($value);
                $model->e_validada = true;
                $model->save(false);
            }
            if ($count == 1) {
                return $this->redirect(['update', 'id' => $idNota]);
            }
        }
        $this->redirect(['index']);
    }

    public function actionReverterNota()
    {
        if (isset($_POST['keylist'])) {
            foreach ($_POST['keylist'] as $id_nota) {
                $model = NotaFiscal::findOne($id_nota);
                $model->e_validada = false;
                $model->save(false);
                $modelProduto = NotaFiscalProduto::findAll(['nota_fiscal_id' => $model->id]);
                foreach ($modelProduto as $produto) {
                    $modelPedidoProduto = NotaFiscalPedidoProduto::findAll(['nota_fiscal_produto_id' => $produto->id]);
                    foreach ($modelPedidoProduto as $pedidoProduto) {
                        $pedido_produto = NotaFiscalPedidoProduto::findOne($pedidoProduto->id);
                        $pedido_produto->e_validado = false;
                        $pedido_produto->nota_fiscal_produto_id = null;
                        $pedido_produto->save(false);
                    }
                }
            }
        }
        $this->redirect(['notas-validadas']);
    }

    public function actionBaixarNota($cChaveNFe)
    {
        $acessoOmie = [
            '468080198586'  => '7b3fb2b3bae35eca3b051b825b6d9f43',
            '469728530271'  => '6b63421c9bb3a124e012a6bb75ef4ace',
            '1017311982687' => '78ba33370fac6178da52d42240591291',
            '1758907907757' => '0a69c9b49e5a188e5f43d5505f2752bc'
        ];

        $omie = new Omie(1, 1);

        $cChaveNFe = str_replace(' ', '', $cChaveNFe);

        foreach ($acessoOmie as $key => $value) {
            $APP_KEY_OMIE            = $key;
            $APP_SECRET_OMIE         = $value;

            $body = [
                "call" => "ConsultarNF",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "cChaveNFe" => $cChaveNFe,
                ]
            ];

            $response = $omie->consulta("/api/v1/produtos/nfconsultar/?JSON=", $body);

            $nota_fiscal = null;
            $produto_nf = null;
            $perc_nota = 100;

            if (isset($response['body']['compl'])) {

                $nota_fiscal = NotaFiscal::findOne(['chave_nf' => $response['body']['compl']['cChaveNFe']]);

                if ($nota_fiscal == null) {
                    $nota_fiscal = new NotaFiscal();
                } else {
                    continue;
                }

                $nota_fiscal->chave_nf = $response['body']['compl']['cChaveNFe'];
                $nota_fiscal->id_nf = $response['body']['compl']["nIdNF"];
                $nota_fiscal->id_pedido = $response['body']['compl']["nIdPedido"];
                $nota_fiscal->id_recebimento = $response['body']['compl']['nIdReceb'];
                $nota_fiscal->id_transportadora = $response['body']['compl']['nIdTransp'];
                $nota_fiscal->modo_frete = $response['body']['compl']['cModFrete'];
                $nota_fiscal->valor_nf = $response['body']['total']['ICMSTot']['vNF'];
                $nota_fiscal->data_nf = $response['body']['ide']['dEmi'];
                $nota_fiscal->data_cancelamento = $response['body']['ide']['dCan'];
                $nota_fiscal->data_emissao = $response['body']['ide']['dEmi'];
                $nota_fiscal->data_inutilizacao = $response['body']['ide']['dInut'];
                $nota_fiscal->data_registro = $response['body']['ide']['dReg'];
                $nota_fiscal->data_saida = $response['body']['ide']['dSaiEnt'];
                $nota_fiscal->finalidade_emissao = $response['body']['ide']['finNFe'];
                $nota_fiscal->tipo_nf = $response['body']['ide']['tpNF'];
                $nota_fiscal->tipo_ambiente = $response['body']['ide']['tpAmb'];
                $nota_fiscal->serie = $response['body']['ide']['serie'];
                $nota_fiscal->codigo_modelo = $response['body']['ide']['mod'];
                $nota_fiscal->indice_pagamento = $response['body']['ide']['indPag'];
                $nota_fiscal->h_saida_entrada_nf = $response['body']['ide']['hSaiEnt'];
                $nota_fiscal->h_emissao = $response['body']['ide']['hEmi'];
                $nota_fiscal->cod_int_empresa = $response['body']['nfEmitInt']['cCodEmpInt'];
                $nota_fiscal->cod_empresa = $response['body']['nfEmitInt']['nCodEmp'];
                $nota_fiscal->cod_int_cliente_fornecedor = $response['body']['nfDestInt']['cCodCliInt'];
                $nota_fiscal->cod_cliente = $response['body']['nfDestInt']['nCodCli'];
                $nota_fiscal->numero_nf = $response['body']["ide"]["nNF"];
                $nota_fiscal->e_validada = false;

                $body = [
                    "call" => "ConsultarCliente",
                    "app_key" => $APP_KEY_OMIE,
                    "app_secret" => $APP_SECRET_OMIE,
                    "param" => [
                        "codigo_cliente_omie" => $nota_fiscal->cod_cliente,
                    ]
                ];
                $responseOmie = $omie->consulta("/api/v1/geral/clientes/?JSON=", $body);

                if ($responseOmie['body']) {
                    $nota_fiscal->fornecedor = $responseOmie['body']['razao_social'];
                }

                if ($nota_fiscal->save(false)) {
                }

                switch ($nota_fiscal->fornecedor) {
                    case 'KARTER LUBRIFICANTES LTDA':
                        $perc_nota = 70;
                        break;
                    case 'JGE PRODUTOS REFRIGERADOS EIRELI - ME':
                        $perc_nota = 50;
                        break;
                    case 'ASSIS FIBRAS INDUSTRIA E COMERCIO LTDA':
                    case 'LEFS COMERCIO IMPORTACAO E EXPORTACAO DE AUTOPECAS LTDA':
                    case 'GENERAL TRUCK DISTRIBUIDORA DE AUTO PECAS LTDA - EPP':
                        $perc_nota = 30;
                        break;
                    case 'RODOPLAST INDUSTRIA E COMERCIO DE COMPONENTES PLASTICOS LTDA':
                        if ($nota_fiscal->cod_cliente == '2643204227') {
                            $perc_nota = 25;
                        }
                        break;
                    case 'HARDEN COMERCIO DE EXPORTACAO E IMPORTACAO LTDA ME':
                        $perc_nota = 25.77;
                        break;
                }

                foreach ($response['body']['det'] as $produto) {

                    $produto_nf = new NotaFiscalProduto();

                    $produto_nf->nota_fiscal_id = $nota_fiscal->id;
                    $produto_nf->descricao = $produto["prod"]["xProd"];
                    $produto_nf->valor_produto = $produto["prod"]["vProd"];
                    $produto_nf->codigo_produto = $produto["prod"]["cProd"];
                    $produto_nf->cod_int_item = $produto["nfProdInt"]["cCodItemInt"];
                    $produto_nf->cod_int_produto = $produto["nfProdInt"]["cCodProdInt"];
                    $produto_nf->cod_item = $produto["nfProdInt"]["nCodItem"];
                    $produto_nf->cod_produto = $produto["nfProdInt"]["nCodProd"];
                    $produto_nf->cod_fiscal_operacao_servico = $produto["prod"]["CFOP"];
                    $produto_nf->cod_situacao_tributaria_icms = $produto["prod"]["EXTIPI"];
                    $produto_nf->cod_ncm = $produto["prod"]["NCM"];
                    $produto_nf->ean = $produto["prod"]["cEAN"];

                    if (isset($produto["prod"]["cEANTrib"])) {
                        $produto_nf->ean_tributável = $produto["prod"]["cEANTrib"];
                    }
                    $produto_nf->codigo_produto_original = $produto["prod"]["cProdOrig"];
                    $produto_nf->codigo_local_estoque = $produto["prod"]["codigo_local_estoque"];
                    $produto_nf->cmc_total = $produto["prod"]["nCMCTotal"];
                    $produto_nf->cmc_unitario = $produto["prod"]["nCMCUnitario"];

                    if (isset($produto["prod"]["pICMS"])) {
                        $produto_nf->aliquota_icms = $produto["prod"]["pICMS"];
                    }

                    $produto_nf->qtd_comercial = $produto["prod"]["qCom"];
                    $produto_nf->qtd_tributavel = $produto["prod"]["qTrib"];
                    $produto_nf->unid_tributavel = $produto["prod"]["uCom"];
                    $produto_nf->valor_desconto = $produto["prod"]["vDesc"];
                    $produto_nf->valor_total_frete = $produto["prod"]["vFrete"];
                    if (isset($produto["prod"]["vIPI"])) {
                        $produto_nf->valor_ipi = $produto["prod"]["vIPI"];
                    } else {
                        $produto_nf->valor_ipi = 0;
                    }

                    if (isset($produto["prod"]["vICMSST"])) {
                        $produto_nf->valor_icms = $produto["prod"]["vICMSST"];
                    } else {
                        $produto_nf->valor_icms = 0;
                    }

                    $produto_nf->outras_despesas = $produto["prod"]["vOutro"];
                    $produto_nf->valor_seguro = $produto["prod"]["vSeg"];
                    $produto_nf->valor_unitario_tributacao = $produto["prod"]["vUnCom"];
                    $produto_nf->descricao_original = $produto["prod"]["xProdOrig"];

                    if ($nota_fiscal->fornecedor == 'ASSIS FIBRAS INDUSTRIA E COMERCIO LTDA') {

                        $add_assis = $produto_nf->valor_unitario_tributacao * 0.08;
                        $produto_nf->valor_real_produto = (($produto_nf->valor_unitario_tributacao * 100) / $perc_nota) + $add_assis;
                    } else {
                        $imposto = ($produto_nf->valor_icms + $produto_nf->valor_ipi + $produto_nf->valor_seguro + $produto_nf->valor_total_frete + $produto_nf->outras_despesas) - $produto_nf->valor_desconto;
                        $imposto = $imposto / $produto_nf->qtd_comercial;
                        $produto_nf->valor_real_produto = ($produto_nf->valor_unitario_tributacao * 100) / $perc_nota + $imposto;
                    }

                    $produtoPA = [
                        "call" => "ConsultarProduto",
                        "app_key" => $APP_KEY_OMIE,
                        "app_secret" => $APP_SECRET_OMIE,
                        "param" => [
                            "codigo_produto" => $produto_nf->cod_produto
                        ]
                    ];

                    $respOmie = $omie->consulta("/api/v1/geral/produtos/?JSON=", $produtoPA);
                    $resp = (object) $respOmie;

                    if ($resp->body) {
                        if (isset($resp->body["codigo_produto_integracao"])) {
                            $produto_nf->pa_produto = $resp->body["codigo_produto_integracao"];
                        }
                    }

                    if ($produto_nf->save(false)) {
                        echo $produto_nf->descricao . ' PA ' . $produto_nf->pa_produto . "\n";
                    }

                    if ($nota_fiscal->finalidade_emissao !== 4 && $nota_fiscal->tipo_nf == 0) {

                        $produto_id = str_replace('PA', '', $produto_nf->pa_produto);
                        $produto_bloqueado = Produto::findOne($produto_id)->e_valor_bloqueado;

                        if ($produto_bloqueado !== true) {

                            $filial = 0;

                            switch ($key) {
                                case '468080198586':
                                    $filial = 96;
                                    break;
                                case '1017311982687':
                                    $filial = 95;
                                    break;
                                case '469728530271':
                                    $filial = 94;
                                    break;
                                case '1758907907757':
                                    $filial = 93;
                                    break;
                            }


                            $produto_filial = ProdutoFilial::find()->where("produto_id = $produto_id and filial_id = $filial")->one();

                            if (empty($produto_filial)) {
                                $produto_filial = new ProdutoFilial();
                                $produto_filial->produto_id = $produto_id;
                                $produto_filial->filial_id  = $filial;
                                $produto_filial->quantidade = $produto_nf->qtd_comercial;
                                $produto_filial->envio      = 1;
                                $produto_filial->save();
                            }

                            $markup = Yii::$app->db->createCommand("select margem from markup_detalhe md 
                                inner join markup_mestre mm on md.markup_mestre_id = mm.id 
                                where ($produto_nf->valor_real_produto ::float between valor_minimo and valor_maximo) and mm.e_markup_padrao = true
                                order by mm.id desc 
                                limit 1")->queryScalar();

                            if ($filial == 96 && $nota_fiscal->fornecedor == 'ZAPPAROLI IND E COM DE PLASTICOS LTDA') {
                                $markup = 1.85;
                            }

                            if ($nota_fiscal->fornecedor == 'KARTER LUBRIFICANTES LTDA' || $nota_fiscal->fornecedor == 'BRIDA LUBRIFICANTES LTDA') {
                                $markup = 1.82;
                            }

                            $modelValorProdutoFilial = new ValorProdutoFilial();
                            $modelValorProdutoFilial->valor = $markup < 5 ? $produto_nf->valor_real_produto * $markup : $markup;
                            $modelValorProdutoFilial->dt_inicio = date("Y-m-d H:i:s");
                            $modelValorProdutoFilial->produto_filial_id = $produto_filial->id;
                            $modelValorProdutoFilial->promocao = false;
                            $modelValorProdutoFilial->valor_compra = $produto_nf->valor_real_produto;
                            if (!$modelValorProdutoFilial->save()) {
                                echo 'erro';
                                die;
                            }
                            ValorProdutoFilialController::AtualizarValorProdutoFilial($modelValorProdutoFilial);
                        }
                    }
                }

                if ($nota_fiscal->finalidade_emissao != 4 && $nota_fiscal->tipo_nf == 0) {

                    if (
                        $nota_fiscal->fornecedor !== 'BR COMPANY' &&
                        $nota_fiscal->fornecedor !== 'MORELATE DISTR DE AUTO PECAS LTDA' &&
                        $nota_fiscal->fornecedor !== 'VANNUCCI IMPORTACAO, EXPORTACAO E COM. PECAS LTDA' &&
                        $nota_fiscal->fornecedor !== 'MSAM Distribuidora de Pecas Ltda' &&
                        $nota_fiscal->fornecedor !== 'ANCHIETA PECAS DISTR DE PCS P CAM E ONIBUS EIRELI'
                    ) {
                        NotaFiscal::EmailNotaFiscalEntrada($nota_fiscal->id);
                    }

                    switch ($nota_fiscal->fornecedor) {
                        case 'KARTER LUBRIFICANTES LTDA':
                        case 'ASSIS FIBRAS INDUSTRIA E COMERCIO LTDA':
                        case 'LEFS COMERCIO IMPORTACAO E EXPORTACAO DE AUTOPECAS LTDA':
                        case 'HARDEN COMERCIO DE EXPORTACAO E IMPORTACAO LTDA ME':
                        case 'LOTTO AUTOMOTIVE COMERCIO DE AUTO PECAS LTDA':
                        case 'ZAPPAROLI IND E COM DE PLASTICOS LTDA':
                        case 'ROMANAPLAST INDUSTRIA E COMERCIO EIRELI':
                        case 'F.CONFUORTO IND. COM. PECAS ACES. LTDA':
                        case 'VERLI INDUSTRIA MECANICA LTDA':
                        case 'Globo Ind. e Com. de Pecas Ltda':
                        case 'EDVALDO JOSE DOS SANTOS TAPECARIA ME':
                        case 'Mlc Industria e Comercio de Acess Para Automoveis Eireli Epp':
                        case 'FRISART IND. E COM. DE ACESSORIOS P/ AUTOS EIRELI':
                        case 'RODOPLAST INDUSTRIA E COMERCIO DE COMPONENTES PLASTICOS LTDA':
                        case 'KM BRASIL AUTO PECAS - EIRELI':
                        case 'IRMAOS AMALCABURIO LTDA':
                        case 'Fibracel pecas para veiculos Ltda':
                        case 'CIPEC INDUSTRIAL DE AUTOPECAS LTDA':
                        case 'BL Fibras Ltda':
                        case 'SPG COMPONENTES AUTOMOTIVOS LTDA':
                        case 'FLEX AUTOMOTIVA LTDA':
                        case 'GENERAL TRUCK DISTRIBUIDORA DE AUTO PECAS LTDA - EPP':
                        case 'GENERAL TRUCK PR DISTRIBUIDORA DE AUTOPECAS LTDA':
                        case 'BRIDA LUBRIFICANTES LTDA':
                        case 'BZ AUTOMOTIVE LTDA':
                            PedidoCompra::CriarPedidoCompras($nota_fiscal->chave_nf, $perc_nota);
                            break;
                    }
                }
            } else {
                echo "não existe dados da nota";
            }
        }

        return $this->redirect("index");
    }

    public function actionReceberXml()
    {
        $_UP['erros'][0] = 'Não houve erro';
        $_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
        $_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
        $_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
        $_UP['erros'][4] = 'Não foi feito o upload do arquivo';

        if ($_FILES['arquivo']['error'] != 0) {

            $searchModel = new NotaFiscalSearch;
            $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'erro' => "Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['arquivo']['error']],
                'tela' => ''
            ]);
        } else {

            if (isset($_POST['enviar-formulario'])) {
                $formatosPermitidos = array("xml", "XML");
                $extensao = pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION);
                if (in_array($extensao, $formatosPermitidos)) {
                    $pasta = "arquivos\\";
                    $temporario = $_FILES['arquivo']['tmp_name'];
                    $novoNome = uniqid() . ".$extensao";
                    if (move_uploaded_file($temporario, $pasta . $novoNome)) {
                        $mensagem = "Upload feito com sucesso";
                    } else {
                        $mensagem = "Erro, não foi possível fazer o upload";
                    }
                } else {
                    $mensagem = "Formato Inválido";
                }
            }

            $nota = new NotaFiscal();
            $xml = simplexml_load_file($pasta . $novoNome);

            if (empty($xml->protNFe->infProt->nProt)) {

                $searchModel = new NotaFiscalSearch;
                $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
                return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                    'erro' => "Arquivo sem dados de autorização!",
                    'tela' => ''

                ]);
            }

            $nota->chave_nf = $xml->NFe->infNFe->attributes()->Id;
            $nota->chave_nf = strtr(strtoupper($nota->chave_nf), array("NFE" => NULL));
            $nota_fiscal = NotaFiscal::findOne(['chave_nf' => $nota->chave_nf]);
            if ($nota_fiscal) {
                $searchModel = new NotaFiscalSearch;
                $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
                return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                    'erro' => "Nota já se encontra no sistema!",
                    'tela' => ''
                ]);
            }

            // <ide>
            $nota->indice_pagamento = (int)$xml->NFe->infNFe->ide->indPag;
            $nota->codigo_modelo = (int)$xml->NFe->infNFe->ide->mod;
            $nota->serie = (int)$xml->NFe->infNFe->ide->serie;
            $nota->numero_nf =  (int)$xml->NFe->infNFe->ide->nNF;
            $dhEmi = $xml->NFe->infNFe->ide->dhEmi;
            $dhEmi = explode('-', substr($dhEmi, 0, 10));
            $nota->data_emissao = $dhEmi[2] . "/" . $dhEmi[1] . "/" . $dhEmi[0];
            $dhSaiEnt = $xml->NFe->infNFe->ide->dhSaiEnt;
            $dhSaiEnt = explode('-', substr($dhSaiEnt, 0, 10));
            $nota->data_saida = $dhSaiEnt[2] . "/" . $dhSaiEnt[1] . "/" . $dhSaiEnt[0];
            $nota->tipo_nf = 0;
            $nota->tipo_ambiente = (int)$xml->NFe->infNFe->ide->tpAmb;
            $nota->valor_nf = $xml->NFe->infNFe->total->ICMSTot->vNF;
            $nota->valor_nf = number_format((float) $nota->valor_nf, 2, ".", "");
            $nota->data_nf = $nota->data_emissao;
            $nota->fornecedor = (string)$xml->NFe->infNFe->emit->xFant;

            if ($nota->tipo_ambiente != 1) {
                $searchModel = new NotaFiscalSearch;
                $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
                return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                    'erro' => "Documento emitido em ambiente de homologação!",
                    'tela' => ''
                ]);
            }
            $nota->finalidade_emissao = (int)$xml->NFe->infNFe->ide->finNFe;
            $nota->save();

            $i = 0;

            while (isset($xml->NFe->infNFe->det[$i])) {

                $produto_nf = new NotaFiscalProduto();

                $produto_nf->nota_fiscal_id = $nota->id;
                $produto_nf->valor_produto = (float)$xml->NFe->infNFe->det[$i]->prod->vProd;
                $produto_nf->valor_produto = number_format($produto_nf->valor_produto, 2, ".", "");

                $produto_nf->codigo_produto_original = (string)$xml->NFe->infNFe->det[$i]->prod->cProd;
                $produto_nf->codigo_produto = (string)$xml->NFe->infNFe->det[$i]->prod->cProd;
                $produto_nf->descricao = (string)$xml->NFe->infNFe->det[$i]->prod->xProd;
                $produto_nf->cod_ncm = (string)$xml->NFe->infNFe->det[$i]->prod->NCM;
                $produto_nf->cod_fiscal_operacao_servico = (string)$xml->NFe->infNFe->det[$i]->prod->CFOP;
                $produto_nf->unid_tributavel = (string)$xml->NFe->infNFe->det[$i]->prod->uCom;
                $produto_nf->qtd_comercial = (int)$xml->NFe->infNFe->det[$i]->prod->qCom;
                $produto_nf->valor_unitario_tributacao = (float)$xml->NFe->infNFe->det[$i]->prod->vUnCom;
                $produto_nf->valor_unitario_tributacao = number_format((float) $produto_nf->valor_unitario_tributacao, 2, ".", "");

                $icms00 = $xml->NFe->infNFe->det[$i]->imposto->ICMS->ICMS00;
                $icms10 = $xml->NFe->infNFe->det[$i]->imposto->ICMS->ICMS10;
                $icms20 = $xml->NFe->infNFe->det[$i]->imposto->ICMS->ICMS20;
                $icms30 = $xml->NFe->infNFe->det[$i]->imposto->ICMS->ICMS30;
                $icms40 = $xml->NFe->infNFe->det[$i]->imposto->ICMS->ICMS40;
                $icms50 = $xml->NFe->infNFe->det[$i]->imposto->ICMS->ICMS50;
                $icms51 = $xml->NFe->infNFe->det[$i]->imposto->ICMS->ICMS51;
                $icms60 = $xml->NFe->infNFe->det[$i]->imposto->ICMS->ICMS60;
                $ICMSSN102 = $xml->NFe->infNFe->det[$i]->imposto->ICMS->ICMSSN102;

                if (!empty($ICMSSN102)) {
                    $produto_nf->aliquota_icms = (int)"0	";
                    $produto_nf->valor_icms = (float)"0.00";
                }
                if (!empty($icms00)) {
                    $produto_nf->aliquota_icms = (int)$xml->NFe->infNFe->det[$i]->imposto->ICMS->ICMS00->pICMS;
                    $produto_nf->aliquota_icms = round($produto_nf->aliquota_icms, 0);
                    $produto_nf->valor_icms = (float)$xml->NFe->infNFe->det[$i]->imposto->ICMS->ICMS00->vICMS;
                    $produto_nf->valor_icms = number_format((float) $produto_nf->valor_icms, 2, ".", "");
                }
                if (!empty($icms20)) {
                    $produto_nf->aliquota_icms = (int)$xml->NFe->infNFe->det[$i]->imposto->ICMS->ICMS20->pICMS;
                    $produto_nf->aliquota_icms = round($produto_nf->aliquota_icms, 0);
                    $produto_nf->valor_icms = (float)$xml->NFe->infNFe->det[$i]->imposto->ICMS->ICMS20->vICMS;
                    $produto_nf->valor_icms = number_format((float) $produto_nf->valor_icms, 2, ".", "");
                }
                if (!empty($icms30)) {
                    $produto_nf->aliquota_icms = (int)"0	";
                    $produto_nf->valor_icms = (float)"0.00";
                }
                if (!empty($icms40)) {
                    $produto_nf->aliquota_icms = (int)"0	";
                    $produto_nf->valor_icms = (float)"0.00";
                }
                if (!empty($icms50)) {
                    $produto_nf->aliquota_icms = (int)"0	";
                    $produto_nf->valor_icms = 0.00;
                }
                if (!empty($icms51)) {
                    $produto_nf->aliquota_icms = (int)$xml->NFe->infNFe->det[$i]->imposto->ICMS->ICMS51->pICMS;
                    $produto_nf->aliquota_icms = round($produto_nf->aliquota_icms, 0);
                    $produto_nf->valor_icms = (float)$xml->NFe->infNFe->det[$i]->imposto->ICMS->ICMS51->vICMS;
                }
                if (!empty($icms60)) {
                    $produto_nf->aliquota_icms = 0;
                    $produto_nf->valor_icms = 0.00;
                }
                $IPITrib = $xml->NFe->infNFe->det[$i]->imposto->IPI->IPITrib;
                if (!empty($IPITrib)) {
                    $produto_nf->valor_ipi = (float)$xml->NFe->infNFe->det[$i]->imposto->IPI->IPITrib->vIPI;
                    $produto_nf->valor_ipi = number_format((float) $produto_nf->valor_ipi, 2, ".", "");
                }

                $produto_nf->save();
                $i++;
            }

            $searchModel = new NotaFiscalSearch;
            $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'erro' => "Nota Importada com Sucesso!",
                'tela' => ''
            ]);
        }
    }
}
