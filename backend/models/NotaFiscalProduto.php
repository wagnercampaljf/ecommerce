<?php

namespace backend\models;

use backend\models\NotaFiscalProduto as ModelsNotaFiscalProduto;
use backend\models\NotaFiscalProdutoQuery as ModelsNotaFiscalProdutoQuery;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

/**
 * Este é o model para a tabela "nota_fiscal_produto".
 *
 * @property integer $id
 * @property integer $nota_fiscal_id
 * @property string $valor_produto
 * @property string $codigo_produto
 * @property string $descricao
 * @property integer $pa_produto
 * @property string $cod_int_item
 * @property string $cod_int_produto
 * @property string $cod_item
 * @property string $cod_produto
 * @property string $cod_fiscal_operacao_servico
 * @property string $cod_situacao_tributaria_icms
 * @property string $cod_ncm
 * @property string $cfop
 * @property string $ean
 * @property string $ean_tributável
 * @property string $codigo_produto_original
 * @property integer $codigo_local_estoque
 * @property string $cmc_total
 * @property string $valor_real_produto
 * @property string $cmc_unitario
 * @property integer $aliquota_icms
 * @property integer $qtd_comercial
 * @property integer $qtd_tributavel
 * @property string $unid_tributavel
 * @property string $valor_desconto
 * @property string $valor_total_frete
 * @property string $valor_icms
 * @property string $outras_despesas
 * @property string $valor_unitario_tributacao
 * @property string $descricao_original
 *
 * @property NotaFiscal $notaFiscal
 *
 * @author Unknown 27/04/2021
 */
