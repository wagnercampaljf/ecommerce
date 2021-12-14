<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "boleto".
 *
 * @property integer $id
 * @property string $numero
 * @property string $dt_vencimento
 * @property string $dt_criacao
 * @property integer $pedido_id
 *
 * @property Pedido $pedido
 *
 * @author Vinicius Schettino 02/12/2014
 */
class Boleto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'boleto';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['numero', 'dt_vencimento', 'pedido_id'], 'required'],
            [['dt_vencimento', 'dt_criacao'], 'safe'],
            [['pedido_id'], 'integer'],
            [['numero'], 'string', 'max' => 255]
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
            'numero' => 'Numero',
            'dt_vencimento' => 'Dt Vencimento',
            'dt_criacao' => 'Dt Criacao',
            'pedido_id' => 'Pedido ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getPedido()
    {
        return $this->hasOne(Pedido::className(), ['id' => 'pedido_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public static function find()
    {
        return new BoletoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Boleto, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class BoletoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['boleto.nome' => $sort_type]);
    }
}
