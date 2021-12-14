<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "visita".
 *
 * @property integer $id
 * @property string $data_entrada
 * @property integer $comprador_id
 * @property integer $produto_id
 *
 * @property Comprador $comprador
 * @property Produto $produto
 *
 * @author Vinicius Schettino 02/12/2014
 */
class Visita extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'visita';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['id', 'data_entrada', 'produto_id'], 'required'],
            [['id', 'comprador_id', 'produto_id'], 'integer'],
            [['data_entrada'], 'safe']
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
            'data_entrada' => 'Data Entrada',
            'comprador_id' => 'Comprador ID',
            'produto_id' => 'Produto ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getComprador()
    {
        return $this->hasOne(Comprador::className(), ['id' => 'comprador_id']);
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
    public static function find()
    {
        return new VisitaQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Visita, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class VisitaQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['visita.nome' => $sort_type]);
    }
}
