<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "movimentacao_estoque_detalhe".
 *
 * @property integer $id
 * @property string $descricao
 * @property integer $produto_id
 * @property string $salvo_em
 * @property integer $salvo_por
 * @property integer $quantidade
 * @property string $id_ajuste_omie_entrada
 * @property string $id_ajuste_omie_saida
 * @property integer $movimentacao_estoque_mestre_id
 * @property bool $e_autorizado
 *
 * @property MovimentacaoEstoqueMestre $movimentacaoEstoqueMestre
 * @property Produto $produto
 * @property Administrador $salvoPor
 *
 * @author Unknown 29/10/2021
 */
class MovimentacaoEstoqueDetalhe extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 29/10/2021
     */
    public static function tableName()
    {
        return 'movimentacao_estoque_detalhe';
    }

    /**
     * @inheritdoc
     * @author Unknown 29/10/2021
     */
    public function rules()
    {
        return [
            [['descricao', 'id_ajuste_omie_entrada', 'id_ajuste_omie_saida'], 'string'],
            [['produto_id', 'salvo_por', 'quantidade', 'movimentacao_estoque_mestre_id'], 'default', 'value' => null],
            [['produto_id', 'salvo_por', 'quantidade', 'movimentacao_estoque_mestre_id'], 'integer'],
            [['salvo_em'], 'required'],
            [['salvo_em'], 'safe'],
            [['salvo_por'], 'exist', 'skipOnError' => true, 'targetClass' => Administrador::className(), 'targetAttribute' => ['salvo_por' => 'id']],
            [['movimentacao_estoque_mestre_id'], 'exist', 'skipOnError' => true, 'targetClass' => MovimentacaoEstoqueMestre::className(), 'targetAttribute' => ['movimentacao_estoque_mestre_id' => 'id']],
            [['produto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Produto::className(), 'targetAttribute' => ['produto_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 29/10/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Descricao',
            'produto_id' => 'PA',
            'salvo_em' => 'Salvo Em',
            'salvo_por' => 'Salvo Por',
            'quantidade' => 'Quantidade',
            'id_ajuste_omie_entrada' => 'Id Ajuste Omie Entrada',
            'id_ajuste_omie_saida' => 'Id Ajuste Omie Saida',
            'movimentacao_estoque_mestre_id' => 'Movimentacao Estoque Mestre ID',
            'produto' => 'Nome',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/10/2021
    */
    public function getMovimentacaoEstoqueMestre()
    {
        return $this->hasOne(MovimentacaoEstoqueMestre::className(), ['id' => 'movimentacao_estoque_mestre_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/10/2021
    */
    public function getProduto()
    {
        return $this->hasOne(Produto::className(), ['id' => 'produto_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/10/2021
    */
    public function getSalvoPor()
    {
        return $this->hasOne(Administrador::className(), ['id' => 'salvo_por']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/10/2021
    */
    public static function find()
    {
        return new MovimentacaoEstoqueDetalheQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da MovimentacaoEstoqueDetalhe, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 29/10/2021
*/
class MovimentacaoEstoqueDetalheQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/10/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['movimentacao_estoque_detalhe.nome' => $sort_type]);
    }
}
