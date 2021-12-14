<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "log_operacao".
 *
 * @property integer $id
 * @property string $descricao
 *
 * @property Log[] $logs
 *
 * @author Unknown 17/05/2021
 */
class LogOperacao extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 17/05/2021
     */
    public static function tableName()
    {
        return 'log_operacao';
    }

    /**
     * @inheritdoc
     * @author Unknown 17/05/2021
     */
    public function rules()
    {
        return [
            [['descricao'], 'required'],
            [['descricao'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 17/05/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descricao' => 'Descricao',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 17/05/2021
    */
    public function getLogs()
    {
        return $this->hasMany(Log::className(), ['log_operacao_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 17/05/2021
    */
    public static function find()
    {
        return new LogOperacaoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da LogOperacao, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 17/05/2021
*/
class LogOperacaoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 17/05/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['log_operacao.nome' => $sort_type]);
    }
}
