<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "banner_subcategoria".
 *
 * @property integer $banner_id
 * @property integer $subcategoria_id
 *
 * @property Subcategoria $subcategoria
 * @property Banner $banner
 *
 * @author Igor 06/10/2015
 */
class BannerSubcategoria extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Igor 06/10/2015
     */
    public static function tableName()
    {
        return 'banner_subcategoria';
    }

    /**
     * @inheritdoc
     * @author Igor 06/10/2015
     */
    public function rules()
    {
        return [
            [['banner_id', 'subcategoria_id'], 'required'],
            [['banner_id', 'subcategoria_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     * @author Igor 06/10/2015
     */
    public function attributeLabels()
    {
        return [
            'banner_id' => Yii::t('app', 'Banner ID'),
            'subcategoria_id' => Yii::t('app', 'Subcategoria ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 06/10/2015
     */
    public function getSubcategoria()
    {
        return $this->hasOne(Subcategoria::className(), ['id' => 'subcategoria_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 06/10/2015
     */
    public function getBanner()
    {
        return $this->hasOne(Banner::className(), ['id' => 'banner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Igor 06/10/2015
     */
    public static function find()
    {
        return new BannerSubcategoriaQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da BannerSubcategoria, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Igor 06/10/2015
 */
class BannerSubcategoriaQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Igor 06/10/2015
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['banner_subcategoria.nome' => $sort_type]);
    }
}
