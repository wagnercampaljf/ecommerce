<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "produto_atributo_opcao".
 *
 * @property integer $produto_atributo_id
 * @property integer $opcao_id
 *
 * @property ProdutoAtributo $produtoAtributo
 * @property OpcaoAtributo $opcao
 *
 * @author Vinicius Schettino 02/12/2014
 */
class ProdutoAtributoOpcao extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'produto_atributo_opcao';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['produto_atributo_id', 'opcao_id'], 'required'],
            [['produto_atributo_id', 'opcao_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function attributeLabels()
    {
        return [
            'produto_atributo_id' => 'Produto Atributo ID',
            'opcao_id' => 'Opcao ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getProdutoAtributo()
    {
        return $this->hasOne(ProdutoAtributo::className(), ['id' => 'produto_atributo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getOpcao()
    {
        return $this->hasOne(OpcaoAtributo::className(), ['id' => 'opcao_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public static function find()
    {
        return new ProdutoAtributoOpcaoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da ProdutoAtributoOpcao, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class ProdutoAtributoOpcaoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['produto_atributo_opcao.nome' => $sort_type]);
    }
}
