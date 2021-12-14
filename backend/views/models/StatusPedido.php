<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "status_pedido".
 *
 * @property integer $id
 * @property string $data_corrente
 * @property string $data_referencia
 * @property integer $pedido_id
 * @property integer $tipo_status_id
 * @property string $observacao
 *
 * @property Pedido $pedido
 * @property TipoStatusPedido $tipoStatus
 *
 * @author Vitor 11/03/2015
 */
class StatusPedido extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vitor 11/03/2015
     */
    public static function tableName()
    {
        return 'status_pedido';
    }

    /**
     * @inheritdoc
     * @author Vitor 11/03/2015
     */
    public function rules()
    {
        return [
            [['data_corrente', 'data_referencia'], 'safe'],
            [['data_referencia', 'pedido_id', 'tipo_status_id'], 'required'],
            [['pedido_id', 'tipo_status_id'], 'integer'],
            [['observacao'], 'string']
        ];
    }

    /**
     * @inheritdoc
     * @author Vitor 11/03/2015
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data_corrente' => 'Data Corrente',
            'data_referencia' => 'Data Referencia',
            'pedido_id' => 'Pedido ID',
            'tipo_status_id' => 'Tipo Status ID',
            'observacao' => 'Observacao',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vitor 11/03/2015
     */
    public function getPedido()
    {
        return $this->hasOne(Pedido::className(), ['id' => 'pedido_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vitor 11/03/2015
     */
    public function getTipoStatus()
    {
        return $this->hasOne(TipoStatusPedido::className(), ['id' => 'tipo_status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vitor 11/03/2015
     */
    public static function find()
    {
        return new StatusPedidoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da StatusPedido, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vitor 11/03/2015
 */
class StatusPedidoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vitor 11/03/2015
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['status_pedido.nome' => $sort_type]);
    }
}
