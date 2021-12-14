<?php

namespace backend\models;

use common\models\Produto;
use Yii;

/**
 * Este é o model para a tabela "nota_fiscal".
 *
 * @property integer $id
 * @property string $chave_nf
 * @property string $valor_nf
 * @property string $data_nf
 * @property string $id_nf
 * @property string $id_pedido
 * @property string $numero_nf
 * @property string $modo_frete
 * @property string $id_recebimento
 * @property integer $id_transportadora
 * @property string $data_cancelamento
 * @property string $data_emissao
 * @property string $data_inutilizacao
 * @property string $data_registro
 * @property string $data_saida
 * @property integer $finalidade_emissao
 * @property integer $tipo_nf
 * @property integer $tipo_ambiente
 * @property integer $serie
 * @property integer $codigo_modelo
 * @property integer $indice_pagamento
 * @property string $h_saida_entrada_nf
 * @property string $h_emissao
 * @property integer $cod_int_empresa
 * @property integer $cod_empresa
 * @property integer $cod_int_cliente_fornecedor
 * @property integer $cod_cliente
 * @property boolean $e_validada
 * @property string $observacao
 *
 * @property NotaFiscalProduto[] $notaFiscalProdutos
 *
 * @author Unknown 27/04/2021
 */
class NotaFiscal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 27/04/2021
     */
    public static function tableName()
    {
        return 'nota_fiscal';
    }

    /**
     * @inheritdoc
     * @author Unknown 27/04/2021
     */
    public function rules()
    {
        return [
            [['chave_nf', 'valor_nf', 'data_nf'], 'required'],
            [['valor_nf', 'id_nf', 'id_pedido', 'numero_nf', 'id_recebimento', 'id_transportadora'], 'number'],
            [['data_nf', 'data_cancelamento', 'data_emissao', 'data_inutilizacao', 'data_registro', 'data_saida', 'h_saida_entrada_nf', 'h_emissao'], 'safe'],
            [['id_transportadora', 'finalidade_emissao', 'tipo_nf', 'tipo_ambiente', 'serie', 'codigo_modelo', 'indice_pagamento', 'cod_int_empresa', 'cod_empresa', 'cod_int_cliente_fornecedor', 'cod_cliente'], 'default', 'value' => null],
            [['finalidade_emissao', 'tipo_nf', 'tipo_ambiente', 'serie', 'codigo_modelo', 'indice_pagamento', 'cod_int_empresa', 'cod_empresa', 'cod_int_cliente_fornecedor', 'cod_cliente'], 'integer'],
            [['chave_nf'], 'string', 'max' => 100],
            [['modo_frete'], 'string', 'max' => 10],
            [['e_validada'], 'boolean'],
            [['observacao'], 'string']
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
            'chave_nf' => 'Chave Nf',
            'valor_nf' => 'Valor Nf',
            'data_nf' => 'Data Nf',
            'id_nf' => 'Id Nf',
            'id_pedido' => 'Id Pedido',
            'numero_nf' => 'Numero Nf',
            'modo_frete' => 'Modo Frete',
            'id_recebimento' => 'Id Recebimento',
            'id_transportadora' => 'Id Transportadora',
            'data_cancelamento' => 'Data Cancelamento',
            'data_emissao' => 'Data Emissao',
            'data_inutilizacao' => 'Data Inutilizacao',
            'data_registro' => 'Data Registro',
            'data_saida' => 'Data Saida',
            'finalidade_emissao' => 'Finalidade Emissao',
            'tipo_nf' => 'Tipo Nf',
            'tipo_ambiente' => 'Tipo Ambiente',
            'serie' => 'Serie',
            'codigo_modelo' => 'Codigo Modelo',
            'indice_pagamento' => 'Indice Pagamento',
            'h_saida_entrada_nf' => 'H Saida Entrada Nf',
            'h_emissao' => 'H Emissao',
            'cod_int_empresa' => 'Cod Int Empresa',
            'cod_empresa' => 'Cod Empresa',
            'cod_int_cliente_fornecedor' => 'Cod Int Cliente Fornecedor',
            'cod_cliente' => 'Cod Cliente',
            'e_validada' => 'Validada',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 27/04/2021
     */
    public function getNotaFiscalProdutos()
    {
        return $this->hasMany(NotaFiscalProduto::class, ['nota_fiscal_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 27/04/2021
     */
    public static function find()
    {
        return new NotaFiscalQuery(get_called_class());
    }

    public static function EmailNotaFiscalEntrada($id)
    {
        $filial_sp2 = '500277925'; //Filial SP2

        $model = NotaFiscal::findOne($id);
        $nota_fiscal_produto = NotaFiscalProduto::findAll(['nota_fiscal_id' => $model->id]);
        $produtos = '';

        foreach ($nota_fiscal_produto as $produto) {

            $markup = Yii::$app->db->createCommand("select margem from markup_detalhe md 
                inner join markup_mestre mm on md.markup_mestre_id = mm.id 
                where ($produto->valor_real_produto ::float between valor_minimo and valor_maximo) and mm.e_markup_padrao = true
                 order by mm.id desc 
                 limit 1")->queryScalar();

            if ($model->cod_empresa == $filial_sp2 && $model->fornecedor == 'ZAPPAROLI IND E COM DE PLASTICOS LTDA') {
                $markup = 1.85;
            }

            if ($model->fornecedor == 'KARTER LUBRIFICANTES LTDA') {
                $markup = 1.82;
            }

            $nome = '';
            $codigo_global = '';
            if (isset($produto->pa_produto)) {
                $pa = substr($produto->pa_produto, 2);
                $dados_produto = Produto::findOne($pa);
                // echo '<pre>'; print_r($dados_produto); echo '</pre>'; die;
                $codigo_global = $dados_produto->codigo_global;
                $nome = $dados_produto->nome;
            } else {
                $nome = $produto->descricao;
            }

            $produtos .= "\n
                Cód. Forn.: " . $produto->codigo_produto_original . "
                Cód. Global: " . $codigo_global . "
                Descrição: " . $nome . "
                Quantidade: " . $produto->qtd_comercial . "
                Valor: " . $produto->valor_real_produto . "
                PA: " . $produto->pa_produto . "
                Markup: " . $markup;
        }

        $emails = 'compraestoque.pecaagora@gmail.com,compras.pecaagora@gmail.com';

        $emails_destinatarios = explode(",", $emails);
        var_dump(\Yii::$app->mailer->compose()
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
            ->setTo($emails_destinatarios)
            ->setSubject("[Entrada de NF] nº $model->numero_nf - $model->fornecedor")
            ->setTextBody($produtos)
            ->send());
    }
}

/**
 * Classe para contenção de escopos da NotaFiscal, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 27/04/2021
 */
class NotaFiscalQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 27/04/2021
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['nota_fiscal.nome' => $sort_type]);
    }
}
