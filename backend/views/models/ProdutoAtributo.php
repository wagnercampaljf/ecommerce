<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "produto_atributo".
 *
 * @property integer $produto_id
 * @property integer $atributo_id
 * @property double $valor
 * @property integer $id
 *
 * @property Atributo $atributo
 * @property Produto $produto
 * @property ProdutoAtributoOpcao[] $produtoAtributoOpcoes
 * @property OpcaoAtributo[] $opcoes
 *
 * @author Vinicius Schettino 02/12/2014
 */
class ProdutoAtributo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'produto_atributo';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['produto_id', 'atributo_id', 'id'], 'required'],
            [['produto_id', 'atributo_id', 'id'], 'integer'],
            [['valor'], 'number']
        ];
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function attributeLabels()
    {
        return [
            'produto_id' => 'Produto ID',
            'atributo_id' => 'Atributo ID',
            'valor' => 'Valor',
            'id' => 'ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getAtributo()
    {
        return $this->hasOne(Atributo::className(), ['id' => 'atributo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getProduto()
    {
        return $this->hasOne(Produto::className(), ['id' => 'produto_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getProdutoAtributoOpcoes()
    {
        return $this->hasMany(ProdutoAtributoOpcao::className(), ['produto_atributo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getOpcoes()
    {
        return $this->hasMany(OpcaoAtributo::className(), ['id' => 'opcao_id'])->viaTable(
            'produto_atributo_opcao',
            ['produto_atributo_id' => 'id']
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public static function find()
    {
        return new ProdutoAtributoQuery(get_called_class());
    }

    public function getValor()
    {
        if ($this->valor !== null) {
            return $this->labelValor();
        } else {
            return implode(', ', $this->opcoes);
        }
    }

    public function labelValor()
    {
        if ($this->valor !== null) {
            return $this->valor . $this->atributo->unidade;
        }
    }
}

/**
 * Classe para contenção de escopos da ProdutoAtributo, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
 */
class ProdutoAtributoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['produto_atributo.nome' => $sort_type]);
    }
}
