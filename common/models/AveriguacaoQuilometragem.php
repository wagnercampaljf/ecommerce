<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "averiguacao_quilometragem".
 *
 * @property integer $id
 * @property integer $km_averiguada
 * @property string $dt_averiguacao
 * @property string $dt_recebimento
 * @property integer $veiculo_id
 *
 * @property Veiculo $veiculo
 *
 * @author Vinicius Schettino 02/12/2014
 */
class AveriguacaoQuilometragem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'averiguacao_quilometragem';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['km_averiguada', 'dt_averiguacao', 'veiculo_id'], 'required'],
            [['km_averiguada', 'veiculo_id'], 'integer'],
            [['dt_averiguacao', 'dt_recebimento'], 'safe']
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
            'km_averiguada' => 'Km Averiguada',
            'dt_averiguacao' => 'Dt Averiguacao',
            'dt_recebimento' => 'Dt Recebimento',
            'veiculo_id' => 'Veiculo ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getVeiculo()
    {
        return $this->hasOne(Veiculo::className(), ['id' => 'veiculo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public static function find()
    {
        return new AveriguacaoQuilometragemQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da AveriguacaoQuilometragem, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class AveriguacaoQuilometragemQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['averiguacao_quilometragem.nome' => $sort_type]);
    }
}
