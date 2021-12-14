<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "forma_pagamento".
 *
 * @property integer $id
 * @property string $nome
 *
 * @property Pedido[] $pedidos
 *
 * @author Vitor 11/03/2015
 */
class FormaPagamento extends \yii\db\ActiveRecord
{

    const BOLETO = 2;

    /**
     * @inheritdoc
     * @author Vitor 11/03/2015
     */
    public static function tableName()
    {
        return 'forma_pagamento';
    }

    /**
     * @inheritdoc
     * @author Vitor 11/03/2015
     */
    public function rules()
    {
        return [
            [['id', 'nome'], 'required'],
            [['id'], 'integer'],
            [['nome'], 'string']
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
            'nome' => 'Nome',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vitor 11/03/2015
     */
    public function getPedidos()
    {
        return $this->hasMany(Pedido::className(), ['forma_pagamento_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vitor 11/03/2015
     */
    public static function find()
    {
        return new FormaPagamentoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da FormaPagamento, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vitor 11/03/2015
 */
class FormaPagamentoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vitor 11/03/2015
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['forma_pagamento.nome' => $sort_type]);
    }
}
