<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "log_expedicao".
 *
 * @property integer $id
 * @property string $descricao
 * @property string $salvo_em
 * @property integer $salvo_por
 *
 * @author Unknown 21/07/2021
 */
class LogExpedicao extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 21/07/2021
     */
    public static function tableName()
    {
        return 'log_expedicao';
    }

    /**
     * @inheritdoc
     * @author Unknown 21/07/2021
     */
    public function rules()
    {
        return [
            [['descricao', 'salvo_em', 'salvo_por'], 'required'],
            [['salvo_em'], 'safe'],
            [['salvo_por'], 'default', 'value' => null],
            [['salvo_por'], 'integer'],
            [['descricao'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 21/07/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Descrição',
            'salvo_em' => 'Salvo Em',
            'salvo_por' => 'Salvo Por',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 21/07/2021
    */
    public static function find()
    {
        return new LogExpedicaoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da LogExpedicao, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 21/07/2021
*/
class LogExpedicaoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 21/07/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['log_expedicao.nome' => $sort_type]);
    }
}
