<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "atributo".
 *
 * @property integer $id
 * @property string $nome
 * @property string $descricao
 * @property string $unidade
 * @property boolean $multiplo
 * @property integer $subcategoria_id
 *
 * @property Subcategoria $subcategoria
 * @property ProdutoAtributo[] $produtoAtributos
 * @property OpcaoAtributo[] $opcaoAtributos
 *
 * @author Vinicius Schettino 02/12/2014
 */
class Atributo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'atributo';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['nome', 'subcategoria_id'], 'required'],
            [['multiplo'], 'boolean'],
            [['subcategoria_id'], 'integer'],
            [['nome'], 'string', 'max' => 30],
            [['descricao'], 'string', 'max' => 250],
            [['unidade'], 'string', 'max' => 25]
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
            'descricao' => 'Descricao',
            'unidade' => 'Unidade',
            'multiplo' => 'Multiplo',
            'subcategoria_id' => 'Subcategoria ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getSubcategoria()
    {
        return $this->hasOne(Subcategoria::className(), ['id' => 'subcategoria_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getProdutoAtributos()
    {
        return $this->hasMany(ProdutoAtributo::className(), ['atributo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getOpcaoAtributos()
    {
        return $this->hasMany(OpcaoAtributo::className(), ['atributo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public static function find()
    {
        return new AtributoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Atributo, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class AtributoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['atributo.nome' => $sort_type]);
    }
}
