<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "carrinho_produto_filial".
 *
 * @property integer $produto_filial_id
 * @property integer $carrinho_id
 * @property string $data_inclusao
 *
 * @property ProdutoFilial $produtoFilial
 * @property Carrinho $carrinho
 *
 * @author Vinicius Schettino 02/12/2014
 */
class CarrinhoProdutoFilial extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'carrinho_produto_filial';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['produto_filial_id', 'carrinho_id'], 'required'],
            [['produto_filial_id', 'carrinho_id'], 'integer'],
            [['data_inclusao'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function attributeLabels()
    {
        return [
            'produto_filial_id' => 'Produto Filial ID',
            'carrinho_id' => 'Carrinho ID',
            'data_inclusao' => 'Data Inclusao',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getProdutoFilial()
    {
        return $this->hasOne(ProdutoFilial::className(), ['id' => 'produto_filial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getCarrinho()
    {
        return $this->hasOne(Carrinho::className(), ['id' => 'carrinho_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public static function find()
    {
        return new CarrinhoProdutoFilialQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da CarrinhoProdutoFilial, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class CarrinhoProdutoFilialQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['carrinho_produto_filial.nome' => $sort_type]);
    }
}
