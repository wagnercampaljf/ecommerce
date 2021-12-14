<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "tipo_status_pedido".
 *
 * @property integer $id
 * @property string $nome
 * @property boolean $ativo
 *
 * @property StatusPedido[] $statusPedidos
 *
 * @author Vitor 11/03/2015
 */
class TipoStatusPedido extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vitor 11/03/2015
     */
    public static function tableName()
    {
        return 'tipo_status_pedido';
    }

    /**
     * @inheritdoc
     * @author Vitor 11/03/2015
     */
    public function rules()
    {
        return [
            [['id', 'nome', 'ativo'], 'required'],
            [['id'], 'integer'],
            [['nome'], 'string'],
            [['ativo'], 'boolean']
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
            'nome' => 'Status',
            'ativo' => 'Ativo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vitor 11/03/2015
     */
    public function getStatusPedidos()
    {
        return $this->hasMany(StatusPedido::className(), ['tipo_status_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vitor 11/03/2015
     */
    public static function find()
    {
        return new TipoStatusPedidoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da TipoStatusPedido, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vitor 11/03/2015
 */
class TipoStatusPedidoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vitor 11/03/2015
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['tipo_status_pedido.nome' => $sort_type]);
    }
}
