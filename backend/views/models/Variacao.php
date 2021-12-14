<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "variacao".
 *
 * @property integer $id
 * @property string $descricao
 * @property integer $variacao_tipo_id
 *
 * @property VariacaoTipo $variacaoTipo
 *
 * @author Unknown 23/06/2021
 */
class Variacao extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 23/06/2021
     */
    public static function tableName()
    {
        return 'variacao';
    }

    /**
     * @inheritdoc
     * @author Unknown 23/06/2021
     */
    public function rules()
    {
        return [
            [['descricao', 'variacao_tipo_id'], 'required'],
            [['descricao'], 'string'],
            [['variacao_tipo_id'], 'default', 'value' => null],
            [['variacao_tipo_id'], 'integer'],
            [['variacao_tipo_id'], 'exist', 'skipOnError' => true, 'targetClass' => VariacaoTipo::className(), 'targetAttribute' => ['variacao_tipo_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 23/06/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Descricao',
            'variacao_tipo_id' => 'Variacao Tipo ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 23/06/2021
    */
    public function getVariacaoTipo()
    {
        return $this->hasOne(VariacaoTipo::className(), ['id' => 'variacao_tipo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 23/06/2021
    */
    public static function find()
    {
        return new VariacaoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Variacao, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 23/06/2021
*/
class VariacaoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 23/06/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['variacao.descricao' => $sort_type]);
    }
}