class NotaFiscalProduto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 27/04/2021
     */
    public static function tableName()
    {
        return 'nota_fiscal_produto';
    }

    /**
     * @inheritdoc
     * @author Unknown 27/04/2021
     */
    public function rules()
    {
        return [
            [['nota_fiscal_id', 'valor_produto', 'codigo_produto'], 'required'],
            [['nota_fiscal_id', 'pa_produto', 'codigo_local_estoque', 'aliquota_icms', 'qtd_comercial', 'qtd_tributavel'], 'default', 'value' => null],
            [['nota_fiscal_id', 'aliquota_icms', 'qtd_comercial', 'qtd_tributavel'], 'integer'],
            [['valor_produto', 'valor_real_produto', 'cod_item', 'cod_produto', 'cmc_total', 'cmc_unitario', 'valor_desconto', 'valor_total_frete', 'valor_icms', 'outras_despesas', 'valor_unitario_tributacao', 'codigo_local_estoque'], 'number'],
            [['codigo_produto', 'codigo_produto_original'], 'string', 'max' => 50],
            [['descricao'], 'string', 'max' => 500],
            [['cod_int_item', 'cod_int_produto', 'cod_situacao_tributaria_icms', 'cod_ncm', 'ean', 'ean_tributável', 'pa_produto'], 'string', 'max' => 20],
            [['cod_fiscal_operacao_servico', 'unid_tributavel'], 'string', 'max' => 10],
            [['descricao_original'], 'string', 'max' => 200],
            [['nota_fiscal_id'], 'exist', 'skipOnError' => true, 'targetClass' => NotaFiscal::class, 'targetAttribute' => ['nota_fiscal_id' => 'id']],
            [['email_produto'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 27/04/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nota_fiscal_id' => 'Nota Fiscal ID',
            'valor_produto' => 'Valor Produto',
            'valor_real_produto' => 'Valor Real Produto',
            'codigo_produto' => 'Codigo Produto',
            'descricao' => 'Descricao',
            'pa_produto' => 'Pa Produto',
            'cod_int_item' => 'Cod Int Item',
            'cod_int_produto' => 'Cod Int Produto',
            'cod_item' => 'Cod Item',
            'cod_produto' => 'Cod Produto',
            'cod_fiscal_operacao_servico' => 'Cod Fiscal Operacao Servico',
            'cod_situacao_tributaria_icms' => 'Cod Situacao Tributaria Icms',
            'cod_ncm' => 'Cod Ncm',
            'cfop' => 'CFOP',
            'ean' => 'Ean',
            'ean_tributável' => 'Ean Tributável',
            'codigo_produto_original' => 'Codigo Produto Original',
            'codigo_local_estoque' => 'Codigo Local Estoque',
            'cmc_total' => 'Cmc Total',
            'cmc_unitario' => 'Cmc Unitario',
            'aliquota_icms' => 'Aliquota Icms',
            'qtd_comercial' => 'Qtd Comercial',
            'qtd_tributavel' => 'Qtd Tributavel',
            'unid_tributavel' => 'Unid Tributavel',
            'valor_desconto' => 'Valor Desconto',
            'valor_total_frete' => 'Valor Total Frete',
            'valor_icms' => 'Valor Icms',
            'outras_despesas' => 'Outras Despesas',
            'valor_unitario_tributacao' => 'Valor Unitario Tributacao',
            'descricao_original' => 'Descricao Original',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 27/04/2021
     */
    public function getNotaFiscal()
    {
        return $this->hasOne(NotaFiscal::class, ['id' => 'nota_fiscal_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 27/04/2021
     */
    public static function find()
    {
        return new NotaFiscalProdutoQuery(get_called_class());
    }

    public function ValidacaoNotaProduto($id)
    {
        $model = NotaFiscalProduto::findOne($id);

        $query1 = (new \yii\db\Query())
            ->Select([
                'nota_fiscal_pedido_produto.id',
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
            ->innerJoin("filial", "filial.id = produto_filial.filial_id");

        $query2 = (new \yii\db\Query())
            ->Select(
                [
                    'nota_fiscal_pedido_produto.id',
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
            ->innerJoin("filial", "filial.id = produto_filial.filial_id");

        $query3 = (new \yii\db\Query())
            ->Select(
                [
                    'nota_fiscal_pedido_produto.id',
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
            ->innerJoin("filial", "filial.id = produto_filial.filial_id");

        $valor_total = ($model->valor_unitario_tributacao * $model->qtd_comercial) + $model->valor_icms + $model->valor_ipi + $model->valor_total_frete + $model->outras_despesas + $model->valor_seguro - $model->valor_desconto;

        $unionQuery = (new \yii\db\Query())
            ->from(['t1' => $query1->union($query2->union($query3, true), true)])
            ->where("t1.e_validado = false and (t1.codigo_fabricante like '%$model->codigo_produto%' or t1.pa = '$model->pa_produto' or t1.valor = '$valor_total' or t1.nome_produto = '$model->descricao')");

        $provider = new ActiveDataProvider([
            'query' => $unionQuery,
            'pagination' => [
                'pageSize' => 20,
                'totalCount' => 100,
            ],
        ]);
        return $provider;
    }

    public function RelacaoNotasPedido($id)
    {
        $query1 = (new \yii\db\Query())
            ->Select([
                'nota_fiscal_pedido_produto.id',
                'nota_fiscal_pedido_produto.nota_fiscal_produto_id',
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
            ->innerJoin("filial", "filial.id = produto_filial.filial_id");

        $query2 = (new \yii\db\Query())
            ->Select(
                [
                    'nota_fiscal_pedido_produto.id',
                    'nota_fiscal_pedido_produto.nota_fiscal_produto_id',
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
            ->innerJoin("filial", "filial.id = produto_filial.filial_id");

        $query3 = (new \yii\db\Query())
            ->Select(
                [
                    'nota_fiscal_pedido_produto.id',
                    'nota_fiscal_pedido_produto.nota_fiscal_produto_id',
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
            ->innerJoin("filial", "filial.id = produto_filial.filial_id");

        $unionQuery = (new \yii\db\Query())
            ->from(['t1' => $query1->union($query2->union($query3, true), true)])
            ->where("t1.e_validado = true and t1.nota_fiscal_produto_id = $id");

        $provider = new ActiveDataProvider([
            'query' => $unionQuery,
            'pagination' => [
                'pageSize' => 20,
                'totalCount' => 100,
            ],
        ]);
        return $provider;
    }
}

/**
 * Classe para contenção de escopos da NotaFiscalProduto, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 27/04/2021
 */
class NotaFiscalProdutoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 27/04/2021
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['nota_fiscal_produto.nome' => $sort_type]);
    }
}
