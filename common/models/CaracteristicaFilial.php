<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "caracteristica_filial".
 *
 * @property integer $caracteristica_id
 * @property integer $filial_id
 * @property string $observacao
 *
 * @property Caracteristica $caracteristica
 * @property Filial $filial
 *
 * @author Igor 27/10/2015
 */
class CaracteristicaFilial extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Igor 27/10/2015
     */
    public static function tableName()
    {
        return 'caracteristica_filial';
    }

    /**
     * @inheritdoc
     * @author Igor 27/10/2015
     */
    public function rules()
    {
        return [
            [['caracteristica_id', 'filial_id'], 'required'],
            [['caracteristica_id', 'filial_id'], 'integer'],
            [['observacao'], 'string']
        ];
    }

    /**
     * @inheritdoc
     * @author Igor 27/10/2015
     */
    public function attributeLabels()
    {
        return [
            'caracteristica_id' => Yii::t('app', 'Caracteristica ID'),
            'filial_id' => Yii::t('app', 'Filial ID'),
            'observacao' => Yii::t('app', 'Observacao'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 27/10/2015
     */
    public function getCaracteristica()
    {
        return $this->hasOne(Caracteristica::className(), ['id' => 'caracteristica_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 27/10/2015
     */
    public function getFilial()
    {
        return $this->hasOne(Filial::className(), ['id' => 'filial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 27/10/2015
     */
    public static function find()
    {
        return new CaracteristicaFilialQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da CaracteristicaFilial, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Igor 27/10/2015
 */
class CaracteristicaFilialQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Igor 27/10/2015
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['caracteristica_filial.nome' => $sort_type]);
    }

    public function byProduto($produto_id)
    {
        return $this->joinWith(['filial.produtoFilials'])->andFilterWhere(['produto_filial.produto_id' => $produto_id]);
    }
    
    public function ativo($ativo = true)
    {
        return $this->joinWith(['filial.lojista'])->andWhere(['lojista.ativo' => $ativo]);
    }
}
