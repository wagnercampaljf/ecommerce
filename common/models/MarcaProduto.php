<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "marca_produto".
 *
 * @property integer $id
 * @property string $nome
 *
 * @author Unknown 06/04/2020
 */
class MarcaProduto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 06/04/2020
     */
    public static function tableName()
    {
        return 'marca_produto';
    }

    /**
     * @inheritdoc
     * @author Unknown 06/04/2020
     */
    public function rules()
    {
        return [
            [['nome'], 'required'],
            [['nome'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 06/04/2020
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 06/04/2020
    */
    public static function find()
    {
        return new MarcaProdutoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da MarcaProduto, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 06/04/2020
*/
class MarcaProdutoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 06/04/2020
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['marca_produto.nome' => $sort_type]);
    }
}
