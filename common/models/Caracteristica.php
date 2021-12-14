<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "caracteristica".
 *
 * @property integer $id
 * @property string $nome
 * @property string $badge
 *
 * @property CaracteristicaFilial[] $caracteristicaFilials
 * @property Filial[] $filials
 *
 * @author Igor 27/10/2015
 */
class Caracteristica extends \yii\db\ActiveRecord implements SearchModel
{
    const ENTREGA_PROPRIA = 6;

    /**
     * @inheritdoc
     * @author Igor 27/10/2015
     */
    public static function tableName()
    {
        return 'caracteristica';
    }

    /**
     * @inheritdoc
     * @author Igor 27/10/2015
     */
    public function rules()
    {
        return [
            [['nome', 'badge', 'classe'], 'required'],
            [['nome', 'badge', 'classe'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     * @author Igor 27/10/2015
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nome' => Yii::t('app', 'Nome'),
            'badge' => Yii::t('app', 'Badge'),
            'classe' => Yii::t('app', 'Classe'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 27/10/2015
     */
    public function getCaracteristicaFilials()
    {
        return $this->hasMany(CaracteristicaFilial::className(), ['caracteristica_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 27/10/2015
     */
    public function getFilials()
    {
        return $this->hasMany(Filial::className(), ['id' => 'filial_id'])->viaTable('caracteristica_filial',
            ['caracteristica_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 27/10/2015
     */
    public static function find()
    {
        return new CaracteristicaQuery(get_called_class());
    }

    /**
     * @return string
     */
    public function getLabelSearch()
    {
        return $this->nome;
    }
}

/**
 * Classe para contenção de escopos da Caracteristica, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Igor 27/10/2015
 */
class CaracteristicaQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Igor 27/10/2015
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['caracteristica.nome' => $sort_type]);
    }

    public function byProduto($produto_id)
    {
        return $this->joinWith(['filials.produtoFilials'])->andFilterWhere(['produto_filial.produto_id' => $produto_id]);
    }
}
