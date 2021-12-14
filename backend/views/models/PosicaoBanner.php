<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "posicao_banner".
 *
 * @property integer $id
 * @property string $nome
 * @property integer $altura
 * @property integer $largura
 * @property string $class
 *
 * @property Banner[] $banners
 *
 * @author Igor 13/10/2015
 */
class PosicaoBanner extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Igor 13/10/2015
     */
    public static function tableName()
    {
        return 'posicao_banner';
    }

    /**
     * @inheritdoc
     * @author Igor 13/10/2015
     */
    public function rules()
    {
        return [
            [['nome', 'altura', 'largura', 'class'], 'required'],
            [['altura', 'largura'], 'integer'],
            [['nome', 'class'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     * @author Igor 13/10/2015
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nome' => Yii::t('app', 'Nome'),
            'altura' => Yii::t('app', 'Altura'),
            'largura' => Yii::t('app', 'Largura'),
            'class' => Yii::t('app', 'Class'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 13/10/2015
     */
    public function getBanners()
    {
        return $this->hasMany(Banner::className(), ['posicao_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 13/10/2015
     */
    public static function find()
    {
        return new PosicaoBannerQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da PosicaoBanner, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Igor 13/10/2015
 */
class PosicaoBannerQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Igor 13/10/2015
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['posicao_banner.nome' => $sort_type]);
    }
}
