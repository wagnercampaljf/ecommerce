<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "filial_transportadora".
 *
 * @property integer $filial_id
 * @property integer $transportadora_id
 * @property integer $dias_postagem
 *
 * @property Filial $filial
 * @property Transportadora $transportadora
 *
 * @author Igor 29/10/2015
 */
class FilialTransportadora extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Igor 29/10/2015
     */
    public static function tableName()
    {
        return 'filial_transportadora';
    }

    /**
     * @inheritdoc
     * @author Igor 29/10/2015
     */
    public function rules()
    {
        return [
            [['filial_id', 'transportadora_id'], 'required'],
            [['dias_postagem', 'filial_id', 'transportadora_id'], 'integer'],
            [['dias_postagem'], 'default', 'value' => 1],
        ];
    }

    /**
     * @inheritdoc
     * @author Igor 29/10/2015
     */
    public function attributeLabels()
    {
        return [
            'filial_id' => Yii::t('app', 'Filial ID'),
            'transportadora_id' => Yii::t('app', 'Transportadora ID'),
            'dias_postagem' => Yii::t('app', 'Dias Postagem'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 29/10/2015
     */
    public function getFilial()
    {
        return $this->hasOne(Filial::className(), ['id' => 'filial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 29/10/2015
     */
    public function getTransportadora()
    {
        return $this->hasOne(Transportadora::className(), ['id' => 'transportadora_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 29/10/2015
     */
    public static function find()
    {
        return new FilialTransportadoraQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da FilialTransportadora, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Igor 29/10/2015
 */
class FilialTransportadoraQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Igor 29/10/2015
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['filial_transportadora.nome' => $sort_type]);
    }
}
