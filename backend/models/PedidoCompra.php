<?php

namespace backend\models;

use backend\controllers\ValorProdutoFilialController;
use common\models\Filial;
use common\models\Fornecedor;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use console\controllers\actions\omie\Omie;
use frontend\models\MarkupMestre;
use Yii;

/**
 * Este é o model para a tabela "pedido_compra".
 *
 * @property integer $id
 * @property string $descricao
 * @property string $data
 * @property string $observacao
 * @property string $email
 * @property string $corpo_email
 * @property integer $filial_id
 * @property MarkupMestre[] $markupMestre
 *
 * @property Filial $filial
 * @property PedidoCompraProdutoFilial[] $pedidoCompraProdutoFilials
 *
 * @author Unknown 05/02/2021
 */
class PedidoCompra extends \yii\db\ActiveRecord
{
    public $markup_id;
    /**
     * @inheritdoc
     * @author Unknown 05/02/2021
     */
    public static function tableName()
    {
        return 'pedido_compra';
    }

    /**
     * @inheritdoc
     * @author Unknown 05/02/2021
     */
    public function rules()
    {
        return [
            [['descricao', 'data', 'email'], 'required'],
            [['valor_total_pedido'], 'number'],
            [['data'], 'safe'],
            [['corpo_email'], 'string'],
            [['descricao'], 'string', 'max' => 200],
            [['observacao'], 'string', 'max' => 500],
            [['status'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 200],
            [['markup_id'], 'required', 'on' => ['create']],
            [['filial_id'], 'exist', 'skipOnError' => true, 'targetClass' => Filial::class, 'targetAttribute' => ['filial_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 05/02/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Descricao',
            'data' => 'Data',
            'observacao' => 'Observacao',
            'email' => 'Email',
            'corpo_email' => 'Corpo Email',
            'status' => 'Status',
            'filial_id' => 'Filial ID',
            'markup_id' => 'Markup',
            'valor_total_pedido' => 'Valor Total Pedido',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 05/02/2021
     */
    public function getFilial()
    {
        return $this->hasOne(Filial::class, ['id' => 'filial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 05/02/2021
     */
    public function getPedidoCompraProdutoFilials()
    {
        return $this->hasMany(PedidoCompraProdutoFilial::class, ['pedido_compra_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 05/02/2021
     */
    public static function find()
    {
        return new PedidoCompraQuery(get_called_class());
    }

    public static function CriarPedidoCompras($cChaveNFe, $perc_nota = 100, $e_manual = false)
    {

        $acessoOmie = [
            '468080198586'  => '7b3fb2b3bae35eca3b051b825b6d9f43',
            '469728530271'  => '6b63421c9bb3a124e012a6bb75ef4ace',
            '1017311982687' => '78ba33370fac6178da52d42240591291',
            '1758907907757' => '0a69c9b49e5a188e5f43d5505f2752bc'
        ];

        $omie = new Omie(1, 1);

        $modelNota = NotaFiscal::findOne(['chave_nf' => $cChaveNFe]);
        $modelNotaProduto = NotaFiscalProduto::findAll(['nota_fiscal_id' => $modelNota->id]);

        if (empty($modelNota)) {
            return [
                'mensagem' => 'Nota não cadastrada no sistema !',
            ];
        }

        if ($e_manual) {               
                

            foreach ($modelNotaProduto as $modelNotaProdutos) {

                

                if ($modelNota->fornecedor == 'ASSIS FIBRAS INDUSTRIA E COMERCIO LTDA') {

                    $add_assis = $modelNotaProdutos->valor_unitario_tributacao * 0.08;
                    $modelNotaProdutos->valor_real_produto = (($modelNotaProdutos->valor_unitario_tributacao * 100) / $perc_nota) + $add_assis;
                } else {
                    $imposto = ($modelNotaProdutos->valor_icms + $modelNotaProdutos->valor_ipi + $modelNotaProdutos->valor_seguro + $modelNotaProdutos->valor_total_frete +
                        $modelNotaProdutos->outras_despesas) - $modelNotaProdutos->valor_desconto;
                    $imposto = $imposto / $modelNotaProdutos->qtd_comercial;
                    $modelNotaProdutos->valor_real_produto = ($modelNotaProdutos->valor_unitario_tributacao * 100) / $perc_nota + $imposto;
                }
                
                $modelNotaProdutos->save();

                $filial = '';

                foreach ($acessoOmie as $APP_KEY_OMIE => $APP_SECRET_OMIE) {

                    $body = [
                        "call" => "ConsultarEmpresa",
                        "app_key" => $APP_KEY_OMIE,
                        "app_secret" => $APP_SECRET_OMIE,
                        "param" => [
                            "codigo_empresa" => $modelNota->cod_empresa,
                        ]
                    ];

                    $response_omie = $omie->consulta("api/v1/geral/empresas/", $body);

                    if ($response_omie['httpCode'] == 200) {
                        switch ($response_omie['body']['cnpj']) {
                            case '18.947.338/0001-10':
                                $filial = 94;
                                break;
                            case '18.947.338/0002-00':
                                $filial = 96;
                                break;
                            case '18.947.338/0003-82':
                                $filial = 95;
                                break;
                            default:
                                $filial = 93;
                                break;
                        }
                    }


                }

                $produto_id = str_replace('PA', '', $modelNotaProdutos->pa_produto);
                $produto_filial = ProdutoFilial::find()->where("produto_id = $produto_id and filial_id = $filial")->limit(1)->one();

                $markup = Yii::$app->db->createCommand("select margem from markup_detalhe md 
                    inner join markup_mestre mm on md.markup_mestre_id = mm.id 
                    where ($modelNotaProdutos->valor_real_produto::float between valor_minimo and valor_maximo) and mm.e_markup_padrao = true
                    order by mm.id desc 
                    limit 1")->queryScalar();

                $valorProdutoFilial = new ValorProdutoFilial();
                $valorProdutoFilial->valor = $markup > 5 ? $markup : number_format($modelNotaProdutos->valor_real_produto * $markup, 2, '.', '');
                $valorProdutoFilial->dt_inicio = date("Y-m-d H:i:s");
                $valorProdutoFilial->produto_filial_id = $produto_filial->id;
                $valorProdutoFilial->valor_cnpj = $markup > 5 ? $markup : number_format($modelNotaProdutos->valor_real_produto * $markup, 2, '.', '');
                $valorProdutoFilial->valor_compra = $modelNotaProdutos->valor_real_produto;
                $valorProdutoFilial->promocao = false;               
                $valorProdutoFilial->save(false);
                // echo '<pre>';var_dump($valorProdutoFilial->save());
                // var_dump($valorProdutoFilial); echo '</pre>';

                // die;
                ValorProdutoFilialController::AtualizarValorProdutoFilial($valorProdutoFilial);
            }
        }

        $modelPedidoCompra = new PedidoCompra();

        $filial = '';

        foreach ($acessoOmie as $APP_KEY_OMIE => $APP_SECRET_OMIE) {

            $body = [
                "call" => "ConsultarEmpresa",
                "app_key" => $APP_KEY_OMIE,
                "app_secret" => $APP_SECRET_OMIE,
                "param" => [
                    "codigo_empresa" => $modelNota->cod_empresa,
                ]
            ];

            $response_omie = $omie->consulta("api/v1/geral/empresas/", $body);

            if ($response_omie['httpCode'] == 200) {
                if ($response_omie['body']['cnpj'] == '18.947.338/0001-10') {
                    $filial = 94;
                } else
                if ($response_omie['body']['cnpj'] == '18.947.338/0002-00') {
                    $filial = 96;
                } else
                if ($response_omie['body']['cnpj'] == '18.947.338/0003-82') {
                    $filial = 95;
                } else {
                    $filial = 93;
                }

                break;
            }
        }

        $fornecedor = Fornecedor::findOne(['codigo_fornecedor_omie' => $modelNota->cod_cliente]);
        
        $modelPedidoCompra->fornecedor_id = $fornecedor->id;
        $modelPedidoCompra->descricao = date('d/m/Y') . ' - ' . $fornecedor->nome;
        $dEmi = explode('-', $modelNota->data_nf);
        $modelPedidoCompra->data = $dEmi = substr($dEmi[2], 0, 2) . "/" . $dEmi[1] . "/" . $dEmi[0];
        $modelPedidoCompra->observacao = '';
        $modelPedidoCompra->email = 'compras.pecaagora@gmail.com';
        $modelPedidoCompra->status = 1;
        $modelPedidoCompra->save(false);


        $total = 0;

        foreach ($modelNotaProduto as $produto) {

            if ($produto->pa_produto !== '') {

                $pedidoCompraProdutoFilial = new PedidoCompraProdutoFilial();
                $produto_id = str_replace('PA', '', $produto->pa_produto);
                $produto_filial = ProdutoFilial::find()->where("produto_id = $produto_id and filial_id = $filial")->limit(1)->one();

                if (!$produto_filial) {

                    $produto_filial = new ProdutoFilial();
                    $produto_filial->produto_id = $produto_id;
                    $produto_filial->filial_id = $filial;
                    $produto_filial->quantidade = $produto->qtd_comercial;
                    $produto_filial->envio = 1;
                    $produto_filial->save(false);
                }

                $pedidoCompraProdutoFilial->produto_filial_id = $produto_filial->id;
                $pedidoCompraProdutoFilial->quantidade = $produto->qtd_comercial;
                $pedidoCompraProdutoFilial->valor_compra = $produto->valor_real_produto;

                $pedidoCompraProdutoFilial->pedido_compra_id = $modelPedidoCompra->id;

                $markup = Yii::$app->db->createCommand("select margem from markup_detalhe md 
                    inner join markup_mestre mm on md.markup_mestre_id = mm.id 
                    where ($pedidoCompraProdutoFilial->valor_compra ::float between valor_minimo and valor_maximo) and mm.e_markup_padrao = true
                    order by mm.id desc 
                    limit 1")->queryScalar();

                if ($filial == 96 && $fornecedor->nome == 'ZAPPAROLI IND E COM DE PLASTICOS LTDA') {
                    $markup = 1.85;
                }

                if ($fornecedor->nome == 'KARTER LUBRIFICANTES LTDA') {
                    $markup = 1.82;
                }

                $pedidoCompraProdutoFilial->valor_venda = $markup > 5 ? $markup : number_format($pedidoCompraProdutoFilial->valor_compra * $markup, 2, '.', '');
                $pedidoCompraProdutoFilial->valor_markup = $markup;
                $pedidoCompraProdutoFilial->e_atualizar_site = true;
                $pedidoCompraProdutoFilial->e_verificado = false;

                $total += ($pedidoCompraProdutoFilial->valor_compra * $pedidoCompraProdutoFilial->quantidade);
                $pedidoCompraProdutoFilial->save(false);
            }
            $modelPedidoCompra->valor_total_pedido = $total;
            $modelPedidoCompra->save(false);
        }

        return [
            'mensagem' => 'Pedido Cadastrado com Sucesso !',
        ];
    }
}

/**
 * Classe para contenção de escopos da PedidoCompra, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 05/02/2021
 */
class PedidoCompraQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 05/02/2021
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['pedido_compra.nome' => $sort_type]);
    }
}
