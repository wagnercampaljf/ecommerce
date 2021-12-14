<?php

namespace backend\models;

use Yii;

/**
 * Este é o model para a tabela "pedido_produto_filial_cotacao".
 *
 * @property integer $id
 * @property integer $pedido_produto_filial_id
 * @property integer $produto_filial_id
 * @property integer $quantidade
 * @property string $valor
 * @property string $observacao
 * @property string $email
 * @property bool $e_atualizar_site
 *
 * @author Unknown 04/05/2021
 */
class PedidoProdutoFilialCotacao extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 04/05/2021
     */
    public static function tableName()
    {
        return 'pedido_produto_filial_cotacao';
    }

    /**
     * @inheritdoc
     * @author Unknown 04/05/2021
     */
    public function rules()
    {
        return [
            [['produto_filial_id', 'quantidade'], 'default', 'value' => null],
            [['produto_filial_id', 'quantidade', 'pedido_produto_filial_id'], 'integer'],
            [['e_atualizar_site'], 'boolean'],
            [['valor', 'pedido_produto_filial_id'], 'required'],
            [['valor'], 'number'],
            [['observacao'], 'string', 'max' => 500],
            [['email'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 04/05/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pedido_produto_filial_id' => 'Pedido Produto Filial ID',
            'produto_filial_id' => 'Produto Filial ID',
            'quantidade' => 'Quantidade',
            'valor' => 'Valor',
            'observacao' => 'Observacao',
            'email' => 'Email',
            'e_atualizar_site' => 'Atualizar Site',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 04/05/2021
     */
    public static function find()
    {
        return new PedidoProdutoFilialCotacaoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da PedidoProdutoFilialCotacao, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 04/05/2021
 */
class PedidoProdutoFilialCotacaoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 04/05/2021
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['pedido_produto_filial_cotacao.nome' => $sort_type]);
    }
}
