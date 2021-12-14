<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "banner_categoria_banner".
 *
 * @property integer $categoria_banner_id
 * @property integer $banner_id
 *
 * @property Banner $banner
 * @property CategoriaBanner $categoriaBanner
 *
 * @author Otavio 20/04/2016
 */
class BannerCategoriaBanner extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Otavio 20/04/2016
     */
    public static function tableName()
    {
        return 'banner_categoria_banner';
    }

    /**
     * @inheritdoc
     * @author Otavio 20/04/2016
     */
    public function rules()
    {
        return [
            [['categoria_banner_id', 'banner_id'], 'required'],
            [['categoria_banner_id', 'banner_id'], 'integer'],
            [['banner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Banner::className(), 'targetAttribute' => ['banner_id' => 'id']],
            [['categoria_banner_id'], 'exist', 'skipOnError' => true, 'targetClass' => CategoriaBanner::className(), 'targetAttribute' => ['categoria_banner_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     * @author Otavio 20/04/2016
     */
    public function attributeLabels()
    {
        return [
            'categoria_banner_id' => 'Categoria Banner ID',
            'banner_id' => 'Banner ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Otavio 20/04/2016
     */
    public function getBanner()
    {
        return $this->hasOne(Banner::className(), ['id' => 'banner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Otavio 20/04/2016
     */
    public function getCategoriaBanner()
    {
        return $this->hasOne(CategoriaBanner::className(), ['id' => 'categoria_banner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Otavio 20/04/2016
     */
    public static function find()
    {
        return new BannerCategoriaBannerQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da BannerCategoriaBanner, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Otavio 20/04/2016
 */
class BannerCategoriaBannerQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Otavio 20/04/2016
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['banner_categoria_banner.nome' => $sort_type]);
    }
}
