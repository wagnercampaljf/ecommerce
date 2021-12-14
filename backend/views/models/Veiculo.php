<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "veiculo".
 *
 * @property integer $id
 * @property string $placa
 * @property string $chassi
 * @property string $dt_aquisicao
 * @property string $observacao
 * @property integer $km_inicial
 * @property string $ano_modelo_id
 * @property integer $empresa_id
 *
 * @property AveriguacaoQuilometragem[] $averiguacaoQuilometragems
 * @property AnoModelo $anoModelo
 * @property Empresa $empresa
 *
 * @author Vinicius Schettino 02/12/2014
 */
class Veiculo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'veiculo';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['dt_aquisicao'], 'safe'],
            [['km_inicial', 'empresa_id'], 'integer'],
            [['ano_modelo_id', 'empresa_id'], 'required'],
            [['placa'], 'string', 'max' => 7],
            [['chassi'], 'string', 'max' => 100],
            [['observacao'], 'string', 'max' => 255],
            [['ano_modelo_id'], 'string', 'max' => 20]
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
            'placa' => 'Placa',
            'chassi' => 'Chassi',
            'dt_aquisicao' => 'Dt Aquisicao',
            'observacao' => 'Observacao',
            'km_inicial' => 'Km Inicial',
            'ano_modelo_id' => 'Ano Modelo ID',
            'empresa_id' => 'Empresa ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getAveriguacaoQuilometragems()
    {
        return $this->hasMany(AveriguacaoQuilometragem::className(), ['veiculo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getAnoModelo()
    {
        return $this->hasOne(AnoModelo::className(), ['id' => 'ano_modelo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getEmpresa()
    {
        return $this->hasOne(Empresa::className(), ['id' => 'empresa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public static function find()
    {
        return new VeiculoQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Veiculo, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class VeiculoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['veiculo.nome' => $sort_type]);
    }
}
