<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "opcao_atributo".
 *
 * @property integer $id
 * @property string $nome
 * @property integer $atributo_id
 *
 * @property Atributo $atributo
 * @property ProdutoAtributoOpcao[] $produtoAtributoOpcaos
 * @property ProdutoAtributo[] $produtoAtributos
 *
 * @author Vinicius Schettino 02/12/2014
 */
class OpcaoAtributo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'opcao_atributo';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['nome', 'atributo_id'], 'required'],
            [['nome'], 'string'],
            [['atributo_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'atributo_id' => 'Atributo ID',
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
    public function getProdutoAtributoOpcaos()
    {
        return $this->hasMany(ProdutoAtributoOpcao::className(), ['opcao_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getProdutoAtributos()
    {
        return $this->hasMany(ProdutoAtributo::className(), ['id' => 'produto_atributo_id'])->viaTable(
            'produto_atributo_opcao',
            ['opcao_id' => 'id']
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public static function find()
    {
        return new OpcaoAtributoQuery(get_called_class());
    }

    public function __toString()
    {
        return $this->nome.$this->atributo->unidade;
    }
}

/**
 * Classe para contenção de escopos da OpcaoAtributo, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
 */
class OpcaoAtributoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['opcao_atributo.nome' => $sort_type]);
    }
}
