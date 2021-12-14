<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "produto_condicao".
 *
 * @property integer $id
 * @property string $nome
 * @property string $meli_id
 *
 * @property Produto[] $produtos
 *
 * @author Unknown 29/06/2020
 */
class ProdutoCondicao extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 29/06/2020
     */
    public static function tableName()
    {
        return 'produto_condicao';
    }

    /**
     * @inheritdoc
     * @author Unknown 29/06/2020
     */
    public function rules()
    {
        return [
            [['nome'], 'string', 'max' => 50],
            [['meli_id'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 29/06/2020
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'meli_id' => 'Meli ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/06/2020
    */
    public function getProdutos()
    {
        return $this->hasMany(Produto::className(), ['produto_condicao_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/06/2020
    */
    public static function find()
    {
        return new ProdutoCondicaoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da ProdutoCondicao, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 29/06/2020
*/
class ProdutoCondicaoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 29/06/2020
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['produto_condicao.nome' => $sort_type]);
    }
}
