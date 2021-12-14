<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "produto_filial_variacao".
 *
 * @property integer $id
 * @property integer $produto_filial_id
 * @property integer $variacao_id
 * @property string $meli_id
 *
 * @property ProdutoFilial $produtoFilial
 * @property Variacao $variacao
 *
 * @author Unknown 24/06/2021
 */
class ProdutoFilialVariacao extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 24/06/2021
     */
    public static function tableName()
    {
        return 'produto_filial_variacao';
    }

    /**
     * @inheritdoc
     * @author Unknown 24/06/2021
     */
    public function rules()
    {
        return [
            [['produto_filial_id', 'variacao_id'], 'required'],
            [['produto_filial_id', 'variacao_id'], 'default', 'value' => null],
            [['produto_filial_id', 'variacao_id'], 'integer'],
            [['meli_id'], 'string', 'max' => 40],
            [['produto_filial_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProdutoFilial::className(), 'targetAttribute' => ['produto_filial_id' => 'id']],
            [['variacao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Variacao::className(), 'targetAttribute' => ['variacao_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 24/06/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'produto_filial_id' => 'Produto Filial ID',
            'variacao_id' => 'Variacao ID',
            'meli_id' => 'Meli ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 24/06/2021
    */
    public function getProdutoFilial()
    {
        return $this->hasOne(ProdutoFilial::className(), ['id' => 'produto_filial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 24/06/2021
    */
    public function getVariacao()
    {
        return $this->hasOne(Variacao::className(), ['id' => 'variacao_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 24/06/2021
    */
    public static function find()
    {
        return new ProdutoFilialVariacaoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da ProdutoFilialVariacao, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 24/06/2021
*/
class ProdutoFilialVariacaoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 24/06/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['produto_filial_variacao.nome' => $sort_type]);
    }
}
