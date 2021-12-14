<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "filial_servico_transportadora".
 *
 * @property integer $filial_id
 * @property integer $servico_transportadora_id
 *
 * @property Filial $filial
 * @property ServicoTransportadora $servicoTransportadora
 *
 * @author Vinicius Schettino 02/12/2014
 */
class FilialServicoTransportadora extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'filial_servico_transportadora';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['filial_id', 'servico_transportadora_id'], 'required'],
            [['filial_id', 'servico_transportadora_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function attributeLabels()
    {
        return [
            'filial_id' => 'Filial ID',
            'servico_transportadora_id' => 'Servico Transportadora ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getFilial()
    {
        return $this->hasOne(Filial::className(), ['id' => 'filial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function getServicoTransportadora()
    {
        return $this->hasOne(ServicoTransportadora::className(), ['id' => 'servico_transportadora_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public static function find()
    {
        return new FilialServicoTransportadoraQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da FilialServicoTransportadora, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
*/
class FilialServicoTransportadoraQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
    */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['filial_servico_transportadora.nome' => $sort_type]);
    }
}
