<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "processamento".
 *
 * @property integer $id
 * @property integer $funcao_id
 * @property string $data_hora_inicial
 * @property string $data_hora_final
 * @property string $observacao
 * @property string $status
 *
 * @property Funcao $funcao
 *
 * @author Unknown 08/07/2021
 */
class Processamento extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Unknown 08/07/2021
     */
    public static function tableName()
    {
        return 'processamento';
    }

    /**
     * @inheritdoc
     * @author Unknown 08/07/2021
     */
    public function rules()
    {
        return [
            [['funcao_id'], 'required'],
            [['funcao_id'], 'default', 'value' => null],
            [['funcao_id'], 'integer'],
            [['data_hora_inicial', 'data_hora_final'], 'safe'],
            [['observacao', 'status'], 'string'],
            [['funcao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Funcao::className(), 'targetAttribute' => ['funcao_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Unknown 08/07/2021
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'funcao_id' => 'Função',
            'data_hora_inicial' => 'Data Hora Inicial',
            'data_hora_final' => 'Data Hora Final',
            'observacao' => 'Observacao',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 08/07/2021
    */
    public function getFuncao()
    {
        return $this->hasOne(Funcao::className(), ['id' => 'funcao_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Unknown 08/07/2021
    */
    public static function find()
    {
        return new ProcessamentoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Processamento, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Unknown 08/07/2021
*/
class ProcessamentoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Unknown 08/07/2021
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['processamento.nome' => $sort_type]);
    }
}
