<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "transportadora".
 *
 * @property integer $id
 * @property string $nome
 *
 * @property ServicoTransportadora[] $servicoTransportadoras
 *
 * @author Vinicius Schettino 02/12/2014
 */
class Transportadora extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'transportadora';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['nome'], 'string', 'max' => 255]
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
            'nome' => 'Nome',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getServicoTransportadoras()
    {
        return $this->hasMany(ServicoTransportadora::className(), ['transportadora_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author smart_i9 30/10/2015
     */
    public function getFilialTransportadoras()
    {
        return $this->hasMany(FilialTransportadora::className(), ['transportadora_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author smart_i9 30/10/2015
     */
    public function getFilials()
    {
        return $this->hasMany(Filial::className(), ['id' => 'filial_id'])->viaTable('filial_transportadora',
            ['transportadora_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public static function find()
    {
        return new TransportadoraQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Transportadora, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class TransportadoraQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['transportadora.nome' => $sort_type]);
    }
}
